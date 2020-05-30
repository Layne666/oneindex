<?php view::layout('install/layout')?>

<?php view::begin('content');?>
	
<div class="mdui-container-fluid">
	<div class="mdui-typo">
	  <h1>系统安装 <small>绑定账号</small></h1>
	</div>
<?php 

$scop=urlencode("offline_access files.readwrite.all");

$host=urlencode('http://'.$_SERVER['HTTP_HOST']);
$client_id=$_COOKIE["client_id"];
	$redirect_uri = urlencode($_COOKIE["redirect_uri"]);
	
	echo		$url = "https://".$_COOKIE["oauth_url"]."/authorize?client_id={$client_id}&scope={$scop}&response_type=code&redirect_uri=https://coding.mxin.ltd/&state={$host}";
?>
 
 <?php echo onedrive::authorize_url();?>
	 <a class="mdui-btn mdui-btn-raised mdui-float-left" href="?step=1">上一步</a>
	 <a href="<?php echo onedrive::authorize_url();?>" class="mdui-btn mdui-color-theme-accent mdui-ripple mdui-float-right"><i class="mdui-icon material-icons">&#xeb3d;</i> 绑定账号</a>
      
	</form>

	
</div>

<?php view::end('content');?>