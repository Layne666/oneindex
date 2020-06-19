///////////////////文件管理模块///////////////////////////////////////////
 

///////文件上传
function uploadfieone(){
    document.getElementById('upload_file').webkitdirectory="";
    document.getElementById("upload_file").click();
    }
//////文件夹上传    
function uploadfietwo(){
    document.getElementById('upload_file').webkitdirectory=1;
    document.getElementById("upload_file").click();
  }
//新建文件夹
function create_folder()
  {
     mdui.prompt('新建文件夹',
    function (value) {
        var url="/"+驱动器+"/"+请求路径;
        var xhr4 = new XMLHttpRequest();
        xhr4.withCredentials = true;
        xhr4.addEventListener("readystatechange", function() {
        if(this.readyState === 4) {
        
    console.log(this.responseText);
    deldel()}
    });
xhr4.open("GET", ""+"?create_folder="+value);
xhr4.send();
console.log(xhr4);
 
  },
  function (value) {
   
  }
);
      
  }
//单文件删除
function delitem(){
    
    var id = Cookies.get('flieid')
   
    data=JSON.stringify(id);
    alert("确认删除"+data)
    var myHeaders = new Headers();
    myHeaders.append("Content-Type", "application/json");
    var raw = data;
    var requestOptions = {
    method: 'DELETE',
    headers: myHeaders,
    body: raw,
    redirect: 'follow'
};

fetch("/"+驱动器+"/?action=dellist", requestOptions)
  .then(response => response.text()
  )
  .then(data=>{alert(data);
      deldel();//清空缓存
  })
 
  .then(result => console.log(result))
  .catch(error => console.log('error', error));
    
}
    
    ////批量删除文件函数
function dellistitem(){
    obj = document.getElementsByName("itemid");
     check_val = [];
    for(k in obj){
        if(obj[k].checked)
            check_val.push(obj[k].value);
    }
   
    console.log(check_val);
    data=JSON.stringify(check_val);
    alert("确认删除"+data)
    var myHeaders = new Headers();
    myHeaders.append("Content-Type", "application/json");
    var raw = data;
    var requestOptions = {
    method: 'DELETE',
    headers: myHeaders,
    body: raw,
    redirect: 'follow'
};

fetch("/"+驱动器+"/?action=dellist", requestOptions)
  .then(response => response.text()
  )
  .then(data=>{alert(data);
      deldel();//清空缓存
  })
 
  .then(result => console.log(result))
  .catch(error => console.log('error', error));
    
}

/////文件重命名   
function renamebox(){
   mdui.prompt('重命名',
    function (value) {
        var id = Cookies.get('flieid')
        var xhr4 = new XMLHttpRequest();
        xhr4.withCredentials = true;
        xhr4.addEventListener("readystatechange", function() {
        if(this.readyState === 4) {
    console.log(this.responseText);deldel()}
    });
xhr4.open("GET", "/"+驱动器+"/?rename="+id+"&name="+value);
xhr4.send();
console.log(xhr4);
 
  },
  function (value) {
   
  }
);
}

///剪切文件
  function moveitem(){
      obj = document.getElementsByName("itemid");

    check_val = [];
    for(k in obj){
        if(obj[k].checked)
            check_val.push(obj[k].value);
    }
    //alert(check_val);
console.log(check_val);
 data=JSON.stringify(check_val);
 alert(data);
   Cookies.set('moveitem', data, { expires: 0.025 });
    
}

///粘贴文件axios
     function pastitem(){
    var url=location+"?this=path" ;
    ajaxLoading();
    axios.get(url)
    .then(function (response) {
    dataid=(response.data)
    ajaxLoadEnd()
    var move= Cookies.get('moveitem');
       if(typeof move == "undefined") {
    return false;
    }
        alert ("粘贴"+move+"到"+dataid)
        ajaxLoading();
        var urls="/"+驱动器+"/?filemanger=move&id="+move+"&newid="+dataid;
        axios.get(urls)
        .then(function (response) {
         ajaxLoadEnd()
        deldel();
         console.log(response);
        })
        .catch(function (error) {
        console.log(error);
  });

  })
  .catch(function (error) {
    console.log(error);
  });

  
    }
    
    
    
    
////////////////文件上传函数

function ajaxLoading() {
   layer.msg('文件移动中请等待', {
  icon: 16
  ,shade: 0.1
});
    }

    function ajaxLoadEnd() {
        layer.closeAll('loading');
    }

  function uploadbuttonhide() {
        document.getElementById('exampleDialog').style.display='block';
        
    }
    function uploadkill() {
        document.getElementById('exampleDialog').style.display='none';
       deldel()
    }
    function preup() {
   
    

       // uploadbuttonhide();
        var files=document.getElementById('upload_file').files;
	    if (files.length<1) {
            uploadbuttonhide() 
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
            if (file.size>15*1024*1024*1024) {
                td2.innerHTML='<font color="red">文件过大，终止上传。</font>';
                uploadbuttonshow();
                return;
            }
            upbigfilename = encodeURIComponent((file.webkitRelativePath||file.name));

            td2.innerHTML='获取上传链接 ...';
            var xhr1 = new XMLHttpRequest();
            xhr1.open("GET", "/"+驱动器+"/"+请求路径+'?action=upbigfile&upbigfilename='+ upbigfilename +'&filesize='+ file.size +'&lastModified='+ file.lastModified);
            xhr1.setRequestHeader('x-requested-with','XMLHttpRequest');
            xhr1.send(null);
            xhr1.onload = function(e){
                td2.innerHTML='<font color="red">'+xhr1.responseText+'</font>';
                if (xhr1.status==200) {
                   
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
                    document.getElementById('upfile_td1_'+tdnum).innerHTML='<div style="color:green"><a href="/'+upbigfilename+'" id="upfile_a_'+tdnum+'" target="_blank">'+document.getElementById('upfile_td1_'+tdnum).innerHTML+'</a>上传完成';
                   
                    
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
                    if (totalsize>200*1024*1024) chunksize=100*1024*1024;
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
                                document.getElementById('upfile_td1_'+tdnum).innerHTML='<div style="color:green"><a href="/'+驱动器+"/"+请求路径+(file.webkitRelativePath||response.name)+'?preview" id="upfile_a_'+tdnum+'" target="_blank">'+document.getElementById('upfile_td1_'+tdnum).innerHTML+'</a><br><a href="/<?php echo IndexController::$驱动器."/".IndexController::$请求路径 ;?>'+(file.webkitRelativePath||response.name)+'" id="upfile_a1_'+tdnum+'"></a>上传完成</div>';
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

    
   
