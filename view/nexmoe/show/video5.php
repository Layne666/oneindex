<?php view::layout('layout')?>
<?php $item['thumb'] = onedrive::thumbnail($item['path']);?>
<?php view::begin('content');?>
<div class="mdui-container-fluid">
	<div class="nexmoe-item">
		<video class="mdui-video-fluid mdui-center" preload controls poster="<?php @e($item['thumb']);?>">
		  <source src="<?php e($item['downloadUrl']);?>" type="video/mp4">
		</video>
	</div>
</div>
<a href="<?php e($url);?>" class="mdui-fab mdui-fab-fixed mdui-ripple mdui-color-theme-accent"><i class="mdui-icon material-icons">file_download</i></a>
<?php view::end('content');?>