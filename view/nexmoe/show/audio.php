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
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/aplayer/dist/APlayer.min.css">
<div class="mdui-container-fluid">
    <div class="nexmoe-item" style="margin: 5% 0;">
	
		<div id="aplayer"></div>
	
	<br>
	
	</div>
</div>
<a href="<?php e($url);?>" class="mdui-fab mdui-fab-fixed mdui-ripple mdui-color-theme-accent"><i class="mdui-icon material-icons">file_download</i></a>
<script src="https://cdn.jsdelivr.net/npm/aplayer/dist/APlayer.min.js"></script>
<script>
const ap = new APlayer({
    container: document.getElementById('aplayer'),
    audio: [{
        name: '<?php e(pathinfo($item["name"], PATHINFO_FILENAME)); ?>',
        artist: '',
        url: '<?php e($downloadUrl);?>',
        cover: '<?php e( !empty($item['thumb'] ) ? $item['thumb'].'&width=176&height=176' : null);?>'
    }]
});
ap.play() //自动播放
</script>

<?php view::end('content');?>