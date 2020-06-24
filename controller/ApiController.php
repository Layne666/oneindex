<?php
class ApiController{
  public  $驱动器;
  public $请求路径;
  public $配置文件;
    	function __construct(){
    	    $varrr=explode("/",$_SERVER["REQUEST_URI"]);
             $this->$驱动器=$varrr["1"] ;
            array_splice($varrr,0, 1);
            unset($varrr['0']); 
           $请求路径 = implode("/", $varrr);
         $this->$请求路径= str_replace("?".$_SERVER["QUERY_STRING"],"",$请求路径); 
         if ($驱动器==""){
       $this->$请求路径="default";
     }
    $me= ROOT.'config/'.$this->$驱动器.'.php';

    if (file_exists($me)) {
    $this->$配置文件 = include ($me); }
 
    	}
    
    function put(){
        $filename= $_GET['upbigfilename'];
        $path=$this->$请求路径.$filename;
        $path = onedrive::urlencode($path);
	 	$path = empty($path)?'/':":/{$path}:/";
	    $token= $this->$配置文件["access_token"];
		$request['headers'] = "Authorization: bearer {$token}".PHP_EOL."Content-Type: application/json".PHP_EOL;
		$request['url']= $this->$配置文件["api"].$path."createUploadSession";
	    $request['post_data'] = '{"item": {"@microsoft.graph.conflictBehavior": "rename"}}';
		$resp = fetch::post($request);
		$data = json_decode($resp->content, true);
			if($resp->http_code == 409){
				return false;
			}
	
		echo $resp->content;
    }
    function delete(){
        
        echo "删除";
    }
    function rename()
    {}
    
    function creat()
    {}
    function PROPFIND(){
        
    }
}