<?php 
	$url = 'https://view.officeapps.live.com/op/view.aspx?src='.urlencode($item['downloadUrl']);
	view::direct($url);
	exit();
?>
