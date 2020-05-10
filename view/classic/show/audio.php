<?php view::layout('layout')?>

<?php view::begin('content');?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/aplayer/dist/APlayer.min.css">
<div class="mdui-container-fluid">
	<br>
	<center>
	<div id="aplayer"></div>
	</audio>
	</center>
	<br>
	<!-- 固定标签 -->
	<div class="mdui-textfield">
	  <label class="mdui-textfield-label">下载地址</label>
	  <input class="mdui-textfield-input" type="text" value="<?php e($url);?>"/>
	</div>
	<div class="mdui-textfield">
	  <label class="mdui-textfield-label">引用地址</label>
	  <textarea class="mdui-textfield-input"><audio src="<?php e($url);?>"></audio></textarea>
	</div>
</div>
<a href="<?php e($url);?>" class="mdui-fab mdui-fab-fixed mdui-ripple mdui-color-theme-accent"><i class="mdui-icon material-icons">file_download</i></a>
<script src="https://cdn.jsdelivr.net/npm/aplayer/dist/APlayer.min.js"></script>
<script>
const ap = new APlayer({
    container: document.getElementById('aplayer'),
    audio: [{
        name: '<?php e(pathinfo($item["name"], PATHINFO_FILENAME)); ?>',
        artist: 'Oneindex Preview',
        url: '<?php e($item['downloadUrl']);?>',
        cover: '<?php e( !empty($item['thumb'] ) ? $item['thumb'].'&width=176&height=176' : null);?>'
    }]
});
</script>

<?php view::end('content');?>
