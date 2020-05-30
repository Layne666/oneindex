<?php view::layout('install/layout'); ?>

<?php view::begin('content'); ?>

<?php $default_client_id = 'ea2b36f6-b8ad-40be-bc0f-e5e4a4a7d4fa'; ?>
<?php $default_client_secret = 'h27zG8pr8BNsLU0JbBh5AOznNS5Of5Y540l/koc7048='; ?>

<div class="mdui-container-fluid">
	<div class="mdui-typo">
	  <h1>系统安装 <small>设置应用ID和世纪互联(世纪互联无需填写,国际版自行注册应用支持无api权限账户)</small></h1>
	</div>

	<div class="mdui-typo">
      <h4 class="doc-article-title">
	    填入<code>client_id</code>和<code>client_secret</code>
      </h4>
    </div>

	<form action="" method="post">
	
	<form action="" method="post">
	<div class=mdui-textfield mdui-textfield-floating-label>    <label class="mdui-radio">
    <input type="radio" name="drivestype" value="us"/>
    <i class="mdui-radio-icon"></i>
   国际版
  </label>
  
  <label class="mdui-radio">
    <input type="radio" name="drivestype" value="cn" checked/>
    <i class="mdui-radio-icon"></i>
   世纪互联
  </label>
  
  
  
  
  
  </div>
  
  
  
  
		<div class="mdui-textfield mdui-textfield-floating-label mdui-textfield-has-bottom mdui-textfield-not-empty">
		
		
		
			<i class="mdui-icon material-icons">https</i>
			
			<label class="mdui-textfield-label">应用机密(client secret)</label>
			<input type="text" type="text" class="mdui-textfield-input" name="client_secret" required value="v4[Nq:4=rmFS78BwYi[@x3sGk-iY.U:S"/>
			<div class="mdui-textfield-error"></div>
		</div>
		
		<div class="mdui-textfield mdui-textfield-floating-label mdui-textfield-has-bottom mdui-textfield-not-empty">
			<i class="mdui-icon material-icons">https</i>
	
		  	<i class="mdui-icon material-icons">&#xe5c3;</i>
		  	<label class="mdui-textfield-label">应用 ID(client_id)</label>
		  	<input type="text" class="mdui-textfield-input" name="client_id" required value="3447f073-eef3-4c60-bb68-113a86f2c39a"/>
		  	<div class="mdui-textfield-error"></div>
		</div>
		
	<div class="mdui-textfield mdui-textfield-floating-label mdui-textfield-has-bottom mdui-textfield-not-empty">
		   <i class="mdui-icon material-icons">&#xe41a;</i>
		   <label class="mdui-textfield-label mdui-textfield-has-bottom mdui-textfield-not-empty"><?php echo '回调地址'; ?></label>
		   <input type="txet" class="mdui-textfield-input" name="redirect_uri" value="<?php echo $redirect_uri; ?>"/>
		   <div class="mdui-textfield-error">重定向URL不能为空</div>
		</div>
	 <a class="mdui-btn mdui-btn-raised mdui-float-left" href="?step=0">上一步</a>
	 <button class="mdui-btn mdui-color-theme-accent mdui-ripple mdui-float-right" type="submit">下一步</button>
	</form>

	
</div>

<?php view::end('content'); ?>