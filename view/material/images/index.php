<?php view::layout('layout')?>

<?php view::begin('content');?>
	
<div class="mdui-container-fluid" style="padding-top: 100px;">

<center>
	<div class="mdui-typo-display-3-opacity">OneImages</div>
</center>


<center>
	<form action="" method="post" enctype="multipart/form-data">
		<input type="file" style="margin: 50px 0;" name="file" />


	<div class="mdui-row-xs-3">
	  <div class="mdui-col"></div>
	  <div class="mdui-col">
	    <button class="mdui-btn mdui-btn-block mdui-color-theme-accent mdui-ripple">上传</button>
	  </div>
	</div>

	</form>
</center>

</div>

<?php view::end('content');?>