<?php
require __DIR__.'/init.php';

 //应用的APPID
 $app_id = "101861794";
 //应用的APPKEY
 $app_secret = "aca494b87124da2fbd94d31de263a114";
 //成功授权后的回调地址
 $my_url = 'https://coding.mxin.ltd/';
 //Step1：获取Authorization Code
 session_start();
 $code = $_REQUEST["code"];
 if(empty($code))
 {
 	
 	
    //state参数用于防止CSRF攻击，成功授权后回调时会原样带回
    $_SESSION['state'] = "http://".$_SERVER['HTTP_HOST']."/login.php";
    //拼接URL
    $dialog_url = "https://graph.qq.com/oauth2.0/authorize?response_type=code&client_id="
       . $app_id . "&redirect_uri=" . urlencode($my_url) . "&state="
       . $_SESSION['state'];
    echo("<script> top.location.href='" . $dialog_url . "'</script>");
 }
 //Step2：通过Authorization Code获取Access Token
 if($_REQUEST['state']!=="" )
 {
    //拼接URL
    $token_url = "https://graph.qq.com/oauth2.0/token?grant_type=authorization_code&"
    . "client_id=" . $app_id . "&redirect_uri=" . urlencode($my_url)
    . "&client_secret=" . $app_secret . "&code=" . $code;
    $response = file_get_contents($token_url);
    if (strpos($response, "callback") !== false)
    {
       $lpos = strpos($response, "(");
       $rpos = strrpos($response, ")");
       $response  = substr($response, $lpos + 1, $rpos - $lpos -1);
       $msg = json_decode($response);
       if (isset($msg->error))
       {
          echo "<h3>error:</h3>" . $msg->error;
          echo "<h3>msg  :</h3>" . $msg->error_description;
          exit;
       }
    }
    //Step3：使用Access Token来获取用户的OpenID
    $params = array();
    parse_str($response, $params);
    $graph_url = "https://graph.qq.com/oauth2.0/me?access_token=".$params['access_token'];
    $str  = file_get_contents($graph_url);
    if (strpos($str, "callback") !== false)
    {
       $lpos = strpos($str, "(");
       $rpos = strrpos($str, ")");
       $str  = substr($str, $lpos + 1, $rpos - $lpos -1);
    }
    $user = json_decode($str);
    if (isset($user->error))
    {
       echo "<h3>error:</h3>" . $user->error;
       echo "<h3>msg  :</h3>" . $user->error_description;
       exit;
    }
   //cho("Hello " . $user->openid);
    if(config("openid")==""){
         config("openid",$user->openid);
         config("password",$user->openid);
         setcookie("admin",config("password"));
           header('Location:/');
    }elseif($user->openid==config("openid")){
          setcookie("admin",config("password"));
           header('Location:/');
    }else{
        echo"不是管理员";
    }
  
    
 }
 else
 {
    echo("The state does not match. You may be a victim of CSRF.");
 }
      
      
      
       
       
   //	header('Location: '.$goto);
     
      