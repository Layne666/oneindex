<?php 
define('VIEW_PATH', ROOT.'view/admin/');
class UploadController{

	function index(){
		if($_POST['upload'] == 1){
			$local = realpath($_POST['local']);
			$remotepath = get_absolute_path($_POST['remote']);
			if(is_file($local)){
				$this->add_task($local, $remotepath);
				$message = "文件<kbd>".$local."</kbd>已添加到队列";
			}elseif(is_dir($local)){
				$this->scan_dir($local, $remotepath);
				$message = "文件夹<kbd>".$local."</kbd>已添加到队列";
			}elseif($local == realpath('.')){
				$message = "因为安全原因，程序文件夹根目录不能上传";
			}else{
				$message = "文件不存在";
			}
			$request = $this->task_request();
			$request['url'] = substr($request['url'],0,-4).'run';
			fetch::post($request);
		}elseif(!empty($_POST['begin_task'])){
			$this->task($_POST['begin_task']);
		}elseif(!empty($_POST['delete_task'])){
			unset($_POST['delete_task']);
			config('@upload', (array)$uploads);
		}elseif(!empty($_POST['empty_uploaded'])){
			config('@uploaded', array());
		}
		$uploading = config('@upload');
		$uploaded = array_reverse((array)config('@uploaded'));
		return view::load('upload')->with('uploading', $uploading)->with('uploaded', $uploaded)->with('message', $message);
	}

	//扫描文件夹，添加到任务队列
	private function scan_dir($localpath, $remotepath){
		$files = scandir($localpath);
		foreach ($files as $file) {
			if ($file == '.' || $file == '..') {
				continue;
			}
			if (is_dir($localpath . '/' . $file)) {
		        $this->scan_dir($localpath . '/' . $file, $remotepath.$file.'/');
		    }else{
			    $localfile = realpath($localpath . '/' . $file);
			    $remotefile = $remotepath.$file;
			    $this->add_task($localfile, $remotefile);
		    }
		}
	}

	private function add_task($localfile, $remotefile){
	    $task = array(
			'localfile'=>$localfile,
			'remotepath' => $remotefile,
			'filesize'=>onedrive::_filesize($localfile),
			'upload_type'=>'web',
			'update_time'=>0,
	    );

	    $uploads = (array)config('@upload');
	    if(empty($uploads[$remotefile])){
		    $uploads[$remotefile] = $task;
		    config('@upload', $uploads);
	    }
	}

	//运行队列中的任务
	function run(){
		$uploads = (array)config('@upload');
		$time = time();
		$runing = 0;
		foreach($uploads as $task){
			if($time < ($task['update_time']+60) AND $task['type']=='web' ){
				$runing = $runing +1;
			}
			if($runing > 5)break;
		}
		
		foreach($uploads as $remotepath=>$task){
			if($time < ($task['update_time']+60) OR !is_array($task) ){
				continue;
			}
			$runing = $runing +1;
			print $remotepath.PHP_EOL;
			fetch::post($this->task_request($remotepath));
			if($runing > 5)break;
		}

		if(count($uploads) > 5){
			set_time_limit(100);
			sleep(60);
			$request = $this->task_request();
			$request['url'] = substr($request['url'],0,-4).'run';
			fetch::get($request);
		}
	}

	private function task_request($remotepath=''){
		$request['headers'] = "Cookie: admin=".md5(config('password').config('refresh_token')).PHP_EOL;
		$request['headers'] .= "Host: ".$_SERVER['HTTP_HOST'];
		$request['curl_opt']=[CURLOPT_CONNECTTIMEOUT => 1,CURLOPT_TIMEOUT=>1,CURLOPT_FOLLOWLOCATION=>true];
		$http_type = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://';
		$request['url'] = $http_type.'127.0.0.1'.get_absolute_path(dirname($_SERVER['PHP_SELF'])).'?/admin/upload/task';
		$request['post_data'] = 'remotepath='.urlencode($remotepath);
		return $request;
	}
	
	//执行任务
	function task($remotepath=null){
		$remotepath = is_null($remotepath)?$_POST['remotepath']:$remotepath;
		//file_put_contents('log.txt',$remotepath.PHP_EOL, FILE_APPEND);
		$uploads = config('@upload');
		$task = $uploads[$remotepath];

		if(empty($task)){
			return;
		}
		if($task['filesize'] < 10485760){
			@onedrive::upload($task['remotepath'], file_get_contents($task['localfile']));
			unset($uploads[$remotepath]);
				
			config('@upload', (array)$uploads);
			config($remotepath.'@uploaded','success');
		}else{
			$uploads[$remotepath]['update_time'] = time();
			config('@upload', (array)$uploads);
			$this->upload_large_file($task);
		}
	}

	function upload_large_file($task){

		
		//创建上传会话
		if(empty($task['url'])){
			$data = onedrive::create_upload_session($task['remotepath']);
			if(!empty($data['uploadUrl'])){
				$task['url'] = $data['uploadUrl'];
				$task['offset'] = 0;
				$task['length'] = 327680;
				$task['update_time'] = time();
				config($task['remotepath'].'@upload',$task);
			}elseif ( $data === false ){
				$uploads = config('@upload');
				unset($uploads[$task['remotepath']]);
				config('@upload', $uploads);
				config($task['remotepath'].'@uploaded','exists');
			}
		}else{
			$begin_time = microtime(true);
			set_time_limit(0);
			$data = onedrive::upload_session($task['url'], $task['localfile'], $task['offset'], $task['length']);
			if(!empty($data['nextExpectedRanges'])){ 
			//继续上传
				$upload_time = microtime(true) - $begin_time;
				$task['speed'] = $task['length']/$upload_time;
				$task['length'] = intval($task['length']/$upload_time/32768*2)*327680;
				$task['length'] = ($task['length']>104857600)?104857600:$task['length'];
				list($offset, $filesize) = explode('-',$data['nextExpectedRanges'][0]);
				$task['offset'] = intval($offset);
				$info['update_time'] = time();
				config($task['remotepath'].'@upload',$task);
			}elseif(!empty($data['@content.downloadUrl']) || !empty($data['id'])){ 
			//上传完成
				unset($uploads[$task['remotepath']]);
				config('@upload', $uploads);
				config($task['remotepath'].'@uploaded','success');
				return;
			}else{
			//失败，重新获取信息
				echo "re get url";
				$data = onedrive::upload_session_status($task['url']);
				if(empty($data)|| $info['length']<100){
					onedrive::delete_upload_session($task['url']);
					unset($task['url']);
					config($task['remotepath'].'@upload', $task);
				}elseif(!empty($data['nextExpectedRanges'])){
					list($offset, $filesize) = explode('-',$data['nextExpectedRanges'][0]);
					$task['offset'] = intval($offset);
					$task['length'] = $task['length']/1.5;
					config($task['remotepath'].'@upload', $task);
				}
			}
		}
		$request= $this->task_request($task['remotepath']);
		$resp = fetch::post($request);
		//var_dump($resp);
	}

	
}
