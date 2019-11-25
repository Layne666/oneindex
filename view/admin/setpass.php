<?php view::layout('layout')?>

<?php view::begin('content');?>
<div class="mdui-container-fluid">

	<div class="mdui-typo">
	  <h1> 修改密码 <small>后台密码修改(与文件夹密码无关)</small></h1>
	</div>
	<form action="" method="post">
		<div class="mdui-textfield">
		  <h4>旧密码</h4>
		  <input class="mdui-textfield-input" type="password" name="old_pass" />
		</div>

		<div class="mdui-textfield">
		  <h4>新密码</h4>
		  <input class="mdui-textfield-input" type="password" name="password" />
		</div>

		<div class="mdui-textfield">
		  <h4>重复新密码</h4>
		  <input class="mdui-textfield-input" type="password" name="password2" />
		</div>
		
	   <button type="submit" class="mdui-btn mdui-color-theme-accent mdui-ripple mdui-float-right">
	   	<i class="mdui-icon material-icons">&#xe161;</i> 保存
	   </button>
	</form>
</div>
<?php view::end('content');?>