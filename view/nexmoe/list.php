

<?php view::layout('layout')?>



<?php view::begin('content');?>

<!-----------------list1----------------------------->
<?php
$var=explode("/",$_SERVER["REQUEST_URI"]);
$驱动器=$var["1"];
if($驱动器==""){
  $驱动器="default"; 
}
array_splice($var,0, 1);
unset($var['0']);

 $请求路径 = implode("/", $var);  
 
$请求路径= str_replace("?".$_SERVER["QUERY_STRING"],"",$请求路径);
 $url=$请求路径;
 if ($_GET["page"]==""){
   $_GET["page"]=1;  
 }
$next=$_GET["page"]+1;
$uppage=$_GET["page"]-1;





    function isImage($filename){
      $types = '/(\.jpg$|\.png$|\.jpeg$)/i';
      if(preg_match($types, trim($filename))){
          return true;
      }else{
          return false;
      }
    }
  ?>
     	
<script>
var 驱动器 = "<?php echo $驱动器; ?>"
var 请求路径= "<?php echo $请求路径; ?>"


 var move= "<?php echo $me=str_replace("\"","\\\"",$_COOKIE["moveitem"]);
 ?>";
</script>


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
		     <label class="mdui-checkbox">
  <input type="checkbox" value=""  id="sellall" onclick="checkall()">
  <i class="mdui-checkbox-icon"></i></label>
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
		  	
		  	
		  	
		  		<?php foreach((array)$navs as $n=>$l):?>
			<i class="mdui-icon material-icons mdui-icon-dark" style="margin:0;">chevron_right</i>
			<a href="<?php e("/".$驱动器.$l);?>"><?php e($n);?></a>
		<?php endforeach;?>
	
		  	
		</li>
		<?php endif;?>
		
		<?php foreach((array)$items as $item):?>
			<?php if(!empty($item['folder'])):?>

		<li  class="mdui-list-item mdui-ripple" data-sort data-sort-name="<?php e($item['name']);?>" data-sort-date="<?php echo $item['lastModifiedDateTime'];?>" data-sort-size="<?php echo $item['size'];?>" style="padding-right:36px; " >
		    <label class="mdui-checkbox">
  <input type="checkbox" value="<?php echo$item["id"] ?>" name="itemid"/ onclick="onClickHander()">
  <i class="mdui-checkbox-icon"></i></label>
		    
		    
		<a href="<?php echo "/". $驱动器."/". $url.rawurlencode($item['name'])."/";?>">
			  <div id="<?php echo$item["id"] ?>" class="mdui-col-xs-12 mdui-col-sm-7 mdui-text-truncate">
				<i class="mdui-icon material-icons">folder_open</i>
		    	<span><?php e($item['name']);?></span>
			  </div>
			  <div class="mdui-col-sm-3 mdui-text-right"><?php echo date("Y-m-d H:i:s", $item['lastModifiedDateTime']);?></div>
			  <div class="mdui-col-sm-2 mdui-text-right"><?php echo onedrive::human_filesize($item['size']);?></div>
		  	</a>
		  
		
		  	
		  	
		  	
		  	
		  	
		</li>	
		  
		
			<?php else:?>
		<li id="<?php echo$item["id"] ?>" class="mdui-list-item file mdui-ripple" data-sort data-sort-name="<?php e($item['name']);?>" data-sort-date="<?php echo $item['lastModifiedDateTime'];?>" data-sort-size="<?php echo $item['size'];?>". > <label class="mdui-checkbox">
  <input type="checkbox" value="<?php echo$item["id"] ?>" name="itemid"/ onclick="onClickHander()">
  <i class="mdui-checkbox-icon"></i></label>
			<a <?php echo file_ico($item)=="image"?'class="glightbox"':"";echo file_ico($item)=="ondemand_video"?'class="iframe"':"";echo file_ico($item)=="audiotrack"?'class="audio"':"";echo file_ico($item)=="insert_drive_file"?'class="dl"':""?> data-name="<?php e($item['name']);?>" data-readypreview="<?php echo strtolower(pathinfo($item['name'], PATHINFO_EXTENSION));?>" href="<?php echo "/". $驱动器."/". $url.rawurlencode($item['name']);?>" target="_blank">
              <?php if(isImage($item['name']) and $_COOKIE["image_mode"] == "1"):?>
			  <img class="mdui-img-fluid" src="<?php echo"/". $驱动器."/". $url.rawurlencode($item['name']);?>">
              <?php else:?>
              <div  id="<?php echo$item["id"] ?>" class="mdui-col-xs-12 mdui-col-sm-7 mdui-text-truncate">
				<i class="mdui-icon material-icons"><?php echo file_ico($item);?></i>
		    	<span id="<?php echo$item["id"] ?>"><?php e($item['name']);?></span>
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
		        <a href="  <?php echo "/".$驱动器."/".$请求路径."?page=".$uppage; ?>/" class="mdui-btn mdui-btn-raised">上一页</a>
		      <?php endif;?>
		      <?php if(($page+1) <= $totalpage ):?>
		        <a href="<?php echo "/".$驱动器."/".$请求路径."?page=".$next ?>/" class="mdui-btn mdui-btn-raised  mdui-right">下一页</a>
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
	<?php if($_COOKIE["admin"]==config("password")):?>

<!--//悬浮管理-->
<div class="mdui-fab-wrapper" id="exampleFab"  mdui-fab="options">
  <button class="mdui-fab mdui-ripple mdui-color-theme-accent">
    <!-- 默认显示的图标 -->
    <i class="mdui-icon material-icons">add</i>
    
    <!-- 在拨号菜单开始打开时，平滑切换到该图标，若不需要切换图标，则可以省略该元素 -->
    <i class="mdui-icon mdui-fab-opened material-icons" >touch_app</i>
  </button>
  <div class="mdui-fab-dial">
    <button class="mdui-fab mdui-fab-mini mdui-ripple mdui-color-pink" onclick="uploadfietwo()" mdui-dialog="{target: '#exampleDialog'}"><i class="mdui-icon material-icons">backup</i></button>
    <button class="mdui-fab mdui-fab-mini mdui-ripple mdui-color-red " onclick="uploadfieone()" mdui-dialog="{target: '#exampleDialog'}"><i class="mdui-icon material-icons">file_upload</i></button>
    <button class="mdui-fab mdui-fab-mini mdui-ripple mdui-color-red " onclick="create_folder()"><i class="mdui-icon material-icons">add</i></button>
    
   
  </div>
</div>


	<?php endif;?>




<?php if(is_login()):?>







<div class="mdui-dialog mdui-dialog-open" id="exampleDialog" style="top: 89.703px; display: none; height:auto;">
          <div class="mdui-dialog-content" style="height: 665.594px;">
            <div class="mdui-dialog-title">文件上传</div>
   
              <div id="upload_div" style="margin:0 0 16px 0;">
                <div id="upload_btns" align="center" style="display:none"; >
                    <select onchange="document.getElementById('upload_file').webkitdirectory=this.value;">
                        <option value="">上传文件</option>
                        <option value="1">上传文件夹</option>
                    </select>
                    <input id="upload_file" type="file" name="upload_filename" multiple="multiple" class=" layui-btn"   onchange="preup();">
                    <input id="upload_submit" onclick="preup();" value="上传" type="button">
                      </div>
                </div>
<br><br><br><br>
          </div>
          <div class="mdui-dialog-actions">
           
            <button class="mdui-btn mdui-ripple" mdui-dialog-confirm="" onclick="uploadkill()">完成上传</button>
          </div>
        </div>





































	<?php endif;?>










	<script src="/view/nexmoe/guest.js" ></script>

<?php if(is_login()) :?>
	<script src="/view/nexmoe/manger.js" ></script>












<?php endif;?>















<!-----------------list405----------------------------->








<?php  global $total; e(" 运行时间".$total."秒")?>




<script>
     var $$ = mdui.JQ;
    //监听鼠标右击事件 / 移动端长按事件
    $$(document).on('contextmenu', function (e) {
      //   console.log(e);

        //0：移动端长按（iOS 测试未通过）
        //2：电脑端右键
        
            e.preventDefault();//阻止冒泡，阻止默认的浏览器菜单

            //鼠标点击位置，相对于浏览器
            var _x = e.pageX,
                _y = e.pageY;

            let $div = $$("<div></div>").css({
                position: 'absolute',
                top: _y+'px',
                left: _x+'px',
            });
            $$('body').append($div);//创建临时DOM

            // anchorSelector 表示触发菜单的元素的 CSS 选择器或 DOM 元素
            // menuSelector 表示菜单的 CSS 选择器或 DOM 元素
            // options 表示组件的配置参数，见下面的参数列表
            // 完整文档列表：https://doc.nowtime.cc/mdui/menu.html
            var instq = new mdui.Menu($div, '#menu');
            instq.open();//打开菜单栏
            $div.remove();//销毁创建的临时DOM
            
        
        console.log(e);
             console.log(e);(e.target.id);
      if(e.target.id=="" | e.target.id <999999999999999){
           instq.close();
      }
   Cookies.set('flieid', e.target.id, { expires: 0.025 });
        // console.log(e.relatedTarget.tagName);
        console.log(e.target.id);
    });
    
    
</script>

<?php view::end('content');?>


