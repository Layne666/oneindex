<?php
 
!defined('ROOT') && define('ROOT', str_replace("\\", "/", dirname(__FILE__)) . '/');
require_once(ROOT.'vendor/autoload.php') ;
require_once(ROOT.'init.php') ;
use GuzzleHttp\Client;
use GuzzleHttp\Promise;
 $access=config('@default')["access_token"];



function api_get($url,$type="cn"){
    global  $access;
    if($type=="cn"){
        $api="https://microsoftgraph.chinacloudapi.cn/v1.0/";
    }else{
          $api="https://graph.microsoft.com/v1.0/";
    }
     $apc=new Client([
    'base_uri' => $api,
    'headers' => [
        'Accept' => 'application/json',
        'Authorization' =>     $access,
        'Content-Type' => 'application/json',
    ],
    'http_errors' => false
]);

    return json_decode($apc->get($url)->getBody());
    
    
    
}






function get_drivebyname($token,$name="/me",$type="cn")

{  if($type=="cn"){
        $api="https://microsoftgraph.chinacloudapi.cn/v1.0/";
    }else{
          $api="https://graph.microsoft.com/v1.0/";
    }
    $apc=new Client([
    'base_uri' => $api,
    'headers' => [
        'Accept' => 'application/json',
        'Authorization' =>    $token,
        'Content-Type' => 'application/json',
    ],
    'http_errors' => false
]);
if($name=="/me"){
      return  $data=json_decode($apc->get("me/drive/")->getBody());
}else{

         $hostname=json_decode($apc->get("sites/root")->getBody())->siteCollection->hostname;
     $siteid=json_decode(($apc->get("sites/". $hostname.":".$name)->getBody()))->id;
   return  $driveid=json_decode($apc->get("sites/".($siteid."/drive"))->getBody());}
    
}
$t1=microtime(true);
$clients = new Client([
  
   
    'http_errors' => false
]);


// Initiate each request but do not block
$promises = array();
    for ($i;$i<30;$i++){
     $promises[$i] =  $clients->requestAsync('get', 'https://www.hostloc.com/forum-45-1.html');
    }
    


// Wait on all of the requests to complete.
$results = Promise\unwrap($promises);



foreach ($results as $k=>$v){
    echo "1";
   $v->getBody();
    
}
$t2=microtime(true);
echo $time1=$t2-$t1;