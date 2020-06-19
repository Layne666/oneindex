<?php 
if( php_sapi_name() !== "cli" ){
   die( "NoAccess" );
}
require 'init.php';


ini_set('memory_limit', '128M');


class one{
    static  function cac(){
        echo CACHE_PATH;
    }
	static function cache_clear(){
		cache::clear();
	}

	static function cache_refresh($drives="default"){
	    echo "缓存更新";
echo  $drivesfile = ROOT.'config/'.$drives.'.php';
 
if (file_exists($drivesfile)) {
    $配置文件 = include $drivesfile;


  
    
    }
	    
define('CACHE_PATH', ROOT.'cache/'.$drives."/");

echo cache::$type = empty( config('cache_type') )?'secache':config('cache_type');
 echo onedrive::$client_id =  $配置文件["client_id"];
 echo onedrive::$client_secret =$配置文件["client_secret"];
 echo onedrive::$redirect_uri = $配置文件["redirect_uri"];
 echo onedrive::$api_url = $配置文件["api_url"];
 echo onedrive::$oauth_url = $配置文件["oauth_url"];
 echo onedrive::$access_token=access_token($配置文件,$drives);
 
	    onedrive::$typeurl=$配置文件["api"];
	    
	    
	    
	    
	    	    	    
	//oneindex::refresh_cache(get_absolute_path(config('onedrive_root')));
		cache::refresh_cache(get_absolute_path(config('onedrive_root')));
	
        // 清除php文件缓存
        cache::clear_opcache();
	}

	static function token_refresh($drives="default"){
	    	    
$drivesfile = ROOT.'config/'.$drives.'.php';
 

     $配置文件 = include $drivesfile;
    if( $配置文件==""){exit;}





onedrive::$client_id =  $配置文件["client_id"];
onedrive::$client_secret =$配置文件["client_secret"];
onedrive::$redirect_uri = $配置文件["redirect_uri"];
onedrive::$api_url = $配置文件["api_url"];
onedrive::$oauth_url = $配置文件["oauth_url"];
 
  
		$refresh_token = $配置文件['refresh_token'];
		$token = onedrive::get_token($refresh_token);
		
$配置文件["access_token"]=$token["access_token"];
	$配置文件['expires_on'] = time()+ $token['expires_in'];
		config("@".$drives,$配置文件 );
		echo" 刷新成功";
	
	}

	static function upload_file($localfile, $remotefile=null,$drives="default"){
	    
	    
$drivesfile = ROOT.'config/'.$drives.'.php';
 

    $配置文件 = include $drivesfile;
    





onedrive::$client_id =  $配置文件["client_id"];
onedrive::$client_secret =$配置文件["client_secret"];
onedrive::$redirect_uri = $配置文件["redirect_uri"];
onedrive::$api_url = $配置文件["api_url"];
onedrive::$oauth_url = $配置文件["oauth_url"];
 	onedrive::$access_token=access_token($配置文件,$驱动器);
onedrive::$typeurl=$配置文件["api"] ;
	    
	    
	    
	    
	    
	    
	    
	    
		$localfile = realpath($localfile);
		if(!file_exists($localfile)){
			print ' 本地文件不存在';
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
		if($filesize < 10){
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
	    	print ' 创建上传会话'.PHP_EOL;
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





static function ls ($path="/",$drives="default"){
    
       
$drivesfile = ROOT.'config/'.$drives.'.php';
 

    $配置文件 = include $drivesfile;
    





onedrive::$client_id =  $配置文件["client_id"];
onedrive::$client_secret =$配置文件["client_secret"];
onedrive::$redirect_uri = $配置文件["redirect_uri"];
onedrive::$api_url = $配置文件["api_url"];
onedrive::$oauth_url = $配置文件["oauth_url"];
 	onedrive::$access_token=access_token($配置文件,$驱动器);
onedrive::$typeurl=$配置文件["api"] ;
	    
	    
	    
	    
    print "列目录";
    
  $item=onedrive::dir($path);
  foreach ($item as $item)
    {
        
        echo $path."/".$item["name"].$item["id"]."\n";
    }
    
    
    
    
    
    
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
token:refresh  	    参数说明 php one.php  token:refresh 驱动器名称(default)
upload文件上传      非默认盘时候不能省列远程路径
upload:file     	参数说明 php one.php upload:file 本地文件 远程路径 驱动器名称(default)
upload:folder  	    参数说明 php one.php upload:folder  本地文件 远程路径 驱动器名称(default)
