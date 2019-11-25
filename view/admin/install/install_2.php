<?php view::layout('install/layout')?>

<?php view::begin('content');?>
	
<div class="mdui-container-fluid">
	<div class="mdui-typo">
	  <h1>程序安装 <small>绑定微软账号</small></h1>
	</div>


	 <a class="mdui-btn mdui-btn-raised mdui-float-left" href="?step=1">上一步</a>
	 <a href="<?php echo onedrive::authorize_url();?>" class="mdui-btn mdui-color-theme-accent mdui-ripple mdui-float-right"><i class="mdui-icon material-icons">&#xeb3d;</i> 绑定账号</a>
      
	</form>

	
</div>

<?php view::end('content');?>