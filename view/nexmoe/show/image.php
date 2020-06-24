<?php view::layout('layout')?>
<?php
$downloadUrl = $item['downloadUrl'];
 	if (config('proxy_domain') != ""){
 	$downloadUrl = str_replace(config('main_domain'),config('proxy_domain'),$item['downloadUrl']);
 	}else {
 		$downloadUrl = $item['downloadUrl'];
 	}
?>
<?php view::begin('content');?>


<div class="mdui-container-fluid">
    <div class="nexmoe-item">
	<img class="mdui-img-fluid mdui-center" src="<?php e($item['downloadUrl']);?>"/>
	<br>
	<div class="mdui-row">
	  <select class="mdui-select" mdui-select="{position: 'top'}" id="sel">
	    <option value="<?php e( 'https://'.$_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"]);?>" selected>下载地址</option>
	    <option value="<img src='<?php e( 'https://'.$_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"]);?>' />">引用地址(HTML)</option>
	    <option value="![](<?php e( 'https://'.$_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"]);?>)">引用地址(Markdown)</option>
	  </select>
	  <textarea class="mdui-textfield-input" id="val" readonly><?php e( 'https://'.$_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"]);?></textarea>
	</div>
	<script type="text/javascript">
	    window.onload = function() {
	        var sel = document.getElementById("sel");
	        if(sel && sel.addEventListener){
	            sel.addEventListener('change',function(e){
	                var ev = e||window.event;
	                var target = ev.target||ev.srcElement;
	                document.getElementById("val").value = target.value;
	            },false)
	        }
	    }
	</script>
    </div>
</div>

<a href="<?php e( 'https://'.$_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"]);?>" class="mdui-fab mdui-fab-fixed mdui-ripple mdui-color-theme-accent"><i class="mdui-icon material-icons">file_download</i></a>
<?php view::end('content');?>
