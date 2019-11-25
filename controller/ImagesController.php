<?php 

class ImagesController{
	function generateRandomString($length) {
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $randomString = '';
            for ($i = 0; $i < $length; $i++) {
                $randomString .= $characters[rand(0, strlen($characters) - 1)];
            }
            return $randomString;
        }
	
	function index(){
		if($this->is_image($_FILES["file"]) ){
		    $filename = $_FILES["file"]['name'];
			$content = file_get_contents( $_FILES["file"]['tmp_name']);
			$remotepath =  'images/'.date('Y/m/d/').$this->generateRandomString(10).'/';
			$remotefile = $remotepath.$filename;
			$result = onedrive::upload(config('onedrive_root').$remotefile, $content);
			
			if($result){
				$root = get_absolute_path(dirname($_SERVER['SCRIPT_NAME'])).config('root_path');
				$http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
				$url = $_SERVER['HTTP_HOST'].$root.'/'.$remotepath.rawurldecode($filename).((config('root_path') == '?')?'&s':'?s');
				$url = $http_type.str_replace('//','/', $url);
				view::direct($url);
			}
		}
		return view::load('images/index');
	}

	function is_image($file){
		$config = config('images@base');
		$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
		if(!in_array($ext,$config['exts'])){
			return false;
		}
		if($file['size'] > 10485760 || $file['size'] == 0){
			return false;
		}

		return true;
	}
}
