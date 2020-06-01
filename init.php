<?php
$stime=microtime(true); 

error_reporting(E_ALL & ~E_NOTICE);
date_default_timezone_set('PRC');
define('TIME', time());
!defined('ROOT') && define('ROOT', str_replace("\\", "/", dirname(__FILE__)) . '/');

//__autoload方法
function i_autoload($className) {
	if (is_int(strripos($className, '..'))) {
		return;
	}
	$file = ROOT . 'lib/' . $className . '.php';
	if (file_exists($file)) {
		include $file;
	}
}
spl_autoload_register('i_autoload');

!defined('FILE_FLAGS') && define('FILE_FLAGS', LOCK_EX);
/**
 * config('name');
 * config('name@file');
 * config('@file');
 */
if (!function_exists('config')) {
	!defined('CONFIG_PATH') && define('CONFIG_PATH', ROOT . 'config/');
	function config($key) {
		static $configs = array();
		list($key, $file) = explode('@', $key, 2);
		$file = empty($file) ? 'base' : $file;

		$file_name = CONFIG_PATH . $file . '.php';
		//读取配置
		if (empty($configs[$file]) AND file_exists($file_name)) {
			$configs[$file] = @include $file_name;
		}

		if (func_num_args() === 2) {
			$value = func_get_arg(1);
			//写入配置
			if (!empty($key)) {
				$configs[$file] = (array) $configs[$file];
				if (is_null($value)) {
					unset($configs[$file][$key]);
				} else {
					$configs[$file][$key] = $value;
				}

			} else {
				if (is_null($value)) {
					return unlink($file_name);
				} else {
					$configs[$file] = $value;
				}

			}
			file_put_contents($file_name, "<?php return " . var_export($configs[$file], true) . ";", FILE_FLAGS);
		} else {
			//返回结果
			if (!empty($key)) {
				return $configs[$file][$key];
			}

			return $configs[$file];
		}
	}
}
///////////////////////////////////////////
function access_token($配置文件,$驱动器){
 
  $token = $配置文件;
  ///////////////未配置////////////////
 
if ($_SERVER["REQUEST_URI"]=="/?/admin/"){
   
    return ;
}
 
if ($_SERVER["REQUEST_URI"]=="/?/login"){
   
    return ;
}
 
 
 ///////////////////未授权////////////////
  if($token ["refresh_token"]=="")//未授权
{
    if($_GET["code"])//通过code获取授权
    {  $code= $_GET["code"];
        $驱动器=str_replace("?code=".$code,"",$驱动器);
       $配置文件=config("@".$驱动器);
    
             $client_id = $配置文件["client_id"];
            $client_secret = $配置文件["client_secret"];
            $redirect_uri = $配置文件["redirect_uri"];
   
            $授权url =  $配置文件["oauth_url"]."/token";
   
   
             $curl = curl_init();
   
             curl_setopt_array($curl, array(
              CURLOPT_URL => $授权url ,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "code=".$_GET["code"]."&grant_type=authorization_code&client_id=".$client_id."&client_secret=".$client_secret."&redirect_uri=https%3A//coding.mxin.ltd",
            CURLOPT_HTTPHEADER => array(
            "SdkVersion: postman-graph/v1.0",
            "client_secret:" .$client_secret,
            "code: ".$_GET["code"],
            "redirect_uri: https://coding.mxin.ltd",
            "Content-Type: application/x-www-form-urlencoded",
            "grant_type: authorization_code",
   
   
   ),
   ));
   
   $response = curl_exec($curl);
   
   curl_close($curl);
   $response=json_decode($response,true);
   $response;
   if(!empty($response["refresh_token"])){
   config("refresh_token@".$驱动器,$response["refresh_token"]);
   config("access_token@".$驱动器,$response["access_token"]);
 
   
   $地址=str_replace("?code=".$code,"",$_SERVER["REQUEST_URI"]);
   
   echo '<a href="'.$地址.'">授权成功</a>';
  exit;
   
   }else{echo "授权失败";}
   
   
   
   





    }else //生成授权地址
    {





        $oauthurl=$配置文件["oauth_url"];
        $client_id=$配置文件["client_id"];
        if ($_SERVER["REQUEST_URI"]=="/"){
        $_SERVER["REQUEST_URI"]="/default";
        }
        $redirect_uri=urlencode("http://" .$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]);
        $授权地址= $oauthurl."/authorize?client_id=".$client_id."&scope=offline_access+files.readwrite.all+Sites.ReadWrite.All&response_type=code&redirect_uri=https://coding.mxin.ltd&state=".$redirect_uri;
        echo '<a href="'.$授权地址.'">授权应用</a>';










    }
    
    
    


}

 ///////////////////已经授权////////////////
if($token ["refresh_token"]!=="")//已经授权
{
    if($token['expires_on'] > time()+600){
      
        return $token['access_token'];
       
    }else{
        $refresh_token = $token['refresh_token'];
        $newtoken =get_token($配置文件);

             if(!empty($newtoken['refresh_token'])){
                 $配置文件["expires_on"] = time()+ $newtoken['expires_in'];
            	$配置文件["access_token"]=$newtoken["access_token"];
        
                config('@'.$驱动器, $配置文件);
            
          require(ROOT."del.php");
              return $token['access_token'];
              }else{

            echo "获取accesstoken失败";
              }



    }





}

}



function get_token($配置文件=array()){
		 $oauth_url=$配置文件["oauth_url"];
		 $client_id=$配置文件["client_id"];
		 $redirect_uri=$配置文件["redirect_uri"];
		$client_secret=$配置文件["client_secret"];
		$refresh_token=$配置文件["refresh_token"];

		 	$request['url'] = $oauth_url."/token";
 	 	$request['post_data']  = "client_id={$client_id}&redirect_uri={$redirect_uri}&client_secret={$client_secret}&refresh_token={$refresh_token}&grant_type=refresh_token";
 	  
			$request['headers']= "Content-Type: application/x-www-form-urlencoded";
			$resp = fetch::post($request);
	if($resp->http_code=="200"){
	   	$data = json_decode($resp->content, true);

			return $data;
	}
	else{
	    //echo $resp->http_code."错误";exit;
	}
		
		}
    


//////////////////////////////////////




if (!function_exists('db')) {
	function db($table) {
		return db::table($table);
	}
}

if (!function_exists('view')) {
	function view($file, $set = null) {
		return view::load($file, $set = null);
	}
}

if (!function_exists('_')) {
	function _($str) {
		return htmlspecialchars($str);
	}
}

if (!function_exists('e')) {
	function e($str) {
		echo $str;
	}
}

function get_absolute_path($path) {
    $path = str_replace(array('/', '\\', '//'), '/', $path);
    $parts = array_filter(explode('/', $path), 'strlen');
    $absolutes = array();
    foreach ($parts as $part) {
        if ('.' == $part) continue;
        if ('..' == $part) {
            array_pop($absolutes);
        } else {
            $absolutes[] = $part;
        }
    }
    return str_replace('//','/','/'.implode('/', $absolutes).'/');
}

!defined('CONTROLLER_PATH') && define('CONTROLLER_PATH', ROOT.'controller/');






$varrr=explode("/",$_SERVER["REQUEST_URI"]);
 $驱动器=$varrr["1"] ;
 if ($驱动器==""){
     $驱动器="default";
 }
 
array_splice($varrr,0, 1);
unset($varrr['0']);

 $请求路径 = implode("/", $varrr);  
 
$请求路径= str_replace("?".$_SERVER["QUERY_STRING"],"",$请求路径);
 $url=$请求路径;


////////////////////////////////////////////////////////////////////////////////


//加载配置文件
 $drivesfile = ROOT.'config/'.$驱动器.'.php';
if (file_exists($drivesfile)) {
    $配置文件 = include $drivesfile;
} else {
    if (!file_exists(ROOT.'config/default.php')) {
  //header('Location: install.php');

    
    }
 
}

 

define('CACHE_PATH', ROOT.'cache/'.$驱动器."");
cache::$type = empty( config('cache_type') )?'secache':config('cache_type');
////////////////////////////////////初始化配置文件start//////////////////////////////////////

define('CACHE_PATH', ROOT.'cache/'.$驱动器."");
cache::$type = empty( config('cache_type') )?'secache':config('cache_type');









   


