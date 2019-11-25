<?php view::layout('layout')?>

<?php view::begin('content');?>
	
<div class="mdui-container-fluid">

<div class="nexmoe-item" style="padding: 100px!important;">
	<div class="mdui-typo-display-3-opacity" style="text-align:center;">OneImages</div>

	<form action="" method="post" enctype="multipart/form-data">
		<input class="mdui-center" type="file" style="margin: 50px 0;" name="file" />


	<div class="mdui-row-xs-3">
	  <div class="mdui-col"></div>
	  <div class="mdui-col">
	    <button class="mdui-btn mdui-btn-block mdui-color-theme-accent mdui-ripple">上传</button>
	  </div>
	</div>

	</form>
	
</div>

</div>

<?php view::end('content');?>