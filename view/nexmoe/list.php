<?php view::layout('layout')?>
<?php 
if ($_SERVER["REQUEST_URI"]=="/")
{
   header("location:/default");
}
////
/////////
$var=explode("/",$_SERVER["REQUEST_URI"]);
$驱动器=$var["1"];
array_splice($var,0, 1);
unset($var['0']);

 $请求路径 = implode("/", $var);  
 
$请求路径= str_replace("?".$_SERVER["QUERY_STRING"],"",$请求路径);
 $url=$请求路径;


//////////





    function isImage($filename){
      $types = '/(\.jpg$|\.png$|\.jpeg$)/i';
      if(preg_match($types, trim($filename))){
          return true;
      }else{
          return false;
      }
    }
  ?>
<?php 
function file_ico($item){
  $ext = strtolower(pathinfo($item['name'], PATHINFO_EXTENSION));
  if(in_array($ext,['bmp','jpg','jpeg','png','gif','webp'])){
  	return "image";
  }
  if(in_array($ext,['mp4','mkv','webm','avi','mpg', 'mpeg', 'rm', 'rmvb', 'mov', 'wmv', 'mkv', 'asf', 'flv', 'm3u8'])){
  	return "ondemand_video";
  }
  if(in_array($ext,['ogg','mp3','wav','flac','aac','m4a','ape'])){
  	return "audiotrack";
  }
  return "insert_drive_file";
}
?>

<?php view::begin('content');?>
	
<div class="mdui-container-fluid">
<?php if($head):?>
<div class="mdui-typo" style="padding: 20px;">
	<?php e($head);?>
</div>
<?php endif;?>
<style>
.thumb .th{
	display: none;
}
.thumb .mdui-text-right{
	display: none;
}
.thumb .mdui-list-item a ,.thumb .mdui-list-item {
	width:213px;
	height: 230px;
	float: left;
	margin: 10px 10px !important;
}

.thumb .mdui-col-xs-12,.thumb .mdui-col-sm-7{
	width:100% !important;
	height:230px;
}

.thumb .mdui-list-item .mdui-icon{
	font-size:100px;
	display: block;
	margin-top: 40px;
	color: #7ab5ef;
}
.thumb .mdui-list-item span{
	float: left;
	display: block;
	text-align: center;
	width:100%;
	position: absolute;
    top: 180px;
}
.thumb .forcedownload {
    display: none; 
</style>

<div class="nexmoe-item">
<div class="mdui-row">
	<ul class="mdui-list">
		<li class="mdui-list-item th" style="padding-right:36px;">
		  <div class="mdui-col-xs-12 mdui-col-sm-7">文件 <i class="mdui-icon material-icons icon-sort" data-sort="name" data-order="downward">expand_more</i></div>
		  <div class="mdui-col-sm-3 mdui-text-right">修改时间 <i class="mdui-icon material-icons icon-sort" data-sort="date" data-order="downward">expand_more</i></div>
		  <div class="mdui-col-sm-2 mdui-text-right">大小 <i class="mdui-icon material-icons icon-sort" data-sort="size" data-order="downward">expand_more</i></div>
		</li>
		<?php if($path != '/'):?>
		<li class="mdui-list-item mdui-ripple">
			<a href="<?php echo "/". $驱动器."/". $url.'../';?>">
			    
			    
			    
			    
			    
			  <div class="mdui-col-xs-12 mdui-col-sm-7">
				<i class="mdui-icon material-icons">arrow_upward</i>
		    	..
			  </div>
			  <div class="mdui-col-sm-3 mdui-text-right"></div>
			  <div class="mdui-col-sm-2 mdui-text-right"></div>
		  	</a>
		</li>
		<?php endif;?>
		
		<?php foreach((array)$items as $item):?>
			<?php if(!empty($item['folder'])):?>

		<li class="mdui-list-item mdui-ripple" data-sort data-sort-name="<?php e($item['name']);?>" data-sort-date="<?php echo $item['lastModifiedDateTime'];?>" data-sort-size="<?php echo $item['size'];?>" style="padding-right:36px;">
		    
		    
		<a href="<?php echo "/". $驱动器."/". $url.rawurlencode($item['name']);?>">
			  <div class="mdui-col-xs-12 mdui-col-sm-7 mdui-text-truncate">
				<i class="mdui-icon material-icons">folder_open</i>
		    	<span><?php e($item['name']);?></span>
			  </div>
			  <div class="mdui-col-sm-3 mdui-text-right"><?php echo date("Y-m-d H:i:s", $item['lastModifiedDateTime']);?></div>
			  <div class="mdui-col-sm-2 mdui-text-right"><?php echo onedrive::human_filesize($item['size']);?></div>
		  	</a>
		</li>
			<?php else:?>
		<li class="mdui-list-item file mdui-ripple" data-sort data-sort-name="<?php e($item['name']);?>" data-sort-date="<?php echo $item['lastModifiedDateTime'];?>" data-sort-size="<?php echo $item['size'];?>">
			<a <?php echo file_ico($item)=="image"?'class="glightbox"':"";echo file_ico($item)=="ondemand_video"?'class="iframe"':"";echo file_ico($item)=="audiotrack"?'class="audio"':"";echo file_ico($item)=="insert_drive_file"?'class="dl"':""?> data-name="<?php e($item['name']);?>" data-readypreview="<?php echo strtolower(pathinfo($item['name'], PATHINFO_EXTENSION));?>" href="<?php echo "/". $驱动器."/". $url.rawurlencode($item['name']);?>" target="_blank">
              <?php if(isImage($item['name']) and $_COOKIE["image_mode"] == "1"):?>
			  <img class="mdui-img-fluid" src="<?php echo"/". $驱动器."/". $url.rawurlencode($item['name']);?>">
              <?php else:?>
              <div class="mdui-col-xs-12 mdui-col-sm-7 mdui-text-truncate">
				<i class="mdui-icon material-icons"><?php echo file_ico($item);?></i>
		    	<span><?php e($item['name']);?></span>
			  </div>
			  <div class="mdui-col-sm-3 mdui-text-right"><?php echo date("Y-m-d H:i:s", $item['lastModifiedDateTime']);?></div>
			  <div class="mdui-col-sm-2 mdui-text-right"><?php echo onedrive::human_filesize($item['size']);?>
			  
			  </div>
              <?php endif;?>
		  	</a>
		  	
			<div class="forcedownload "  >
 			      <a title="直接下载" href="<?php echo "/". $驱动器."/". $url.rawurlencode($item['name']);?>">
			          <button class="mdui-btn mdui-ripple mdui-btn-icon"><i class="mdui-icon material-icons">file_download</i></button>
			      </a>
			</div>



		</li>
			<?php endif;?>
		<?php endforeach;?>

		  <?php if($totalpage > 1 ):?>
		  <li class="mdui-list-item th">
		    <div class="mdui-col-sm-6 mdui-left mdui-text-left">
		      <?php if(($page-1) >= 1 ):?>
		        <a href="<?php echo preg_replace('/\/$/', '', "$root"); ?><?php e($path) ?>.page-<?php e($page-1) ?>/" class="mdui-btn mdui-btn-raised">上一页</a>
		      <?php endif;?>
		      <?php if(($page+1) <= $totalpage ):?>
		        <a href="<?php echo preg_replace('/\/$/', '', "$root"); ?><?php e($path) ?>.page-<?php e($page+1) ?>/" class="mdui-btn mdui-btn-raised">下一页</a>
		      <?php endif;?>
		    </div>
		    <div class="mdui-col-sm-6 mdui-right mdui-text-right">
		      <div class="mdui-right mdui-text-right"><span class="mdui-chip-title">Page: <?php e($page);?>/<?php e($totalpage);?></span></div>
		    </div>
		  </li>
		  <?php endif;?>
	</ul>
</div>
</div>
<?php if($readme):?>
<div class="mdui-typo mdui-shadow-3" style="padding: 20px;margin: 20px; 0">
	<div class="mdui-chip">
	  <span class="mdui-chip-icon"><i class="mdui-icon material-icons">face</i></span>
	  <span class="mdui-chip-title">README.md</span>
	</div>
	<?php e($readme);?>
</div>
<?php endif;?>
</div>
<script src="//cdn.jsdelivr.net/gh/mcstudios/glightbox/dist/js/glightbox.min.js"></script>
<script src="//cdn.jsdelivr.net/npm/aplayer/dist/APlayer.min.js"></script>
<script>
var $$ = mdui.JQ;
$$(function() {
    $$('.file .iframe').each(function() {
        $$(this).on('click', function() {
            layer.open({
              type: 2,
              title: '<a target="_blank" href="'+$$(this).attr('href')+"?s"+'">'+ $$(this).find('span').text()+'(点击新窗口打开)</a>', //如伪静态去除了/?/,需把"&s=1"改为"?s",或者改为以post请求这个链接//jia
              //shadeClose: true,
              move: false,
              shade: false,
              maxmin: true, 
              area: ['100%', '100%'],
              content: $$(this).attr('href')+"?s" //如伪静态去除了/?/,需把"&s=1"改为"?s",或者改为以post请求这个链接//le
              ,min: function(layero){
                  //zi;  
                  layero.css({top: '90%'})
              }
            });
            return false;
        });
    });
	$('.file .dl').each(function () {
        $(this).on('click', function () {
            var form = $('<form target=_blank method=post></form>').attr('action', $(this).attr('href')).get(0);
            $(document.body).append(form);
            form.submit();
            $(form).remove();
            return false;
        });
    });
}); 
window.TC=window.TC||{};
jQuery(".file .audio").click(function(e){
            e.preventDefault();
            TC.preview_audio(this);
});
TC.preview_audio = function(aud){
    if(!TC.aplayer){
        TC.aplayerList=[];
        jQuery(".file .audio").each(function(){
            var ext = jQuery(this).data("readypreview");
                var n = jQuery(this).find("span").text();
                var l = n.replace("."+ext,".lrc");
                var la = jQuery('a[data-name="'+l+'"]');
                var lrc = undefined;
                if(la.length>0){
                    lrc = la[0].href+"?s";
                }
                TC.aplayerList.push({
                    name:n,
                    url:this.href,
                    artist:" ",
                    lrc:lrc
                });
        })
        jQuery('<div id="aplayer">').appendTo("body");
        TC.aplayer = new APlayer({
            container: document.getElementById('aplayer'),
            fixed: true,
            audio: TC.aplayerList,
            lrcType: 3
        });
    }
    var k=-1;
    for(var i in TC.aplayerList){
        if(TC.aplayerList[i].name==jQuery(aud).data("name")){
            k=i;
            break;
        }
    }
    if(k>=0){
        TC.aplayer.list.switch(k);
        TC.aplayer.play();
        TC.aplayer.setMode("normal");
    }
}
	
$ = mdui.JQ;
$.fn.extend({
    sortElements: function (comparator, getSortable) {
        getSortable = getSortable || function () { return this; };

        var placements = this.map(function () {
            var sortElement = getSortable.call(this),
                parentNode = sortElement.parentNode,
                nextSibling = parentNode.insertBefore(
                    document.createTextNode(''),
                    sortElement.nextSibling
                );

            return function () {
                parentNode.insertBefore(this, nextSibling);
                parentNode.removeChild(nextSibling);
            };
        });

        return [].sort.call(this, comparator).each(function (i) {
            placements[i].call(getSortable.call(this));
        });
    }
});
var lightbox = GLightbox();
function downall() {
     let dl_link_list = Array.from(document.querySelectorAll("li a"))
         .map(x => x.href) // 所有list中的链接
         .filter(x => x.slice(-1) != "/"); // 筛选出非文件夹的文件下载链接

     let blob = new Blob([dl_link_list.join("\r\n")], {
         type: 'text/plain'
     }); // 构造Blog对象
     let a = document.createElement('a'); // 伪造一个a对象
     a.href = window.URL.createObjectURL(blob); // 构造href属性为Blob对象生成的链接
     a.download = "folder_download_link.txt"; // 文件名称，你可以根据你的需要构造
     a.click() // 模拟点击
     a.remove();
}

function thumb(){
	if($('#thumb i').text() == "apps"){
		$('#thumb i').text("format_list_bulleted");
		$('.nexmoe-item').removeClass('thumb');
		$('.nexmoe-item .mdui-icon').show();
		$('.nexmoe-item .mdui-list-item').css("background","");
	}else{
		$('#thumb i').text("apps");
		$('.nexmoe-item').addClass('thumb');
		$('.mdui-col-xs-12 i.mdui-icon').each(function(){
			if($(this).text() == "image" || $(this).text() == "ondemand_video"){
				var href = $(this).parent().parent().attr('href');
				var thumb =(href.indexOf('?') == -1)?'?t=220':'&t=220';
				$(this).hide();
				$(this).parent().parent().parent().css("background","url("+href+thumb+")  no-repeat center top");
			}
		});
	}

}	

$(function(){


	$('.icon-sort').on('click', function () {
        let sort_type = $(this).attr("data-sort"), sort_order = $(this).attr("data-order");
        let sort_order_to = (sort_order === "less") ? "more" : "less";

        $('li[data-sort]').sortElements(function (a, b) {
            let data_a = $(a).attr("data-sort-" + sort_type), data_b = $(b).attr("data-sort-" + sort_type);
            let rt = data_a.localeCompare(data_b, undefined, {numeric: true});
            return (sort_order === "more") ? 0-rt : rt;
        });

        $(this).attr("data-order", sort_order_to).text("expand_" + sort_order_to);
    });

  	
  
});
  
var ckname='image_mode';
function getCookie(name) 
{
    var arr,reg=new RegExp("(^| )"+name+"=([^;]*)(;|$)");
    if(arr=document.cookie.match(reg))
        return unescape(arr[2]); 
    else
        return null; 
} 
function setCookie(key,value,day){
	var exp = new Date(); 
	exp.setTime(exp.getTime() - 1); 
	var cval=getCookie(key); 
	if(cval!=null) 
	document.cookie= key + "="+cval+";expires="+exp.toGMTString(); 
	var date = new Date();
	var nowDate = date.getDate();
	date.setDate(nowDate + day);
	var cookie = key+"="+value+"; expires="+date;
	document.cookie = cookie;
	return cookie;
}
$('#image_view').on('click', function () {
	if($(this).prop('checked') == true){
		setCookie(ckname,1,1);
		window.location.href=window.location.href;
	}else{
		setCookie(ckname,0,1);
		window.location.href=window.location.href;
	}
});
  
</script>
<?php view::end('content');?>