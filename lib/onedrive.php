<?php
	class onedrive{
		static $client_id;
		static $client_secret;
		static $redirect_uri;
		static $typeurl;
		static $oauth_url;
		static $drivestype;
		static $api;
		static $api_url;
		static  $access_token;
		//验证URL，浏览器访问、授权
		static function authorize_url(){
			$client_id = self::$client_id;
			$scope = urlencode("offline_access files.readwrite.all");
			$redirect_uri = self::$redirect_uri;
			$url = self::$oauth_url."/authorize?client_id={$client_id}&scope={$scope}&response_type=code&redirect_uri={$redirect_uri}";
			
			if($_SERVER['HTTP_HOST'] != 'localhost'){
				$url .= '&state='.urlencode('http://'.$_SERVER['HTTP_HOST'].get_absolute_path(dirname($_SERVER['PHP_SELF'])));
			}
			
			return $url;
		}

		//使用 $code, 获取 $refresh_token
		static function authorize($code = ""){
			$client_id = self::$client_id;
			$client_secret = self::$client_secret;
			$redirect_uri = self::$redirect_uri;

			$url = self::$oauth_url."/token";
			$post_data = "client_id={$client_id}&redirect_uri={$redirect_uri}&client_secret={$client_secret}&code={$code}&grant_type=authorization_code";
			fetch::$headers = "Content-Type: application/x-www-form-urlencoded";
			$resp = fetch::post($url, $post_data);
			$data = json_decode($resp->content, true);
			return $data;
		}

		//使用 $refresh_token，获取 $access_token
		static function get_token($refresh_token){
			$client_id = self::$client_id;
			$client_secret = self::$client_secret;
			$redirect_uri = self::$redirect_uri;

			$request['url'] = self::$oauth_url."/token";
			$request['post_data']  = "client_id={$client_id}&redirect_uri={$redirect_uri}&client_secret={$client_secret}&refresh_token={$refresh_token}&grant_type=refresh_token";
			$request['headers']= "Content-Type: application/x-www-form-urlencoded";
			$resp = fetch::post($request);
			$data = json_decode($resp->content, true);
		
			
			return $data;
		}

	


        static function access_token(){
            $varrr=explode("/",$_SERVER["REQUEST_URI"]);
            $驱动器=$varrr["1"];
            $配置文件 = config('@'.$驱动器);
			if($配置文件['expires_on'] > time()+600){
				return $token['access_token'];
			}else{
				$refresh_token = config('refresh_token@'.$驱动器);
				$token = self::get_token($refresh_token);
				if(!empty($token['refresh_token'])){
					$配置文件['expires_on'] = time()+ $token['expires_in'];
					$配置文=$token;
					
					config('@'.$驱动器, $配置文件);
					return $token['access_token'];
				}
			}
			return "";
		}









		// 生成一个request，带token
		static function request($path="/", $query=""){
			$path = self::urlencode($path);
			$path = empty($path)?'/':":/{$path}:/";
	    	$token=self::$access_token;
			$request['headers'] = "Authorization: bearer {$token}".PHP_EOL."Content-Type: application/json".PHP_EOL;
              $request['url']=self::$typeurl.$path.$query;
		
			return $request;
			
		}

		
		//返回目录信息
		static function dir($path="/"){
			$request = self::request($path, "children?select=name,size,folder,@microsoft.graph.downloadUrl,lastModifiedDateTime,id");
			$items = array();
			self::dir_next_page($request, $items);
			//不在列表显示的文件夹
			$hide_list = explode(PHP_EOL,config('onedrive_hide'));
			if(is_array($hide_list) && count($hide_list)>0){
				foreach($hide_list as $hide_dir){
					foreach($items as $key=>$_array){
						if(!empty(trim($hide_dir)) && stristr($key,trim($hide_dir)))unset($items[$key]);
					}
				}
			}
			return $items;
		}

		//通过分页获取页面所有item
		static function dir_next_page($request, &$items, $retry=0){
			$resp = fetch::get($request);
			
			$data = json_decode($resp->content, true);
			if(empty($data) && $retry < 3){
				$retry += 1;
				return self::dir_next_page($request, $items, $retry);
			}
			
			foreach((array)$data['value'] as $item){
				//var_dump($item);
				$items[$item['name']] = array(
					'name'=>$item['name'],
					'id'=>$item['id'],
					'size'=>$item['size'],
					'lastModifiedDateTime'=>strtotime($item['lastModifiedDateTime']),
					'downloadUrl'=>$item['@microsoft.graph.downloadUrl'],
					'folder'=>empty($item['folder'])?false:true
				);
			}

			if(!empty($data['@odata.nextLink'])){
				$request = self::request();
				$request['url'] = $data['@odata.nextLink'];
				return self::dir_next_page($request, $items);
			}
		}

		
		
		
		static function rename($itemid,$name){
		     $access_token=self::$access_token;
		 $api=str_replace("root","items/".$itemid,self::$typeurl) ;
		 
		 
		 $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => $api,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "PATCH",
        CURLOPT_POSTFIELDS =>"{\n  \"name\": \"".$name."\"\n}",
        CURLOPT_HTTPHEADER => array(
         "Authorization: Bearer ".$access_token,
         "Content-Type: application/json"
  ),
));

$response = curl_exec($curl);

curl_close($curl);
var_dump($response) ;

	
		    
		}
		
		
		
		
        static function delete($itemid=array()){
       
            $varrr=explode("/",$_SERVER["REQUEST_URI"]);
		    $驱动器=$varrr["1"] ;array_splice($varrr,0, 1);unset($varrr['0']); 
            $请求路径 = implode("/", $varrr);
            $请求路径= self::urlencode(str_replace("?".$_SERVER["QUERY_STRING"],"",$请求路径)); 
            $path = empty($请求路径)?'/':":/{$请求路径}:/";
            $配置文件=require(ROOT."config/".$驱动器.".php");
           
          $access_token=$配置文件["access_token"];
		     $apie=str_replace("root","items/",$配置文件["api"]) ;
		   
		  $apis=array();

         for($i=0; $i<count($itemid); $i++)
         {
          $apis[$i]=$apie.$itemid[$i];
           }

$result=$res=$ch=array();
$nch = 0;
$mh = curl_multi_init();
  foreach ($apis as $nk => $url) {
 
    $timeout=20;
    $ch[$nch] = curl_init();
    curl_setopt_array($ch[$nch], array(
    CURLOPT_URL => $url,
    CURLOPT_TIMEOUT => $timeout,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "DELETE",
    CURLOPT_HTTPHEADER => array(
    "Authorization: Bearer ". $access_token,
    "Content-Type: application/json"
  ),
    
    ));
 
    curl_multi_add_handle($mh, $ch[$nch]);
    ++$nch;
 
  }
 
  /* wait for performing request */
 
  do {
    $mrc = curl_multi_exec($mh, $running);
  } while (CURLM_CALL_MULTI_PERFORM == $mrc);
 
  while ($running && $mrc == CURLM_OK) {
    // wait for network
    if (curl_multi_select($mh, 0.5) > -1) {
      // pull in new data;
      do {
        $mrc = curl_multi_exec($mh, $running);
      } while (CURLM_CALL_MULTI_PERFORM == $mrc);
    }
 
  }
 
  if ($mrc != CURLM_OK) {
    error_log("CURL Data Error");
  }
 
  /* get data */
 
  $nch = 0;
 
  foreach ($apis as $moudle=>$node) {
    if (($err = curl_error($ch[$nch])) == '') {
      $res[$nch]=curl_multi_getcontent($ch[$nch]);
      $result[$moudle]=$res[$nch];
    }else{
      error_log("curl error");
 
    }
 
    curl_multi_remove_handle($mh,$ch[$nch]);
    curl_close($ch[$nch]);
    ++$nch;
  }
 
  curl_multi_close($mh);
  echo "批量处理完成";
 
    
		}
		
		
		


        static function pathtoid($access_token,$path){	
			$request=self::request(urldecode($path));
            $request['headers'] = "Authorization: bearer {$access_token}".PHP_EOL."Content-Type: application/json".PHP_EOL;
            $resp=	fetch::get($request);
            $data = json_decode($resp->content, true);
            return $data["id"];
   
        }
        static function movepast($itemid,$newitemid){
    
        $varrr=explode("/",$_SERVER["REQUEST_URI"]);
		    $驱动器=$varrr["1"] ;array_splice($varrr,0, 1);unset($varrr['0']); 
	
		$配置文件=require(ROOT."config/".$驱动器.".php");
        onedrive::$typeurl=$配置文件["api"] ;
        onedrive::$access_token=access_token($配置文件,$驱动器);
        $api=str_replace("root","items/".$itemid,self::$typeurl) ;
        $access_token=self::$access_token;

        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => $api,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "PATCH",
        CURLOPT_POSTFIELDS =>"{\n  \"parentReference\": {\n    \"id\": \"".$newitemid."\"\n  }\n  \n}",
        CURLOPT_HTTPHEADER => array(
         "Authorization: Bearer ".onedrive::$access_token,
        "Content-Type: application/json" ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        echo $response;
        echo $id."完成";
   
    
        }











static function 批量移动($itemid=array(),$newitemid){
 
 
    $varrr=explode("/",$_SERVER["REQUEST_URI"]);
    $驱动器=$varrr["1"] ;array_splice($varrr,0, 1);unset($varrr['0']); 
    $配置文件=require(ROOT."config/".$驱动器.".php");
    $token=$配置文件["access_token"];
     echo  $typeurl=$配置文件["api"] ;
    var_dump($itemid);
    $apis=array();
    $api=str_replace("root","items/",$typeurl);
    for($i=0; $i<count($itemid); $i++)
    {
     $apis[$i]=$api.$itemid[$i];
        
        
    }
   
   
  //  $api=str_replace("root","items/".$itemid,$typeurl) ;
$result=$res=$ch=array();
$nch = 0;
$mh = curl_multi_init();
  foreach ($apis as $nk => $url) {
 
    $timeout=20;
    $ch[$nch] = curl_init();
    curl_setopt_array($ch[$nch], array(
    CURLOPT_URL => $url,
    CURLOPT_TIMEOUT => $timeout,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
     CURLOPT_CUSTOMREQUEST => "PATCH",
    CURLOPT_POSTFIELDS =>"{\n  \"parentReference\": {\n    \"id\": \"".$newitemid."\"\n  }\n  \n}",
    CURLOPT_HTTPHEADER => array(
    "Authorization: Bearer ".$token,
    "Content-Type: application/json"
  ),
    
    ));
 
    curl_multi_add_handle($mh, $ch[$nch]);
    ++$nch;
 
  }
 
  /* wait for performing request */
 
  do {
    $mrc = curl_multi_exec($mh, $running);
  } while (CURLM_CALL_MULTI_PERFORM == $mrc);
 
  while ($running && $mrc == CURLM_OK) {
    // wait for network
    if (curl_multi_select($mh, 0.5) > -1) {
      // pull in new data;
      do {
        $mrc = curl_multi_exec($mh, $running);
      } while (CURLM_CALL_MULTI_PERFORM == $mrc);
    }
 
  }
 
  if ($mrc != CURLM_OK) {
    error_log("CURL Data Error");
  }
 
  /* get data */
 
  $nch = 0;
 
  foreach ($apis as $moudle=>$node) {
    if (($err = curl_error($ch[$nch])) == '') {
      $res[$nch]=curl_multi_getcontent($ch[$nch]);
      $result[$moudle]=$res[$nch];
    }else{
      error_log("curl error");
 
    }
 
    curl_multi_remove_handle($mh,$ch[$nch]);
    curl_close($ch[$nch]);
    ++$nch;
  }
 
  curl_multi_close($mh);
  echo "批量处理完成";
 var_dump($result) ;
 
}





static function get_siteidbyname($sitename,$access_token,$api_url){
    
    
     $request['headers'] = "Authorization: bearer {$access_token}".PHP_EOL.'Content-Type: application/json'.PHP_EOL;
    $request['url'] = $api_url.'/sites/root';
    $resp = fetch::get($request);
    $data = json_decode($resp->content, true);
    $hostname = $data['siteCollection']['hostname'];
    $getsiteid = $api_url.'/sites/'.$hostname.':'.$_REQUEST['site'];
    $request['url'] = $getsiteid;
    $respp = fetch::get($request);
    $datass = json_decode($respp->content, true);
   return $siteidurl=  $datass["id"];
    
    
    
}









static function create_folder($path,$name="新建文件夹"){
     	$path = self::urlencode($path);
   	$path = empty($path)?'/':":/{$path}:/";
	    	$api=self::$typeurl.$path."/children";
       	$curl = curl_init();
                curl_setopt_array($curl, array(
          CURLOPT_URL => $api,
             CURLOPT_RETURNTRANSFER => true,
                 CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
         CURLOPT_TIMEOUT => 0,
         CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS =>"{\n  \"name\": \"".$name."\",\n  \"folder\": { },\n  \"@microsoft.graph.conflictBehavior\": \"rename\"\n}",
  CURLOPT_HTTPHEADER => array(
    "Authorization: Bearer ".self::$access_token."",
    "Content-Type: application/json"
  ),
));

$response = curl_exec($curl);

curl_close($curl);
echo $response;

        }
		//文件缩略图链接
		static function thumbnail($path,$size='large'){
			$request = self::request($path,"thumbnails/0?select={$size}");
			$resp = fetch::get($request);
			$data = json_decode($resp->content, true);
			$request = self::request($path,"thumbnails/0?select={$size}");
			return @$data[$size]['url'];
		}

		//简单文件上传函数
		static function upload($path,$content){
			$request = self::request($path,"content");
			$request['post_data'] = $content;
			$resp = fetch::put($request);
			$data = @json_decode($resp->content, true);
			return $data;
		}

		static function upload_url($path, $url){
			$request = self::request(get_absolute_path(dirname($path)),"children");
			$request['headers'] .= "Prefer: respond-async".PHP_EOL;
			$post_data['@microsoft.graph.sourceUrl'] = $url;
			$post_data['name'] = pathinfo($path, PATHINFO_BASENAME );
			$post_data['file'] = json_decode("{}");
			$request['post_data'] = json_encode($post_data);
			$resp = fetch::post($request);
			list($tmp, $location) = explode('ocation:', $resp->headers);
			list($location, $tmp) = explode(PHP_EOL, $location);
			return trim($location);
		}
		
		static function create_upload_session($path){
			$request = self::request($path, 'createUploadSession');
			$request['post_data'] = '{"item": {"@microsoft.graph.conflictBehavior": "fail"}}';
			$token = self::access_token();
			$resp = fetch::post($request);
			$data = json_decode($resp->content, true);
			if($resp->http_code == 409){
				return false;
			}
			return $data;
		}

		static function upload_session($url, $file, $offset, $length=10240){
			$token = self::access_token();
			$file_size = self::_filesize($file);
			$content_length = (($offset+$length)>$file_size)?($file_size-$offset):$length;
			$end = $offset+$content_length-1;
			$post_data = self::file_content($file, $offset, $length);

			$request['url'] = $url;
			$request['curl_opt']=[CURLOPT_TIMEOUT=>360];
			$request['headers'] = "Authorization: bearer {$token}".PHP_EOL;
			$request['headers'] .= "Content-Length: {$content_length}".PHP_EOL;
			$request['headers'] .= "Content-Range: bytes {$offset}-{$end}/{$file_size}";
			$request['post_data'] = $post_data;
			$resp = fetch::put($request);
			$data = json_decode($resp->content, true);
			return $data;
		}

		static function upload_session_status($url){
			$token = self::access_token();
			fetch::$headers = "Authorization: bearer {$token}".PHP_EOL."Content-Type: application/json".PHP_EOL;
			$resp = fetch::get($url);
			$data = json_decode($resp->content, true);
			return $data;
		}

		static function delete_upload_session($url){
			$token = self::access_token();
			fetch::$headers = "Authorization: bearer {$token}".PHP_EOL."Content-Type: application/json".PHP_EOL;
			$resp = fetch::delete($url);
			$data = json_decode($resp->content, true);
			return $data;
		}

		static function file_content($file, $offset, $length){
			$handler = fopen($file, "rb") OR die('获取文件内容失败');
			fseek($handler, $offset);
			
			return fread($handler, $length);
		}

		static function human_filesize($size, $precision = 1) {
			for($i = 0; ($size / 1024) > 1; $i++, $size /= 1024) {}
			return round($size, $precision).(['B','KB','MB','GB','TB','PB','EB','ZB','YB'][$i]);
		}

		static function urlencode($path){
			foreach(explode('/', $path) as $k=>$v){
				if(empty(!$v)){
					$paths[] = rawurlencode($v);
				}
			}
			return @join('/',$paths);
		}
			
		static function _filesize($path){
		    if (!file_exists($path))
		        return false;
		    $size = filesize($path);
		    
		    if (!($file = fopen($path, 'rb')))
		        return false;
		    
		    if ($size >= 0){//Check if it really is a small file (< 2 GB)
		        if (fseek($file, 0, SEEK_END) === 0){//It really is a small file
		            fclose($file);
		            return $size;
		        }
		    }
		    
		    //Quickly jump the first 2 GB with fseek. After that fseek is not working on 32 bit php (it uses int internally)
		    $size = PHP_INT_MAX - 1;
		    if (fseek($file, PHP_INT_MAX - 1) !== 0){
		        fclose($file);
		        return false;
		    }
		    
		    $length = 1024 * 1024;
		    while (!feof($file)){//Read the file until end
		        $read = fread($file, $length);
		        $size = bcadd($size, $length);
		    }
		    $size = bcsub($size, $length);
		    $size = bcadd($size, strlen($read));
		    
		    fclose($file);
		    return $size;
		}
	}
