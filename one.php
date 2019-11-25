<?php 
if( php_sapi_name() !== "cli" ){
   die( "NoAccess" );
}
require 'init.php';
ini_set('memory_limit', '128M');

class one{
	static function cache_clear(){
		cache::clear();
	}

	static function cache_refresh(){
		oneindex::refresh_cache(get_absolute_path(config('onedrive_root')));
	}

	static function token_refresh(){
		$refresh_token = config('refresh_token');
		$token = onedrive::get_token($refresh_token);
		if(!empty($token['refresh_token'])){
			config('@token', $token);
		}
	}

	static function upload_file($localfile, $remotefile=null){
		$localfile = realpath($localfile);
		if(!file_exists($localfile)){
			exit('file not exists');
		}
		print ' 本地文件：'.$localfile.PHP_EOL;

		if(empty($remotefile)){
			$remotepath = pathinfo($localfile, PATHINFO_BASENAME);
		}elseif(substr($remotefile, -1) == '/'){
			$remotepath = get_absolute_path($remotefile);
			$remotepath = substr($remotepath,1).pathinfo($localfile, PATHINFO_BASENAME);
		}else{
			$remotepath = ltrim($remotefile, '/');
		}
		print ' 远程文件：'.$remotepath.PHP_EOL;
		
		$filesize = onedrive::_filesize($localfile) OR die('无法获取文件大小');
		if($filesize < 10485760){
			print ' 上传方式：直接上传'.PHP_EOL;
			$begin_time = microtime(true);
			$result = onedrive::upload($remotepath, file_get_contents($localfile));
			if(!empty($result)){
				$upload_time = microtime(true) - $begin_time;
				print ' 上传成功:'.onedrive::human_filesize($filesize/$upload_time).'/s'.PHP_EOL;
			}else{
				print ' 上传失败!'.PHP_EOL;
			}
		}else{
			print ' 上传方式：分块上传'.PHP_EOL;
			return self::upload_large_file($localfile, $remotepath);
		}
		return;
	}
	
	static function upload_folder($localfolder, $remotefolder='/'){
		$localfolder = realpath($localfolder);
		$remotefolder = get_absolute_path($remotefolder);
		print ' 开始上传文件夹'.PHP_EOL;
		self::folder2upload($localfolder,$remotefolder);
	}

	static function folder2upload($localfolder, $remotefolder){
		$files = scandir($localfolder);
		foreach ($files as $file) {
			if ($file == '.' || $file == '..') {
				continue;
			}
			if (is_dir($localfolder . '/' . $file)) {
		        self::folder2upload($localfolder . '/' . $file, $remotefolder.$file.'/');
		    }else{
			    $localfile = realpath($localfolder . '/' . $file);
			    $remotefile = $remotefolder.$file;
			    self::upload_file($localfile, $remotefile);
		    }
		}
	}

	//static function add2uploading($path,$remotefolder) {
	//  $files = scandir($path);
	//  foreach ($files as $file) {
	//    if ($file != '.' && $file != '..') {
	//      if (is_dir($path . '/' . $file)) {
	//        self::add2uploading($path . '/' . $file, $remotefolder.$file.'/');
	//      } else {
	//	    $localfile = realpath($path . '/' . $file);
	//	    echo $localfile.PHP_EOL;
	//	    $remotepath = $remotefolder.$file;
	//        $task = array(
	//			'localfile'=>$localfile,
	//			'remotepath' => $remotepath,
	//			'filesize'=>onedrive::_filesize($localfile),
	//			'update_time'=>0
	//        );
	//        $uploads = config('@upload');
	//        if(empty($uploads[$remotepath])){
	//	        $uploads[$remotepath] = $task;
	//	        config('@upload', $uploads);
	//        }
	//      }
	//    }
	//  }
	//}


	static function upload_large_file($localfile, $remotepath){
		fetch::init([CURLOPT_TIMEOUT=>200]);
		$upload = config('@upload');
		$info = $upload[$remotepath];
		if(empty($info['url'])){
			print ' 创建上传会话'.PHP_EOL;
			$data = onedrive::create_upload_session($remotepath);
			if(!empty($data['uploadUrl'])){
				$info['url'] = $data['uploadUrl'];
				$info['localfile'] = $localfile;
				$info['remotepath'] = $remotepath;
				$info['filesize'] = onedrive::_filesize($localfile);
				$info['offset'] = 0;
				$info['length'] = 327680;
				$info['update_time'] = time();
				$upload[$remotepath] = $info;
				config('@upload', $upload);
			}elseif ( $data === false ){
				print ' 文件已存在!'.PHP_EOL;
				return;
			}
		}
		
		if(empty($info['url'])){
			print ' 获取会话失败！'.PHP_EOL;
			sleep(3);
			return self::upload_large_file($localfile, $remotepath);
		}
		
		print ' 上传分块'.onedrive::human_filesize($info['length']).'	';
		$begin_time = microtime(true);
		$data = onedrive::upload_session($info['url'], $info['localfile'], $info['offset'], $info['length']);

		if(!empty($data['nextExpectedRanges'])){
			$upload_time = microtime(true) - $begin_time;
			$info['speed'] = $info['length']/$upload_time;
			
			print onedrive::human_filesize($info['speed']).'/s'.'	'.round(($info['offset']/$info['filesize'])*100).'%	'.PHP_EOL;
			$info['length'] = intval($info['length']/$upload_time/32768*2)*327680;
			$info['length'] = ($info['length']>104857600)?104857600:$info['length'];
			
			list($offset, $filesize) = explode('-',$data['nextExpectedRanges'][0]);
			$info['offset'] = $offset;
			$info['update_time'] = time();
			$upload[$remotepath] = $info;
			config('@upload', $upload);
		}elseif(!empty($data['@content.downloadUrl']) || !empty($data['id'])){
			unset($upload[$remotepath]);
			config('@upload', $upload);
			print ' 上传完成！'.PHP_EOL;
			return;
		}else{
			print ' 失败!'.PHP_EOL;
			$data = onedrive::upload_session_status($info['url']);
			if(empty($data)|| $info['length']<100){
				onedrive::delete_upload_session($info['url']);
				unset($upload[$remotepath]);
				config('@upload', $upload);
			}elseif(!empty($data['nextExpectedRanges'])){
				list($offset, $filesize) = explode('-',$data['nextExpectedRanges'][0]);
				$info['offset'] = $offset;
				$info['length'] = $info['length']/1.5;
				$upload[$remotepath] = $info;
				config('@upload', $upload);
			}
		}

		return self::upload_large_file($localfile, $remotepath);
		
	}

	
}


array_shift($argv);
$action = str_replace(':', '_',array_shift($argv));

if(is_callable(['one',$action])){
	@call_user_func_array(['one',$action], $argv);
	exit();
}
?>
oneindex commands :
 cache
  cache:clear    	clear cache
  cache:refresh  	refresh cache
 token
  token:refresh  	refresh token
 upload
  upload:file  		upload a file to onedrive
  upload:folder  	upload a folder to onedrive
