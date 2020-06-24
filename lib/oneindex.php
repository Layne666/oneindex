<?php

	class oneindex{
		static $dir = array();
		static $file = array();
		static $thumb = array();
		
	

		// 刷新缓存
		static function refresh_cache($path){
			set_time_limit(0);
			if( php_sapi_name() == "cli" ){
			   echo $path.PHP_EOL;
			}
			$items = onedrive::dir($path);
			if(is_array($items)){
				cache::set('dir_'.$path, $items, config('cache_expire_time') );
			}
			foreach((array)$items as $item){
			    if($item['folder']){
			        self::refresh_cache($path.$item['name'].'/');
			    }
			}
		}
		
		// 列目录
		static function dir($path = '/'){
			$path = self::get_absolute_path($path);

			if(!empty(self::$dir[$path])){
				return self::$dir[$path];
			} 

    	self::$dir[$path] = cache::get('dir_'.$path, function() use ($path){
				return onedrive::dir($path);
			}, config('cache_expire_time'));
    

		
			return self::$dir[$path];
		}

		// 获取文件信息
		static function file($path){
			$path = self::get_absolute_path($path);
			$path_parts = pathinfo($path);
			$items = self::dir($path_parts['dirname']);
			if(!empty($items) && !empty($items[$path_parts['basename']])){
				return $items[$path_parts['basename']];
			}
		}


		// 文件是否存在
		static function file_exists($path){
			if(!empty(self::file($path))){
				return true;
			}
			return false;
		}

		//获取文件内容
		static function get_content($path){
			$item = self::file($path);
			// 仅小于10M 获取内容
			if(empty($item) OR $item['size'] > 10485760){
				return false;
			}

			return cache::get('content_'.$item['path'], function() use ($item){
				$resp = fetch::get($item['downloadUrl']);
				if($resp->http_code == 200){
					return $resp->content;
				}
			}, config('cache_expire_time') );
		}



		//缩略图
		static function thumb($path,$width=800,$height=800){
			$path = self::get_absolute_path($path);
			if(empty(self::$thumb[$path])){
				self::$thumb[$path] = cache::get('thumb_'.$path, function() use ($path){
					$url = onedrive::thumbnail($path);
					list($url,$tmp) = explode('&width=', $url);
					return $url;
				}, config('cache_expire_time'));
			}

			self::$thumb[$path] .= strpos(self::$thumb[$path], '?')?'&':'?';
			return self::$thumb[$path]."width={$width}&height={$height}";
		}

		//获取下载链接
		static function download_url($path){
			$item = self::file($path);
			if(!empty($item['downloadUrl'])){
				return $item['downloadUrl'];
			}
			return false;
		}


		//工具函数获取绝对路径
		static function get_absolute_path($path) {
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

	
	
	
	static function is_mobile(){  
    $_SERVER['ALL_HTTP'] = isset($_SERVER['ALL_HTTP']) ? $_SERVER['ALL_HTTP'] : '';  
    $mobile_browser = '0';  
    if(preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|iphone|ipad|ipod|android|xoom)/i', strtolower($_SERVER['HTTP_USER_AGENT'])))  
        $mobile_browser++;  
    if((isset($_SERVER['HTTP_ACCEPT'])) and (strpos(strtolower($_SERVER['HTTP_ACCEPT']),'application/vnd.wap.xhtml+xml') !== false))  
        $mobile_browser++;  
    if(isset($_SERVER['HTTP_X_WAP_PROFILE']))  
        $mobile_browser++;  
    if(isset($_SERVER['HTTP_PROFILE']))  
        $mobile_browser++;  
    $mobile_ua = strtolower(substr($_SERVER['HTTP_USER_AGENT'],0,4));  
    $mobile_agents = array(  
        'w3c ','acs-','alav','alca','amoi','audi','avan','benq','bird','blac',  
        'blaz','brew','cell','cldc','cmd-','dang','doco','eric','hipt','inno',  
        'ipaq','java','jigs','kddi','keji','leno','lg-c','lg-d','lg-g','lge-',  
        'maui','maxo','midp','mits','mmef','mobi','mot-','moto','mwbp','nec-',  
        'newt','noki','oper','palm','pana','pant','phil','play','port','prox',  
        'qwap','sage','sams','sany','sch-','sec-','send','seri','sgh-','shar',  
        'sie-','siem','smal','smar','sony','sph-','symb','t-mo','teli','tim-',  
        'tosh','tsm-','upg1','upsi','vk-v','voda','wap-','wapa','wapi','wapp',  
        'wapr','webc','winw','winw','xda','xda-' 
        );  
    if(in_array($mobile_ua, $mobile_agents))  
        $mobile_browser++;  
    if(strpos(strtolower($_SERVER['ALL_HTTP']), 'operamini') !== false)  
        $mobile_browser++;  
    // Pre-final check to reset everything if the user is on Windows  
    if(strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'windows') !== false)  
        $mobile_browser=0;  
    // But WP7 is also Windows, with a slightly different characteristic  
    if(strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'windows phone') !== false)  
        $mobile_browser++;  
    if($mobile_browser>0)  
        return true;  
    else
        return false;  
}

	}