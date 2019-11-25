<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0,maximum-scale=1.0, user-scalable=no"/>
	<title>OneIndex 系统管理</title>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/mdui@0.4.1/dist/css/mdui.min.css" integrity="sha256-lCFxSSYsY5OMx6y8gp8/j6NVngvBh3ulMtrf4SX5Z5A=" crossorigin="anonymous">
	<script src="https://cdn.jsdelivr.net/npm/mdui@0.4.1/dist/js/mdui.min.js" integrity="sha256-dZxrLDxoyEQADIAGrWhPtWqjDFvZZBigzArprSzkKgI=" crossorigin="anonymous"></script>
	<script>$ = mdui.JQ;</script>
</head>
<body class="mdui-drawer-body-left mdui-appbar-with-toolbar  mdui-theme-primary-indigo mdui-theme-accent-blue">
<header class="mdui-appbar mdui-appbar-fixed">
  <div class="mdui-toolbar mdui-color-theme">
    <span class="mdui-btn mdui-btn-icon mdui-ripple mdui-ripple-white" mdui-drawer="{target: '#main-drawer', swipe: true}"><i class="mdui-icon material-icons">menu</i></span>
    <a href="./" target="_blank" class="mdui-typo-headline mdui-hidden-xs">OneIndex</a>
    <div class="mdui-toolbar-spacer"></div>
    <a href="<?php echo $root?>?/logout"><i class="mdui-icon material-icons">&#xe8ac;</i> 登出</a>
  </div>
</header>
<div class="mdui-drawer" id="main-drawer">
	<?php $root = get_absolute_path(dirname($_SERVER['SCRIPT_NAME']));?>
  <div class="mdui-list">
	<br><br>  
	<a href="<?php echo $root?>?/admin" class="mdui-list-item">
      <i class="mdui-list-item-icon mdui-icon material-icons">&#xe8b8;</i>
      <div class="mdui-list-item-content">基本设置</div>
    </a>

    <a href="<?php echo $root?>?/admin/cache" class="mdui-list-item">
      <i class="mdui-list-item-icon mdui-icon material-icons">&#xe53b;</i>
      <div class="mdui-list-item-content">页面缓存</div>
    </a>

    <a href="<?php echo $root?>?/admin/show" class="mdui-list-item">
      <i class="mdui-list-item-icon mdui-icon material-icons">&#xe3a5;</i>
      <div class="mdui-list-item-content">文件展示设置</div>
    </a>
    <a href="<?php echo $root?>?/admin/images" class="mdui-list-item">
      <i class="mdui-list-item-icon mdui-icon material-icons">&#xe410;</i>
      <div class="mdui-list-item-content">图床设置(OneImages)</div>
    </a>

    <a href="<?php echo $root?>?/admin/upload" class="mdui-list-item">
      <i class="mdui-list-item-icon mdui-icon material-icons">&#xe2c6;</i>
      <div class="mdui-list-item-content">上传管理</div>
    </a>

    <a href="<?php echo $root?>?/admin/setpass" class="mdui-list-item">
      <i class="mdui-list-item-icon mdui-icon material-icons">&#xe88d;</i>
      <div class="mdui-list-item-content">密码修改</div>
    </a>

    <a href="https://onedrive.live.com/" class="mdui-list-item" target="_blank">
      <i class="mdui-list-item-icon mdui-icon material-icons">&#xe2bf;</i>
      <div class="mdui-list-item-content">文件管理(OneDrive)</div>
    </a>
  </div>
</div>

<a id="anchor-top"></a>

<div class="mdui-container">
	<?php view::section('content');?>
</div>
<script>
	$("a[href='<?php echo'?'.(route::get_uri());?>']").addClass("mdui-text-color-blue");
  // 消息提示
  <?php echo (isset($message) && !empty($message)) ? "mdui.snackbar({position: 'right-top', message: '{$message}'});" : '';?>
</script>
</body>

</html>