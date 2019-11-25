<?php

require  __DIR__.'/init.php';

//世纪互联
onedrive::$api_url = "https://microsoftgraph.chinacloudapi.cn/v1.0";
onedrive::$oauth_url = "https://login.partner.microsoftonline.cn/common/oauth2/v2.0";


/**
 *    程序安装
 */
if( empty( config('refresh_token') ) ){
	route::any('/','AdminController@install');
}

/**
 *    系统后台
 */
route::group(function(){
	return ($_COOKIE['admin'] == md5(config('password').config('refresh_token')) );
},function(){
	route::get('/logout','AdminController@logout');
	route::any('/admin/','AdminController@settings');
	route::any('/admin/cache','AdminController@cache');
	route::any('/admin/show','AdminController@show');
	route::any('/admin/setpass','AdminController@setpass');
	route::any('/admin/images','AdminController@images');

	route::any('/admin/upload','UploadController@index');
	//守护进程
	route::any('/admin/upload/run','UploadController@run');
	//上传进程
	route::post('/admin/upload/task','UploadController@task');
});
//登陆
route::any('/login','AdminController@login');

//跳转到登陆
route::any('/admin/',function(){
	return view::direct(get_absolute_path(dirname($_SERVER['SCRIPT_NAME'])).'?/login');
});



define('VIEW_PATH', ROOT.'view/'.(config('style')?config('style'):'material').'/');
/**
 *    OneImg
 */
$images = config('images@base');
if( ($_COOKIE['admin'] == md5(config('password').config('refresh_token')) || $images['public']) ){
	route::any('/images','ImagesController@index');
	if($images['home']){
		route::any('/','ImagesController@index');
	}
}


/**
 *    列目录
 */
route::group(function () {
	$hotlink = config('onedrive_hotlink');

	// 未启用防盗链
	if (!$hotlink) {
		return true;
	}
	// referer 不存在
	if (!isset($_SERVER['HTTP_REFERER'])) {
		return true;
	}

	$referer_domain = get_domain($_SERVER['HTTP_REFERER']);
	// 当前域本身
	if (str_is(get_domain(), $referer_domain)) {
		return true;
	}

	// 白名单
	$hotlinks = explode(';', $hotlink);
	$referer = false;
	
	foreach ($hotlinks as $_hotlink) {
		if (str_is(trim($_hotlink), $referer_domain)) {
			$referer = true;
		}
	}
	if (!$referer) {
		header('HTTP/1.1 403 Forbidden');
	}

	return $referer;
}, function() {
    route::any('{path:#all}','IndexController@index');
});
