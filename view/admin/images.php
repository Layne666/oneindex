<?php view::layout('layout')?>

<?php view::begin('content');?>
<div class="mdui-container-fluid">

	<div class="mdui-typo">
	  <h1> <a href="?/images/" target="_blank">图床</a> <small>OneImages</small></h1>
	</div>
	<form action="" method="post">
		<div class="mdui-textfield">
		  <h4>作为网站首页</h4>
		  <label class="mdui-textfield-label"></label>
		  <label class="mdui-switch">
			  <input type="checkbox" name="home" value="1" <?php echo empty($config['home'])?'':'checked';?>/>
			  <i class="mdui-switch-icon"></i>
		  </label>
		</div>
		<div class="mdui-textfield">
		  <h4>允许游客上传图片</h4>
		  <label class="mdui-textfield-label"></label>
		  <label class="mdui-switch">
			  <input type="checkbox" name="public" value="1" <?php echo empty($config['public'])?'':'checked';?>/>
			  <i class="mdui-switch-icon"></i>
		  </label>
		</div>
		<div class="mdui-textfield">
		  <h4>允许上传文件类型</h4>
		  <input class="mdui-textfield-input" type="text" name="exts" value="<?php echo join(' ',$config['exts']);?>"/>
		</div>
	   <Br>
	   <button type="submit" class="mdui-btn mdui-color-theme-accent mdui-ripple mdui-float-right">
	   	<i class="mdui-icon material-icons">&#xe161;</i> 保存
	   </button>
	   <Br>
	</form>
</div>
<?php view::end('content');?>