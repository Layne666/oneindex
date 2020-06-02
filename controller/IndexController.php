<?php 
class IndexController{
	private $url_path;
	private $name;
	private $path;
	private $items;
	private $time;

	function __construct(){
	    global $配置文件;
 	
       	//分页页数
 		$this->z_page = 100;
       
		$var=explode("/",$_SERVER["REQUEST_URI"]);
        $驱动器=$var["1"];
        array_splice($var,0, 1);
        unset($var['0']);
          $请求路径 = implode("/", $var);  
         $请求路径= str_replace("?".$_SERVER["QUERY_STRING"],"",$请求路径);

		
		

	 	$paths = explode('/', rawurldecode($请求路径));
	 	
	 
		if(substr($_SERVER['REQUEST_URI'], -1) != '/'){
			$this->name = array_pop($paths);
		}

 		preg_match_all("(\.page\-([0-9]*)/$)",get_absolute_path(join('/', $paths)),$mat);
 		if(empty($mat[1][0])){
 			$this->page = 1;
 		} else {
 			$this->page = $mat[1][0];
 		
 		}
         $this->page=$_GET["page"]??"1";
 		$this->url_path = preg_replace("(\.page\-[0-9]*/$)","",get_absolute_path(join('/', $paths)));

	$this->path = get_absolute_path(config('onedrive_root').$this->url_path);
		//获取文件夹下所有元素
		$this->items = $this->items($this->path);
	}

	
	function index(){
	   
	 
	    
	  
	    
	    if ($this->path=="/"){
	          if($this->items==NUll){
	         // echo "数据空";
	          require(ROOT."del.php");
	        //  header("refresh: 0.1");
	          
	      }
	        
	    }
	    
	    
	    
	    
	    
		//是否404
		$this->is404();

		$this->is_password();

		//header("Expires:-1");
	//	header("Cache-Control:no_cache");
	//	header("Pragma:no-cache");

		if(!empty($this->name)){//file
			return $this->file();
		}else{//dir
			return $this->dir();
		}
	}

	//判断是否加密
	function is_password(){
		if(empty($this->items['.password'])){
			return false;
		}else{
			$this->items['.password']['path'] = get_absolute_path($this->path).'.password';
 		}
		
		$password = $this->get_content($this->items['.password']);
		list($password) = explode("\n",$password);
		$password = trim($password);
		unset($this->items['.password']);
		if(!empty($password) && strcmp($password, $_COOKIE[md5($this->path)]) === 0){
			return true;
		}

		$this->password($password);
		
	}

	function password($password){
		if(!empty($_POST['password']) && strcmp($password, $_POST['password']) === 0){
			setcookie(md5($this->path), $_POST['password']);
			return true;
		}
		$navs = $this->navs();
		echo view::load('password')->with('navs',$navs);
		exit();
	}

	//文件
	function file(){
		$item = $this->items[$this->name];
		if ($item['folder']) {//是文件
		
		
		
		
			$url = $_SERVER['REQUEST_URI'].'/';
		}elseif(!is_null($_GET['t']) ){//缩略图
			$url = $this->thumbnail($item);
		}elseif($_SERVER['REQUEST_METHOD'] == 'POST' || !is_null($_GET['s']) ){
			return $this->show($item);
		}else{//返回下载链接
			if (config('proxy_domain') != ""){
			$url = str_replace(config('main_domain'),config('proxy_domain'),$item['downloadUrl']);
			}else {
				$url = $item['downloadUrl'];
			}
		}
		header('Location: '.$url);
	}


	
	//文件夹
	function dir(){
		$root = get_absolute_path(dirname($_SERVER['SCRIPT_NAME'])).config('root_path');
		$navs = $this->navs();

		if($this->items['index.html']){
			$this->items['index.html']['path'] = get_absolute_path($this->path).'index.html';
			$index = $this->get_content($this->items['index.html']);
			header('Content-type: text/html');
			echo $index;
			exit();
		}

		if($this->items['README.md']){
			$this->items['README.md']['path'] = get_absolute_path($this->path).'README.md';
			$readme = $this->get_content($this->items['README.md']);
			$Parsedown = new Parsedown();
			$readme = $Parsedown->text($readme);
			//不在列表中展示
			unset($this->items['README.md']);
		}

		if($this->items['HEAD.md']){
			$this->items['HEAD.md']['path'] = get_absolute_path($this->path).'HEAD.md';
			$head = $this->get_content($this->items['HEAD.md']);
			$Parsedown = new Parsedown();
			$head = $Parsedown->text($head);
			//不在列表中展示
			unset($this->items['HEAD.md']);
		}

		$this->totalpage = ceil(count($this->items) / $this->z_page);

		if($this->page*$this->z_page >= count($this->items))
		$this->page = $this->totalpage;

		return view::load('list')->with('title', config('title_name'))
					->with('navs', $navs)
					->with('path',join("/", array_map("rawurlencode", explode("/", $this->url_path))))
					->with('root', $root)
					->with('items', array_slice($this->items,$this->z_page*($this->page-1),$this->z_page))
					->with('head',$head)
					->with('readme',$readme)
 					->with('page',$this->page)
 					->with('totalpage',$this->totalpage);
	}

	function show($item){
		$root = get_absolute_path(dirname($_SERVER['SCRIPT_NAME'])).(config('root_path')?'?/':'');
		$ext = strtolower(pathinfo($item['name'], PATHINFO_EXTENSION));
		$data['title'] = $item['name'];
		$data['navs'] = $this->navs();
		$data['item'] = $item;
		$data['ext'] = $ext;
		$data['item']['path'] = get_absolute_path($this->path).$this->name;
		$http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
		$uri = onedrive::urlencode(get_absolute_path($this->url_path.'/'.$this->name));
		$data['url'] = $http_type.$_SERVER['HTTP_HOST'].$root.$uri;
		

		$show = config('show');
		foreach($show as $n=>$exts){
			if ($ext == 'pdf'){
				return view::load('show/pdf')->with($data);
			}
			elseif(in_array($ext,$exts)){
				return view::load('show/'.$n)->with($data);
			}
		}

		header('Location: '.$item['downloadUrl']);
	}
	//缩略图
	function thumbnail($item){
		if(!empty($_GET['t'])){
			list($width, $height) = explode('|', $_GET['t']);
		}else{
			//800 176 96
			$width = $height = 800;
		}
		$item['thumb'] = onedrive::thumbnail($this->path.$this->name);
		list($item['thumb'],$tmp) = explode('&width=', $item['thumb']);
		$item['thumb'] .= strpos($item['thumb'], '?')?'&':'?';
		return $item['thumb']."width={$width}&height={$height}";
	}

	//文件夹下元素
	function items($path, $fetch=false){
		$items = cache::get('dir_'.$this->path, function(){
			return onedrive::dir($this->path);
		}, config('cache_expire_time'));
		return $items;
	}

	function navs(){
		$root = get_absolute_path(dirname($_SERVER['SCRIPT_NAME'])).config('root_path');
		$navs['/'] = get_absolute_path($root.'/');
		foreach(explode('/',$this->url_path) as $v){
			if(empty($v)){
				continue;
			}
			$navs[rawurldecode($v)] = end($navs).$v.'/';
		}
		if(!empty($this->name)){
			$navs[$this->name] = end($navs).urlencode($this->name);
		}
		
		return $navs;
	}

	static function get_content($item){
		$content = cache::get('content_'.$item['path'], function() use ($item){
			$resp = fetch::get($item['downloadUrl']);
			if($resp->http_code == 200){
				return $resp->content;
			}
		}, config('cache_expire_time') );
		return $content;
	}

	//404
	function is404(){
		if(!empty($this->items[$this->name]) || (empty($this->name) && is_array($this->items)) ){
			return false;
		}

		http_response_code(404);
		view::load('404')->show();
		die();
	}

	function __destruct(){
		if (!function_exists("fastcgi_finish_request")) {
			return;
		}
	}
}
