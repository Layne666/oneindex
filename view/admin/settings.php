<?php view::layout('layout'); ?>

<?php view::begin('content'); ?>
<div class="mdui-container-fluid">

	<div class="mdui-typo">
	  <h1> 基本设置 <small>设置基本参数</small></h1>
	</div>
	<form action="" method="post">
		<div class="mdui-textfield">
		  <h4>网站名称</h4>
		  <input class="mdui-textfield-input" type="text" name="site_name" value="<?php echo $config['site_name']; ?>"/>
		</div>

  		<div class="mdui-textfield">
 		  <h4>副标题</h4>
 		  <input class="mdui-textfield-input" type="text" name="title_name" value="<?php echo $config['title_name']; ?>"/>
 		</div>
 		 <div class="mdui-textfield">
		  <h4>sharepoint域名</h4>
		  <input class="mdui-textfield-input" type="text" name="main_domain" value="<?php echo $config['main_domain']; ?>"/>
		</div>
        <div class="mdui-textfield">
		  <h4>sharepoint CDN 域名-留空不使用该功能</h4>
		  <input class="mdui-textfield-input" type="text" name="proxy_domain" value="<?php echo $config['proxy_domain']; ?>"/>
		</div>
		<div class="mdui-textfield">
 		  <h4>侧边栏代码</h4>
		  <textarea class="mdui-textfield-input" rows="4" name="drawer"><?php echo $config['drawer']; ?></textarea>
 		</div>

		<div class="mdui-textfield">
		  <h4>网站风格<small></small></h4>
		  <select name="style" class="mdui-select">
			  <?php
                foreach (scandir(ROOT.'view') as $k => $s) {
                    $styles[$k] = trim($s, '/');
                }
                $styles = array_diff($styles, ['.', '..', 'admin']);
                $style = config('style') ? config('style') : 'material';
                $cache_type = config('cache_type') ? config('cache_type') : 'secache';
                foreach ($styles as $style_name):
              ?>
			  <option value ="<?php echo $style_name; ?>" <?php echo ($style == $style_name) ? 'selected' : ''; ?>><?php echo $style_name; ?></option>
			  <?php endforeach; ?>
		  </select>
		</div>

  		<div class="mdui-textfield">
 		  <h4>项目数量</h4>
 		  <input class="mdui-textfield-input" type="text" name="page_item" value="<?php echo $config['page_item']; ?>"/>
  		</div>

		<div class="mdui-textfield">
		  <h4>起始目录 <small>(空为根目录)</small></h4>
		  <input class="mdui-textfield-input" type="text" name="onedrive_root" value="<?php echo $config['onedrive_root']; ?>"/>
		</div>


		<div class="mdui-textfield">
		  <h4>需要隐藏的目录 <small>(每行一项,通配识别,清空缓存后生效)</small></h4>
		  <textarea class="mdui-textfield-input" placeholder="回车换行" name="onedrive_hide"><?=@$config['onedrive_hide']; ?></textarea>
		</div>

		<div class="mdui-textfield">
		  <h4>缓存类型<small></small></h4>
		  <select name="cache_type" class="mdui-select">
			  <?php
                foreach (['secache', 'filecache', 'memcache', 'redis'] as $type):
              ?>
			  <option value ="<?php echo $type; ?>" <?php echo ($type == $cache_type) ? 'selected' : ''; ?>><?php echo $type; ?></option>
			  <?php endforeach; ?>
		  </select>
		</div>

		<div class="mdui-textfield">
		  <h4>缓存过期时间(秒)</h4>
		  <input class="mdui-textfield-input" type="text" name="cache_expire_time" value="<?php echo $config['cache_expire_time']; ?>"/>
		</div>

		<div class="mdui-textfield">
		  <h4>去掉<code style="color: #c7254e;background-color: #f7f7f9;font-size:16px;">/?/</code> (需配合伪静态使用!!)</h4>
		  <label class="mdui-textfield-label"></label>
		  <label class="mdui-switch">
			  <input type="checkbox" name="root_path" value="?" <?php echo empty($config['root_path']) ? 'checked' : ''; ?>/>
			  <i class="mdui-switch-icon"></i>
		  </label>
		</div>
		

		

	   <button type="submit" class="mdui-btn mdui-color-theme-accent mdui-ripple mdui-float-right">
	   	<i class="mdui-icon material-icons">&#xe161;</i> 保存
	   </button>
	</form>
</div>
<?php view::end('content'); ?>
