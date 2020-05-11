<?php
require  __DIR__.'/init.php';

$config=include("config/token.php");
include("lib/fetch.php");
//config("id@sss")=1;

if (file_exists("config/sharepoint.php")){
    echo "sharepoint配置文件已存在请删除重新配置";exit;
}

if($_GET["site"]==""){
    echo '站点名称填写/sites/名称或者/teams/名称';
         echo '<form action="sharepoint.php" method="get">
　　<input type="text" name="site" value ="/sites/名称" />
　　<input type="submit" value="站点id" />
</form>';
exit; 
    
    
    
}
	$request['headers'] = "Authorization: bearer {$config["access_token"]}".PHP_EOL."Content-Type: application/json".PHP_EOL;
	$request["url"]='https://microsoftgraph.chinacloudapi.cn/v1.0/sites/root';
    $resp=fetch::get($request)	;
	$data = json_decode($resp->content, true);
    $hostname= $data["siteCollection"]["hostname"];

        $getsiteid='https://microsoftgraph.chinacloudapi.cn/v1.0/sites/'.$hostname.':'.$_GET["site"];
    	$request["url"]=$getsiteid;
    	$respp=fetch::get($request)	;
	$datass = json_decode($respp->content, true);

echo $siteidurl=($datass["id"]);
if (($datass["id"])==""){echo "获取站点id失败刷新重试试";

 echo '站点名称填写/sites/名称或者/teams/名称';
         echo '<form action="sharepoint.php" method="get">
　　<input type="text" name="site" value ="/sites/名称" />
　　<input type="submit" value="站点id" />
</form>';

exit;}
$b = '
<?php
return array(
"https://microsoftgraph.chinacloudapi.cn/v1.0/sites/'.$siteidurl.'/drive/root".$path.$query);



';

$results = print_r($b, true); 
file_put_contents('config/sharepoint.php', print_r($b, true));

echo "安装成功站点id".$datass["id"];
echo '<a href="/?/admin" target="_blank">进入后台刷新缓存生效 默认密码oneindex</a>';
