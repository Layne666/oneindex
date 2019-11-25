<?php view::layout('layout')?>

<?php view::begin('content');?>
<div class="mdui-container-fluid">

	<div class="mdui-typo">
	  <h1> 文件展示设置 <small>根据不同后缀进行展示。无设置后缀，直连下载</small></h1>
	</div>
	<form action="" method="post">
		<?php foreach($show as $n=>$ext):?>
			<div class="mdui-textfield">
			  <h4><?php echo $names[$n];?></h4>
			  <input class="mdui-textfield-input" type="text" name="<?php echo $n;?>" value="<?php echo join(' ', $ext);?>"/>
			</div>
		<?php endforeach;?>

	   <button type="submit" class="mdui-btn mdui-color-theme-accent mdui-ripple mdui-float-right">
	   	<i class="mdui-icon material-icons">&#xe161;</i> 保存
	   </button>
	</form>
</div>
<?php view::end('content');?>