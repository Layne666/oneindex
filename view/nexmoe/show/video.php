<?php
$item['thumb'] = onedrive::thumbnail($item['path']);
$downloadUrl = $item['downloadUrl'];
 	if (config('proxy_domain') != ""){
 	$downloadUrl = str_replace(config('main_domain'),config('proxy_domain'),$item['downloadUrl']);
 	}else {
 		$downloadUrl = $item['downloadUrl'];
 	}
?>


<link class="dplayer-css" rel="stylesheet" href="//cdn.jsdelivr.net/npm/dplayer/dist/DPlayer.min.css">
<script src="//cdn.jsdelivr.net/npm/dplayer/dist/DPlayer.min.js"></script>
<script src="//cdn.jsdelivr.net/npm/flv.js/dist/flv.min.js"></script>
<script src="//cdn.jsdelivr.net/npm/hls.js/dist/hls.min.js"></script>


<div class="mdui-container-fluid">
	<div class="nexmoe-item">
	<div class="mdui-center" id="dplayer"></div>
	

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
<script>
if(window.location.href.indexOf(".m3u8") > 0){          
    var type = 'customHls';}
else if(window.location.href.indexOf(".flv") > 0){  
    var type = 'flv';}
else {
    var type = 'normal'; //MP4格式P2P兼容性不好，不调用P2P。
}

const dp = new DPlayer({
	container: document.getElementById('dplayer'),
	lang:'zh-cn',
	video: {
	    url: '<?php e($downloadUrl);?>',
	    pic: '<?php @e($item['thumb']);?>',
	    
	    type: type,
	    customType: {
            customHls: function(video, player) {
                const hls = new Hls();
                hls.loadSource(video.src);
                hls.attachMedia(video);
            },
        },
	}
});
</script>

<?php //view::end('content');
exit;?>