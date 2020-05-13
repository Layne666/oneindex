<?php view::layout('layout')?>
<?php 
	function code_type($ext){
		$code_type['html'] = 'html';
		$code_type['htm'] = 'html';
		$code_type['php'] = 'php';
		$code_type['css'] = 'css';
		$code_type['go'] = 'golang';
		$code_type['java'] = 'java';
		$code_type['js'] = 'javascript';
		$code_type['json'] = 'json';
		$code_type['txt'] = 'text';
		$code_type['sh'] = 'sh';
		$code_type['md'] = 'Markdown';
		
		return @$code_type[$ext];
	}
	$language = code_type($ext);

	$content = IndexController::get_content($item);
?>
<?php view::begin('content');?>
<style type="text/css" media="screen">
    #editor { 
        /*height:800px;*/
    }
</style>
<div class="mdui-container-fluid">
    <div class="nexmoe-item">

        <pre id="editor" ><?php echo htmlentities($content);?></pre>

	<div class="mdui-row">
	  <select class="mdui-select" mdui-select="{position: 'top'}" id="sel">
	    <option value="<?php e($url);?>" selected>下载地址</option>
	  </select>
	  <textarea class="mdui-textfield-input" id="val" readonly><?php e($url);?></textarea>
	</div>
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
<a href="<?php e($url);?>" class="mdui-fab mdui-fab-fixed mdui-ripple mdui-color-theme-accent"><i class="mdui-icon material-icons">file_download</i></a>

<script src="//cdn.bootcss.com/ace/1.4.9/ace.js"></script>
<script src="//cdn.bootcss.com/ace/1.4.9/ext-language_tools.js"></script>
<script>
    var editor = ace.edit("editor");
    editor.setTheme("ace/theme/ambiance");
    editor.setFontSize(18);
    editor.session.setMode("ace/mode/<?php e($language);?>");
    
    //Autocompletion
    editor.setOptions({
        enableBasicAutocompletion: true,
        enableSnippets: true,
        enableLiveAutocompletion: true,
        maxLines: Infinity
    });
</script>
<?php view::end('content');?>