<?php
$item['thumb'] = onedrive::thumbnail($item['path']);
$downloadUrl = $item['downloadUrl'];
 	if (config('proxy_domain') != ""){
 	$downloadUrl = str_replace(config('main_domain'),config('proxy_domain'),$item['downloadUrl']);
 	}else {
 		$downloadUrl = $item['downloadUrl'];
 	}
?>
<body>
	
<script type="text/javascript">

//document.write('<iframe src="https://mozilla.github.io/pdf.js/es5/web/viewer.html?file='+window.location.href+'" style="border: none; width: -webkit-fill-available; height: -webkit-fill-available" />'); 
document.write('<iframe src="/view/nexmoe/show/web/viewer.html?file='+window.location.href+'" width="100%" height="100%" frameborder="0" />'); 
</script>

</body>
</html>

