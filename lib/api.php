<?php
 
$varrr=explode("/",$_SERVER["REQUEST_URI"]);
$驱动器=$varrr["1"] ;array_splice($varrr,0, 1);unset($varrr['0']); $请求路径 = implode("/", $varrr);$请求路径= str_replace("?".$_SERVER["QUERY_STRING"],"",$请求路径); 
 if ($驱动器==""){
     $驱动器="default";
 }

if (file_exists(ROOT.'config/'.$驱动器.'.php')) {
    $配置文件 = include (ROOT.'config/'.$驱动器.'.php'); }
 $access_token=access_token($配置文件,$驱动器);
onedrive::$access_token=$access_token;
onedrive::$typeurl=$配置文件["api"] ;

if(!is_login()){
   // echo "未授权";
   // exit;
}
if ($_SERVER["REQUEST_METHOD"]=="MOVE"){
    echo " 移动文件";
   
    
}


if ($_SERVER["REQUEST_METHOD"]=="PUT"){
  //上传文件
        $filename=  $_GET['upbigfilename'];
        $path=$请求路径.$filename;
        $path = onedrive::urlencode($path);
		$path = empty($path)?'/':":/{$path}:/";
	    $token=$配置文件["access_token"];
		$request['headers'] = "Authorization: bearer {$token}".PHP_EOL."Content-Type: application/json".PHP_EOL;
		$request['url']= $配置文件["api"].$path."createUploadSession";
	    $request['post_data'] = '{"item": {"@microsoft.graph.conflictBehavior": "rename"}}';
		$resp = fetch::post($request);
		$data = json_decode($resp->content, true);
			if($resp->http_code == 409){
				return false;
			}
	
		echo $resp->content;
    
}

if ($_SERVER["REQUEST_METHOD"]=="DELETE"){
    echo " 删除文件";
     if($_GET["action"]=="dellist"){
         $bodyData  = @file_get_contents('php://input');
        $ss=json_decode( $bodyData,true);
        if( !is_array( $ss ) )
{
 $ss=array(
     0=> $ss);
}
      
        
        
         var_dump($ss);
        //  foreach ($ss as $s1s) {
              echo  onedrive::delete($ss);
         // }
         }
    
}

if ($_SERVER["REQUEST_METHOD"]=="RENAME"){
        $bodyData  = @file_get_contents('php://input');
       
        $ss=json_decode( $bodyData,true);
 
  onedrive::rename($ss["id"],$ss["name"])  ;
    
}
if ($_SERVER["REQUEST_METHOD"]=="CREAT"){
    echo " 新建文件";
   
    
}
if ($_SERVER["REQUEST_METHOD"]=="PROPFIND"){
    echo " webdav";
   
    
}