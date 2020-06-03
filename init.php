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
 
 //////////////////////
 
 if ($_GET["site"])
 {
     echo "查找站点";
     echo $_GET["site"];
    
    $token['access_token'];
     
     $request['headers'] = "Authorization: bearer {$配置文件['access_token']}".PHP_EOL.'Content-Type: application/json'.PHP_EOL;
        $request['url'] = 'https://microsoftgraph.chinacloudapi.cn/v1.0/sites/root';
        $resp = fetch::get($request);
        $data = json_decode($resp->content, true);
        $hostname = $data['siteCollection']['hostname'];

        $getsiteid = $配置文件["api_url"].'/sites/'.$hostname.':'.$_REQUEST['site'];
        $request['url'] = $getsiteid;
        $respp = fetch::get($request);
        $datass = json_decode($respp->content, true);

   $siteidurl=  $datass["id"];
     if ($siteidurl==""){
         echo   "获取失败重写获取";
       echo '<form action="/'.$驱动器.'/ "  method="get">
 　　<input type="text" name="site" value ="/sites/名称" />
 　　<input type="submit" value="站点id" />
 </form>';
         exit;
     }
     echo $api=$配置文件["api_url"].'/sites/'.$siteidurl.'/drive/root';
     
     config("api@".$驱动器,$api);
     
     echo "配置sharepoint成功";
     
      echo '<a href="/'.$驱动器.'">授权成功</a>';
     
     
     
     
     
     
     
     
     
     
     
     
     
     exit;
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
   
   
   
   /////////////////////
 echo   "是否启用Sharepoint";
       echo '<form action="/'.$驱动器.'/ "  method="get">
 　　<input type="text" name="site" value ="/sites/名称" />
 　　<input type="submit" value="站点id" />
 </form>';
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
  exit;
   
   }else{echo "授权失败";}
   
   
   
   





    }else //生成授权地址
    {
  
if ($_SERVER["REQUEST_URI"]=="/admin?/logout" 
|$_SERVER["REQUEST_URI"]=="/admin" 
|$_SERVER["REQUEST_URI"]=="/?/admin" 
|$_SERVER["REQUEST_URI"]=="/?/login" 
| $_SERVER["REQUEST_URI"]=="/admin?/logout"
| $_SERVER["REQUEST_URI"]=="/?/admin/setpass" 
| $_SERVER["REQUEST_URI"]=="/?/admin/show"
| $_SERVER["REQUEST_URI"]=="/?/admin/upload"
| $_SERVER["REQUEST_URI"]=="/?/admin/cache"
| $_SERVER["REQUEST_URI"]=="/?/admin/sharepoint"
| $_SERVER["REQUEST_URI"]=="/?/admin/images"
| $_SERVER["REQUEST_URI"]=="/admin/file"
){
   
    return ;
}

  

if($配置文件["oauth_url"]==""){
  	http_response_code(404);
		view::load('404')->show();
		die();
};


        $oauthurl=$配置文件["oauth_url"];
        $client_id=$配置文件["client_id"];
        if ($_SERVER["REQUEST_URI"]=="/"){
        $_SERVER["REQUEST_URI"]="/default";
        }
        $redirect_uri=urlencode("http://" .$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]);
        $授权地址= $oauthurl."/authorize?client_id=".$client_id."&scope=offline_access+files.readwrite.all+Sites.ReadWrite.All&response_type=code&redirect_uri=https://coding.mxin.ltd&state=".$redirect_uri;
        echo '<a href="'.$授权地址.'">授权应用</a>';




exit;





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





function splitlast($str, $split)
{
    $len = strlen($split);
    $pos = strrpos($str, $split);
    if ($pos===false) {
        $tmp[0] = $str;
        $tmp[1] = '';
    } elseif ($pos>0) {
        $tmp[0] = substr($str, 0, $pos);
        $tmp[1] = substr($str, $pos+$len);
    } else {
        $tmp[0] = '';
        $tmp[1] = substr($str, $len);
    }
    return $tmp;
}




   


