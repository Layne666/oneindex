<?php
require __DIR__.'/init.php';



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
    
}
    if (!file_exists(ROOT.'config/base.php') or !file_exists(ROOT.'config/default.php') ) {
      header('Location: /install.php');

  
    exit;
    }
 


 

define('CACHE_PATH', ROOT.'cache/'.$驱动器."");
cache::$type = empty( config('cache_type') )?'secache':config('cache_type');
////////////////////////////////////初始化配置文件start//////////////////////////////////////

define('CACHE_PATH', ROOT.'cache/'.$驱动器."");
cache::$type = empty( config('cache_type') )?'secache':config('cache_type');



 


onedrive::$client_id =  $配置文件["client_id"];
onedrive::$client_secret =$配置文件["client_secret"];
onedrive::$redirect_uri = $配置文件["redirect_uri"];
onedrive::$api_url = $配置文件["api_url"];
onedrive::$oauth_url = $配置文件["oauth_url"];
 	onedrive::$access_token=access_token($配置文件,$驱动器);
onedrive::$typeurl=$配置文件["api"] ;
	
   
//////////////////debug//////////////////


//////////////////debug//////////////////


/*
 *    系统后台
 */
route::group(function () {
    return $_COOKIE['admin'] == config('password');
}, function () {
    route::get('/logout', 'AdminController@logout');
    route::any('/admin/', 'AdminController@settings');
    route::any('/admin/cache', 'AdminController@cache');
    route::any('/admin/show', 'AdminController@show');
    route::any('/admin/setpass', 'AdminController@setpass');
    route::any('/admin/images', 'AdminController@images');
    route::any('/admin/drives', 'AdminController@drives');
    route::any('/admin/sharepoint', 'AdminController@sharepoint');
    route::any('/admin/upload', 'UploadController@index');
    //守护进程
    route::any('/admin/upload/run', 'UploadController@run');
    //上传进程
    route::post('/admin/upload/task', 'UploadController@task');
});
//登陆
route::any('/login', 'AdminController@login');

//跳转到登陆
route::any('/admin/', function () {
    return view::direct(get_absolute_path(dirname($_SERVER['SCRIPT_NAME'])).'?/login');
});

define('VIEW_PATH', ROOT.'view/'.(config('style') ? config('style') : 'material').'/');
/**
 *    OneImg.
 */
$images = config('images@base');
if (($_COOKIE['admin'] == md5(config('password').config('refresh_token')) || $images['public'])) {
    route::any('/images', 'ImagesController@index');
    if ($images['home']) {
        route::any('/', 'ImagesController@index');
    }
}

/*
 *    列目录
 */
 
 echo'
 <script language=javascript>
<!--
var startTime,endTime;
var d=new Date();
startTime=d.getTime();
//-->
</script>';
route::any('{path:#all}', 'IndexController@index');






$etime=microtime(true);//获取程序执行结束的时间
$total=$etime-$stime;   //计算差值
echo "<div style=text-align:center>执行时间为：{$total} 秒";

$req["headers"]="Authorization: bearer {$配置文件["access_token"]}".PHP_EOL."Content-Type: application/json".PHP_EOL;;
$req["url"]=$配置文件["api"];
$req["url"]=str_replace("root","",$req["url"]);
$ss=fetch::get($req);
	$data = json_decode($ss->content, true);
//	var_dump($data);
echo "账户". $data["owner"]["user"]["email"];
echo"已用空间". onedrive::human_filesize($data["quota"]["used"]);
echo"总空间". onedrive::human_filesize($data["quota"]["total"]);
echo"回收站". onedrive::human_filesize($data["quota"]["deleted"]).'网页执行时间';
echo '<script language=javascript>d=new Date();endTime=d.getTime
();document.write((endTime-startTime)/1000);</script>秒';

echo "</div>";



