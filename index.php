<?php
require __DIR__.'/init.php';


if (!file_exists(ROOT.'config/base.php') or !file_exists(ROOT.'config/default.php') ) {
      header('Location: /install.php');
    exit;
}

switch($_SERVER["REQUEST_METHOD"]){
   case "GET": 
       break;
  case "POST":
      break;
     default:
        
         require_once(ROOT."lib/api.php");
         exit;
    
}





	 
	 


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
   // route::any('/admin/upload', 'UploadController@index');
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
if (($_COOKIE['admin'] == config('password') || $images['public'])) {
    route::any('/'.$驱动器.'/images', 'ImagesController@index');
    if ($images['home']) {
        route::any('/', 'ImagesController@index');
    }
}










 
route::any('{path:#all}', 'IndexController@index');

$etime=microtime(true);//获取程序执行结束的时间

$total=$etime-$stime;   //计算差值

?>
<font color="#FFFFF">php 运行时间<?php e($total);?>秒</font> 





