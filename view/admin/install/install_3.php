<?php view::layout('install/layout')?>

<?php view::begin('content');?>
	
<div class="mdui-container-fluid">
	<div class="mdui-typo">
	  <h1>系统安装 <small>完成安装</small></h1>
	</div>

<?php if($refresh_token):?>
	<h1 class='mdui-text-color-green'> 授权成功!</h1>
	<small>初始密码: <?php echo config('password');?></small>
	<br><br>
	

	
	<form action="" method="post">
	<div class=mdui-textfield mdui-textfield-floating-label>    <label class="mdui-radio">
    <input type="radio" name="sharepint" value="0">
    <i class="mdui-radio-icon"></i>
   onedrive
  </label>
  
  <label class="mdui-radio">
    <input type="radio" name="drivestype" value="1" >
    <i class="mdui-radio-icon"></i>
   sharepoint站点
  </label>
  
	<button class="mdui-btn mdui-color-theme-accent mdui-ripple mdui-float-right" type="submit">下一步</button>
	
	
	<a class="mdui-btn mdui-color-theme-accent mdui-ripple mdui-float-left" href="?/admin/" ><i class="mdui-icon material-icons">&#xe8b8;</i> 管理后台</a>
	<a class="mdui-btn mdui-color-theme-accent mdui-ripple mdui-float-right" href="./"><i class="mdui-icon material-icons">&#xe2c7;</i> 访问网站</a>
<?php else:?>
	<h1  class='mdui-text-color-red'>程序安装失败!</h1>
	<br><br>
	<a class="mdui-btn mdui-btn-raised mdui-float-left" href="?step=2">重新绑定</a>
<?php endif;?>


	
</div>

<?php view::end('content');?>