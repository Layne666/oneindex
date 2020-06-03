<?php
require __DIR__.'/init.php';



$varrr=explode("/",$_SERVER["REQUEST_URI"]);
 $驱动器=$varrr["1"] ;
 if ($驱动器==""){
     $驱动器="default";
 }
 
array_splice($varrr,0, 1);
unset($varrr['0']);

 $请求路径 = implode("/", $varrr);  
 
$请求路径= str_replace("?".$_SERVER["QUERY_STRING"],"",$请求路径);
 $url=$请求路径;


////////////////////////////////////////////////////////////////////////////////


//加载配置文件
 $drivesfile = ROOT.'config/'.$驱动器.'.php';
 
if (file_exists($drivesfile)) {
    $配置文件 = include $drivesfile;
    
}
    if (!file_exists(ROOT.'config/base.php') or !file_exists(ROOT.'config/default.php') ) {
      header('Location: /install.php');

  
    exit;
    }
 


 

define('CACHE_PATH', ROOT.'cache/'.$驱动器."");
cache::$type = empty( config('cache_type') )?'secache':config('cache_type');
////////////////////////////////////初始化配置文件start//////////////////////////////////////

define('CACHE_PATH', ROOT.'cache/'.$驱动器."");
cache::$type = empty( config('cache_type') )?'secache':config('cache_type');



 


onedrive::$client_id =  $配置文件["client_id"];
onedrive::$client_secret =$配置文件["client_secret"];
onedrive::$redirect_uri = $配置文件["redirect_uri"];
onedrive::$api_url = $配置文件["api_url"];
onedrive::$oauth_url = $配置文件["oauth_url"];
 	onedrive::$access_token=access_token($配置文件,$驱动器);
onedrive::$typeurl=$配置文件["api"] ;
	
	
	
	

	
if ($_GET["action"]=="upbigfile")
{
    ////////////
    
  $filename=  $_GET['upbigfilename'];
    
    
    
    
    
    
    
     $path=$请求路径.$filename;
    	$path = onedrive::urlencode($path);
			$path = empty($path)?'/':":/{$path}:/";
	$token=$配置文件["access_token"];
			$request['headers'] = "Authorization: bearer {$token}".PHP_EOL."Content-Type: application/json".PHP_EOL;
		$request['url']= $配置文件["api"].$path."createUploadSession";
				
		$request['post_data'] = '{"item": {"@microsoft.graph.conflictBehavior": "fail"}}';
		
			$resp = fetch::post($request);
		//	var_dump($resp);
		
			$data = json_decode($resp->content, true);
			if($resp->http_code == 409){
				return false;
			}
	
		echo $resp->content;
	
    
    
    
    
    
    
    
    
    
    
    
    
    
 
  
    exit;
}
   
//////////////////debug//////////////////


//////////////////debug//////////////////


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
    route::any('/admin/upload', 'UploadController@index');
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
if (($_COOKIE['admin'] == md5(config('password').config('refresh_token')) || $images['public'])) {
    route::any('/images', 'ImagesController@index');
    if ($images['home']) {
        route::any('/', 'ImagesController@index');
    }
}

/*
 *    列目录
 */
 
 echo'
 <script language=javascript>
<!--
var startTime,endTime;
var d=new Date();
startTime=d.getTime();
//-->
</script>';
route::any('{path:#all}', 'IndexController@index');





$etime=microtime(true);//获取程序执行结束的时间
$total=$etime-$stime;   //计算差值
echo "<div style=text-align:center>执行时间为：{$total} 秒";
if($_COOKIE["admin"]!==config("password")){echo '<a href= "/admin">登陆</a>
';
    exit;}
$req["headers"]="Authorization: bearer {$配置文件["access_token"]}".PHP_EOL."Content-Type: application/json".PHP_EOL;;
$req["url"]=$配置文件["api"];
$req["url"]=str_replace("root","",$req["url"]);
$ss=fetch::get($req);
	$data = json_decode($ss->content, true);
//	var_dump($data);

echo "账户". $data["owner"]["user"]["email"];
echo"已用空间". onedrive::human_filesize($data["quota"]["used"]);
echo"总空间". onedrive::human_filesize($data["quota"]["total"]);
echo"回收站". onedrive::human_filesize($data["quota"]["deleted"]).'网页执行时间';
echo '<script language=javascript>d=new Date();endTime=d.getTime
();document.write((endTime-startTime)/1000);</script>秒';

echo "</div>";
?>
<meta http-equiv="content-type" content="text/html;charset=utf-8">
<div id="upload_div" style="margin:0 0 16px 0">
                <div id="upload_btns" align="center">
                    <select onchange="document.getElementById('upload_file').webkitdirectory=this.value;">
                        <option value="">上传文件</option>
                        <option value="1">上传文件夹</option>
                    </select>
                    <input id="upload_file" type="file" name="upload_filename" multiple="multiple">
                    <input id="upload_submit" onclick="preup();" value="上传" type="button">
                </div>
                </div>

<script>
  function uploadbuttonhide() {
        document.getElementById('upload_btns').style.display='none';
        /*document.getElementById('upload_submit').disabled='disabled';
        document.getElementById('upload_file').disabled='disabled';
        document.getElementById('upload_submit').style.display='none';
        document.getElementById('upload_file').style.display='none';*/
    }
    function uploadbuttonshow() {
        document.getElementById('upload_btns').style.display='';
        /*document.getElementById('upload_file').disabled='';
        document.getElementById('upload_submit').disabled='';
        document.getElementById('upload_submit').style.display='';
        document.getElementById('upload_file').style.display='';*/
    }
    function preup() {
        uploadbuttonhide();
        var files=document.getElementById('upload_file').files;
	    if (files.length<1) {
            uploadbuttonshow();
            return;
        };
        var table1=document.createElement('table');
        document.getElementById('upload_div').appendChild(table1);
        table1.setAttribute('class','list-table');
        var timea=new Date().getTime();
        var i=0;
        getuplink(i);
        function getuplink(i) {
            var file=files[i];
            var tr1=document.createElement('tr');
            table1.appendChild(tr1);
            tr1.setAttribute('data-to',1);
            var td1=document.createElement('td');
            tr1.appendChild(td1);
            td1.setAttribute('style','width:30%;word-break:break-word;');
            td1.setAttribute('id','upfile_td1_'+timea+'_'+i);
            td1.innerHTML=(file.webkitRelativePath||file.name)+'<br>'+size_format(file.size);
            var td2=document.createElement('td');
            tr1.appendChild(td2);
            td2.setAttribute('id','upfile_td2_'+timea+'_'+i);
            if (file.size>100*1024*1024*1024) {
                td2.innerHTML='<font color="red">文件过大，终止上传。</font>';
                uploadbuttonshow();
                return;
            }
            upbigfilename = encodeURIComponent((file.webkitRelativePath||file.name));

            td2.innerHTML='获取上传链接 ...';
            var xhr1 = new XMLHttpRequest();
            xhr1.open("GET", '/<?php echo $驱动器."/".$请求路径 ?>?action=upbigfile&upbigfilename='+ upbigfilename +'&filesize='+ file.size +'&lastModified='+ file.lastModified);
            xhr1.setRequestHeader('x-requested-with','XMLHttpRequest');
            xhr1.send(null);
            xhr1.onload = function(e){
                td2.innerHTML='<font color="red">'+xhr1.responseText+'</font>';
                if (xhr1.status==200) {
                   // alert(xhr1.responseText);
                    console.log(xhr1.responseText);
                    var html=JSON.parse(xhr1.responseText);
                    if (!html['uploadUrl']) {
                        td2.innerHTML='<font color="red">'+xhr1.responseText+'</font><br>';
                        uploadbuttonshow();
                    } else {
                        td2.innerHTML='开始上传 ...';
                        binupfile(file,html['uploadUrl'],timea+'_'+i, upbigfilename);
                    }
                }
                if (xhr1.status==409) {
                    td2.innerHTML='md5: '+filemd5;
                    tdnum = timea+'_'+i;
                    document.getElementById('upfile_td1_'+tdnum).innerHTML='<div style="color:green"><a href="/'+upbigfilename+'?preview" id="upfile_a_'+tdnum+'" target="_blank">'+document.getElementById('upfile_td1_'+tdnum).innerHTML+'</a><br><a href="/'+upbigfilename+'" id="upfile_a1_'+tdnum+'"></a>上传完成<button onclick="CopyAllDownloadUrl(\'#upfile_a1_'+tdnum+'\');" id="upfile_cpbt_'+tdnum+'"  style="display:none" >复制链接</button></div>';
                }
                if (i<files.length-1) {
                    i++;
                    getuplink(i);
                }
            }

        }
    }
    function size_format(num) {
        if (num>1024) {
            num=num/1024;
        } else {
            return num.toFixed(2) + ' B';
        }
        if (num>1024) {
            num=num/1024;
        } else {
            return num.toFixed(2) + ' KB';
        }
        if (num>1024) {
            num=num/1024;
        } else {
            return num.toFixed(2) + ' MB';
        }
        return num.toFixed(2) + ' GB';
    }
    function binupfile(file,url,tdnum,filename){
        var label=document.getElementById('upfile_td2_'+tdnum);
        var reader = new FileReader();
        var StartStr='';
        var MiddleStr='';
        var StartTime;
        var EndTime;
        var newstartsize = 0;
        if(!!file){
            var asize=0;
            var totalsize=file.size;
            var xhr2 = new XMLHttpRequest();
            xhr2.open("GET", url);
                    //xhr2.setRequestHeader('x-requested-with','XMLHttpRequest');
            xhr2.send(null);
            xhr2.onload = function(e){
                if (xhr2.status==200) {
                    var html = JSON.parse(xhr2.responseText);
                    var a = html['nextExpectedRanges'][0];
                    newstartsize = Number( a.slice(0,a.indexOf("-")) );
                    StartTime = new Date();
                    asize = newstartsize;
                    if (newstartsize==0) {
                        StartStr='开始于:' +StartTime.toLocaleString()+'<br>' ;
                    } else {
                        StartStr='上次上传'+size_format(newstartsize)+ '<br>本次开始于:' +StartTime.toLocaleString()+'<br>' ;
                    }
                    var chunksize=5*1024*1024; // chunk size, max 60M. 每小块上传大小，最大60M，微软建议10M
                    if (totalsize>200*1024*1024) chunksize=10*1024*1024;
                    function readblob(start) {
                        var end=start+chunksize;
                        var blob = file.slice(start,end);
                        reader.readAsArrayBuffer(blob);
                    }
                    readblob(asize);

                    reader.onload = function(e){
                        var binary = this.result;
                        var xhr = new XMLHttpRequest();
                        xhr.open("PUT", url, true);
                        //xhr.setRequestHeader('x-requested-with','XMLHttpRequest');
                        bsize=asize+e.loaded-1;
                        xhr.setRequestHeader('Content-Range', 'bytes ' + asize + '-' + bsize +'/'+ totalsize);
                        xhr.upload.onprogress = function(e){
                            if (e.lengthComputable) {
                                var tmptime = new Date();
                                var tmpspeed = e.loaded*1000/(tmptime.getTime()-C_starttime.getTime());
                                var remaintime = (totalsize-asize-e.loaded)/tmpspeed;
                                label.innerHTML=StartStr+'上传 ' +size_format(asize+e.loaded)+ ' / '+size_format(totalsize) + ' = ' + ((asize+e.loaded)*100/totalsize).toFixed(2) + '% 平均速度:'+size_format((asize+e.loaded-newstartsize)*1000/(tmptime.getTime()-StartTime.getTime()))+'/s<br>即时速度 '+size_format(tmpspeed)+'/s 预计还要 '+remaintime.toFixed(1)+'s';
                            }
                        }
                        var C_starttime = new Date();
                        xhr.onload = function(e){
                            if (xhr.status<500) {
                            var response=JSON.parse(xhr.responseText);
                            if (response['size']>0) {
                                // contain size, upload finish. 有size说明是最终返回，上传结束
                                var xhr3 = new XMLHttpRequest();
                                xhr3.open("GET", '?action=del_upload_cache&filelastModified='+file.lastModified+'&filesize='+file.size+'&filename='+filename);
                                xhr3.setRequestHeader('x-requested-with','XMLHttpRequest');
                                xhr3.send(null);
                                xhr3.onload = function(e){
                                    console.log(xhr3.responseText+','+xhr3.status);
                                }
                                EndTime=new Date();
                                MiddleStr = '结束于:'+EndTime.toLocaleString()+'<br>';
                                if (newstartsize==0) {
                                    MiddleStr += '平均速度:'+size_format(totalsize*1000/(EndTime.getTime()-StartTime.getTime()))+'/s<br>';
                                } else {
                                    MiddleStr += '本次平均速度:'+size_format((totalsize-newstartsize)*1000/(EndTime.getTime()-StartTime.getTime()))+'/s<br>';
                                }
                                document.getElementById('upfile_td1_'+tdnum).innerHTML='<div style="color:green"><a href="/'+(file.webkitRelativePath||response.name)+'?preview" id="upfile_a_'+tdnum+'" target="_blank">'+document.getElementById('upfile_td1_'+tdnum).innerHTML+'</a><br><a href="/'+(file.webkitRelativePath||response.name)+'" id="upfile_a1_'+tdnum+'"></a>上传完成<button onclick="CopyAllDownloadUrl(\'#upfile_a1_'+tdnum+'\');" id="upfile_cpbt_'+tdnum+'"  style="display:none" >复制链接</button></div>';
                                label.innerHTML=StartStr+MiddleStr;
                                uploadbuttonshow();

                                response.name=file.webkitRelativePath||response.name;
                                addelement(response);

                            } else {
                                if (!response['nextExpectedRanges']) {
                                    label.innerHTML='<font color="red">'+xhr.responseText+'</font><br>';
                                } else {
                                    var a=response['nextExpectedRanges'][0];
                                    asize=Number( a.slice(0,a.indexOf("-")) );
                                    readblob(asize);
                                }
                            } } else readblob(asize);
                        }
                        xhr.send(binary);
                    }
                } else {
                    if (window.location.pathname.indexOf('%23')>0||filename.indexOf('%23')>0) {
                        label.innerHTML='<font color="red">目录或文件名含有#，上传失败。</font>';
                    } else {
                        label.innerHTML='<font color="red">'+xhr2.responseText+'</font>';
                    }
                    uploadbuttonshow();
                }
            }
        }
    }


    function operatediv_close(operate) {
        document.getElementById(operate+'_div').style.display='none';
        document.getElementById('mask').style.display='none';
    }

    function logout() {
        document.cookie = "admin=; path=/";
        location.href = location.href;
    }

    function showdiv(event,action,num) {
        var $operatediv=document.getElementsByName('operatediv');
        for ($i=0;$i<$operatediv.length;$i++) {
            $operatediv[$i].style.display='none';
        }
        document.getElementById('mask').style.display='';
        //document.getElementById('mask').style.width=document.documentElement.scrollWidth+'px';
        document.getElementById('mask').style.height=document.documentElement.scrollHeight<window.innerHeight?window.innerHeight:document.documentElement.scrollHeight+'px';
        if (num=='') {
            var str='';
        } else {
            var str=document.getElementById('file_a'+num).innerText;
            if (str=='') {
                str=document.getElementById('file_a'+num).getElementsByTagName("img")[0].alt;
                if (str=='') {
                    alert('获取文件名失败！');
                    operatediv_close(action);
                    return;
                }
            }
            if (str.substr(-1)==' ') str=str.substr(0,str.length-1);
        }
        document.getElementById(action + '_div').style.display='';
        document.getElementById(action + '_label').innerText=str;//.replace(/&/,'&amp;');
        document.getElementById(action + '_sid').value=num;
        document.getElementById(action + '_hidden').value=str;
        if (action=='rename') document.getElementById(action + '_input').value=str;
        var $e = event || window.event;
        var $scrollX = document.documentElement.scrollLeft || document.body.scrollLeft;
        var $scrollY = document.documentElement.scrollTop || document.body.scrollTop;
        var $x = $e.pageX || $e.clientX + $scrollX;
        var $y = $e.pageY || $e.clientY + $scrollY;
        if (action=='create') {
            document.getElementById(action + '_div').style.left=(document.body.clientWidth-document.getElementById(action + '_div').offsetWidth)/2 +'px';
            document.getElementById(action + '_div').style.top=(window.innerHeight-document.getElementById(action + '_div').offsetHeight)/2+$scrollY +'px';
        } else {
            if ($x + document.getElementById(action + '_div').offsetWidth > document.body.clientWidth) {
                if (document.getElementById(action + '_div').offsetWidth > document.body.clientWidth) {
                    document.getElementById(action + '_div').offsetWidth=document.body.clientWidth+'px';
                    document.getElementById(action + '_div').style.left='0px';
                } else {
                    document.getElementById(action + '_div').style.left=document.body.clientWidth-document.getElementById(action + '_div').offsetWidth+'px';
                }
            } else {
                document.getElementById(action + '_div').style.left=$x+'px';
            }
            document.getElementById(action + '_div').style.top=$y+'px';
        }
        document.getElementById(action + '_input').focus();
    }
    function submit_operate(str) {
        var num=document.getElementById(str+'_sid').value;
        var xhr = new XMLHttpRequest();
        xhr.open("GET", '?'+serializeForm(str+'_form'));
        xhr.setRequestHeader('x-requested-with','XMLHttpRequest');
        xhr.send(null);
        xhr.onload = function(e){
            var html;
            if (xhr.status<300) {
                console.log(xhr.status+','+xhr.responseText);
                if (str=='rename') {
                    html=JSON.parse(xhr.responseText);
                    var file_a = document.getElementById('file_a'+num);
                    file_a.innerText=html.name;
                    file_a.href = (file_a.href.substr(-8)=='?preview')?(html.name.replace(/#/,'%23')+'?preview'):(html.name.replace(/#/,'%23')+'/');
                }
                if (str=='move'||str=='delete') document.getElementById('tr'+num).parentNode.removeChild(document.getElementById('tr'+num));
                if (str=='create') {
                    html=JSON.parse(xhr.responseText);
                    addelement(html);
                }
            } else alert(xhr.status+'\n'+xhr.responseText);
            document.getElementById(str+'_div').style.display='none';
            document.getElementById('mask').style.display='none';
        }
        return false;
    }
    function addelement(html) {
        var tr1=document.createElement('tr');
        tr1.setAttribute('data-to',1);
        var td1=document.createElement('td');
        td1.setAttribute('class','file');
        var a1=document.createElement('a');
        a1.href='/'+html.name.replace(/#/,'%23');
        a1.innerText=html.name;
        a1.target='_blank';
        var td2=document.createElement('td');
        td2.setAttribute('class','updated_at');
        td2.innerText=html.lastModifiedDateTime.replace(/T/,' ').replace(/Z/,'');
        var td3=document.createElement('td');
        td3.setAttribute('class','size');
        td3.innerText=size_format(html.size);
        if (!!html.folder) {
            a1.href+='/';
            document.getElementById('tr0').parentNode.insertBefore(tr1,document.getElementById('tr0').nextSibling);
        }
        if (!!html.file) {
            a1.href+='?preview';
            a1.name='filelist';
            document.getElementById('tr0').parentNode.appendChild(tr1);
        }
        tr1.appendChild(td1);
        td1.appendChild(a1);
        tr1.appendChild(td2);
        tr1.appendChild(td3);
    }
    function getElements(formId) {
        var form = document.getElementById(formId);
        var elements = new Array();
        var tagElements = form.getElementsByTagName('input');
        for (var j = 0; j < tagElements.length; j++){
            elements.push(tagElements[j]);
        }
        var tagElements = form.getElementsByTagName('select');
        for (var j = 0; j < tagElements.length; j++){
            elements.push(tagElements[j]);
        }
        var tagElements = form.getElementsByTagName('textarea');
        for (var j = 0; j < tagElements.length; j++){
            elements.push(tagElements[j]);
        }
        return elements;
    }
    function serializeElement(element) {
        var method = element.tagName.toLowerCase();
        var parameter;
        if (method == 'select') {
            parameter = [element.name, element.value];
        }
        switch (element.type.toLowerCase()) {
            case 'submit':
            case 'hidden':
            case 'password':
            case 'text':
            case 'date':
            case 'textarea':
                parameter = [element.name, element.value];
                break;
            case 'checkbox':
            case 'radio':
                if (element.checked){
                    parameter = [element.name, element.value];
                }
                break;
        }
        if (parameter) {
            var key = encodeURIComponent(parameter[0]);
            if (key.length == 0) return;
            if (parameter[1].constructor != Array) parameter[1] = [parameter[1]];
            var values = parameter[1];
            var results = [];
            for (var i = 0; i < values.length; i++) {
                results.push(key + '=' + encodeURIComponent(values[i]));
            }
            return results.join('&');
        }
    }
    function serializeForm(formId) {
        var elements = getElements(formId);
        var queryComponents = new Array();
        for (var i = 0; i < elements.length; i++) {
            var queryComponent = serializeElement(elements[i]);
            if (queryComponent) {
                queryComponents.push(queryComponent);
            }
        }
        return queryComponents.join('&');
    }



</script>





