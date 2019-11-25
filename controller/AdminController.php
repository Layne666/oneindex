<?php 
define('VIEW_PATH', ROOT.'view/admin/');
class AdminController{
	static $default_config = array(
	  'site_name' =>'OneIndex',
	  'password' => 'oneindex',
	  'style'=>'material',
	  'onedrive_root' =>'',
	  'cache_type'=>'secache',
	  'cache_expire_time' => 3600,
	  'cache_refresh_time' => 600,
	  'root_path' => '?',
	  'show'=> array (
	  	'stream'=>['txt'],
	    'image' => ['bmp','jpg','jpeg','png','gif'],
	    'video5'=>['mp4','webm','mkv'],
	    'video'=>[],
	    'video2'=>['avi','mpg', 'mpeg', 'rm', 'rmvb', 'mov', 'wmv', 'asf', 'ts', 'flv'],
	    'audio'=>['ogg','mp3','wav'],
	    'code'=>['html','htm','php', 'css', 'go','java','js','json','txt','sh','md'],
	    'doc'=>['csv','doc','docx','odp','ods','odt','pot','potm','potx','pps','ppsx','ppsxm','ppt','pptm','pptx','rtf','xls','xlsx']
	  ),
	  'images'=>['home'=>false,'public'=>false, 'exts'=>['jpg','png','gif','bmp']]
	);
	
	function __construct(){
	}

	function login(){
		if(!empty($_POST['password']) && $_POST['password'] == config('password')){
			setcookie('admin', md5(config('password').config('refresh_token')) );
			return view::direct(get_absolute_path(dirname($_SERVER['SCRIPT_NAME'])).'?/admin/');
		}
		return view::load('login')->with('title', '系统管理');
	}

	function logout(){
		setcookie('admin', '' );
		return view::direct(get_absolute_path(dirname($_SERVER['SCRIPT_NAME'])).'?/login');
	}

	function settings(){
		$message = false;

		if($_POST){
			
			if ($this->cache_exists($_POST['cache_type'])) {
				$message = '保存成功';
				config('cache_type', $_POST['cache_type']);
			} else {
				$message = '缓存类型不可用，请确认已经安装了该拓展。';
				config('cache_type', 'secache');
			}

			config('site_name', $_POST['site_name']);
			config('style', $_POST['style']);
			config('onedrive_root', get_absolute_path($_POST['onedrive_root']));
			config('onedrive_hide', $_POST['onedrive_hide']);
			config('onedrive_hotlink', $_POST['onedrive_hotlink']);
			config('cache_expire_time', intval($_POST['cache_expire_time']));
			$_POST['root_path'] = empty($_POST['root_path'])?'?':'';
			config('root_path', $_POST['root_path']);
		}

		$config = config('@base');

		return view::load('settings')->with('config', $config)->with('message', $message);
	}

	/**
	 * 判断缓存类型
	 *
	 * @param string $cache_type 缓存类型
	 * @return void
	 */
	function cache_exists($cache_type){
		// 需要判断环境的缓存类型
		$_cache_type = [
			'redis',
			'memcache',
		];

		if (in_array($cache_type, $_cache_type)) {
			return class_exists(ucfirst($cache_type));
		}

		return true;
	}

	function cache(){
		if(!is_null($_POST['clear'])){
			cache::clear();
			$message = "清除缓存成功";
		}elseif ( !is_null($_POST['refresh']) ){
			oneindex::refresh_cache(get_absolute_path(config('onedrive_root')));
			$message = "重建缓存成功";
		}
		return view::load('cache')->with('message', $message);
	}

	function images(){
		if($_POST){
			$config['home'] = empty($_POST['home'])?false:true;
			$config['public'] = empty($_POST['public'])?false:true;
			$config['exts'] = explode(" ", $_POST['exts']);
			config('images@base',$config);
		}
		$config = config('images@base');
		return view::load('images')->with('config', $config);;
	}


	function show(){
		if(!empty($_POST) ){
			foreach($_POST as $n=>$ext){
				$show[$n] = explode(' ', $ext);
			}
			config('show', $show);
		}
		$names = [
			'stream'=>'直接输出(<5M)，走本服务器流量(stream)',
			'image' =>'图片(image)',
			'video'=>'Dplayer 视频(video)',
			'video2'=>'Dplayer DASH 视频(video2)/个人版账户不支持',
			'video5'=>'html5视频(video5)',
			'audio'=>'音频播放(audio)',
			'code'=>'文本/代码(code)',
			'doc'=>'文档(doc)'
		];
		$show = config('show');
		return view::load('show')->with('names', $names)->with('show', $show);
	}

	function setpass(){
		if($_SERVER['REQUEST_METHOD'] == 'POST'){
			if($_POST['old_pass'] == config('password')){
				if($_POST['password'] == $_POST['password2']){
					config('password', $_POST['password']);
					$message = "修改成功";
				}else{
					$message = "两次密码不一致，修改失败";
				}
			}else{
				$message = "原密码错误，修改失败";
			}
		}
		return view::load('setpass')->with('message', $message);
	}
	
	function install(){
		if(!empty($_GET['code'])){
			return $this->install_3();
		}
		switch ( intval($_GET['step']) ){
			case 1:
				return $this->install_1();
			case 2:
				return $this->install_2();	
			default:
				return $this->install_0();
		}
	}

	function install_0(){
		$check['php'] = version_compare(PHP_VERSION,'5.5.0','ge');
		$check['curl'] = function_exists('curl_init');
		$check['config'] = is_writable(ROOT.'config/');
		$check['cache'] = is_writable(ROOT.'cache/');

		return view::load('install/install_0')->with('title','系统安装')
						->with('check', $check);
	}

	function install_1(){
		if(!empty($_POST['client_secret']) && !empty($_POST['client_id']) && !empty($_POST['redirect_uri']) ){
			config('@base', self::$default_config);
			config('client_secret',$_POST['client_secret']);
			config('client_id',$_POST['client_id']);
			config('redirect_uri',$_POST['redirect_uri']);
			return view::direct('?step=2');
		}
		if($_SERVER['HTTP_HOST'] == 'localhost'){
			$redirect_uri = 'http://'.$_SERVER['HTTP_HOST'].get_absolute_path(dirname($_SERVER['PHP_SELF']));
		}else{
			// 非https,调用ju.tn中转
			$redirect_uri = 'https://pan.layne666.cn/';
		}
		
		$ru = "https://developer.microsoft.com/en-us/graph/quick-start?appID=_appId_&appName=_appName_&redirectUrl={$redirect_uri}&platform=option-php";
		$deepLink = "/quickstart/graphIO?publicClientSupport=false&appName=oneindex&redirectUrl={$redirect_uri}&allowImplicitFlow=false&ru=".urlencode($ru);
		$app_url = "https://apps.dev.microsoft.com/?deepLink=".urlencode($deepLink);
		return view::load('install/install_1')->with('title','系统安装')
						->with('redirect_uri', $redirect_uri)
						->with('app_url', $app_url);
	}

	function install_2(){
		return view::load('install/install_2')->with('title','系统安装');
	}

	function install_3(){
		$data = onedrive::authorize($_GET['code']);
		if(!empty($data['refresh_token'])){
			config('refresh_token',$data['refresh_token']);
			config('@token', $data);
		}
		return view::load('install/install_3')->with('refresh_token',$data['refresh_token']);
		
	}
}
