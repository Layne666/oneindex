<?php

define('VIEW_PATH', ROOT.'view/admin/');
class AdminController
{
    public static $default_config = array(
      'site_name' => 'OneIndex',
      'title_name' => 'Index of /',
      'requrl'=> "",
      'password' => 'oneindex',
      'drawer' => '<br>',
      'style' => 'nexmoe',
      'onedrive_root' => '',
      'cache_type' => 'filecache',
      'cache_expire_time' => 3600,
      'cache_refresh_time' => 600,
      'page_item' => 50,
      'root_path' => '',
      'show' => array(
        'stream' => ['txt'],
        'image' => ['bmp', 'jpg', 'jpeg', 'png', 'gif', 'webp'],
        'video5' => [],
        'video' => ['mpg', 'mpeg', 'mov', 'flv', 'mp4', 'webm', 'mkv', 'm3u8'],
        'video2' => ['avi', 'rm', 'rmvb', 'wmv', 'asf', 'ts'],
        'audio' => ['ogg', 'mp3', 'wav', 'flac', 'aac', 'm4a', 'ape'],
        'code' => ['html', 'htm', 'php', 'css', 'go', 'java', 'js', 'json', 'txt', 'sh', 'md'],
        'doc' => ['csv', 'doc', 'docx', 'odp', 'ods', 'odt', 'pot', 'potm', 'potx', 'pps', 'ppsx', 'ppsxm', 'ppt', 'pptm', 'pptx', 'rtf', 'xls', 'xlsx'],
      ),
      'images' => ['home' => false, 'public' => false, 'exts' => ['jpg', 'png', 'gif', 'bmp']],
    );

    public function __construct()
    {
    }

    public function login()
    {
        if (!empty($_POST['password']) && $_POST['password'] == config('password')) {
            setcookie('admin',config('password'));

            return view::direct(get_absolute_path(dirname($_SERVER['SCRIPT_NAME'])).'?/admin/');
        }

        return view::load('login')->with('title', '系统管理');
    }

    public function logout()
    {
        setcookie('admin', '');

        return view::direct(get_absolute_path(dirname($_SERVER['SCRIPT_NAME'])).'?/login');
    }

    public function settings()
    {
        if ($_POST) {
            config('site_name', $_POST['site_name']);
            config('title_name', $_POST['title_name']);
            config('drawer', $_POST['drawer']);
            config('style', $_POST['style']);
            config('main_domain', $_POST['main_domain']);
            config('proxy_domain', $_POST['proxy_domain']);
            config('onedrive_root', get_absolute_path($_POST['onedrive_root']));

            config('onedrive_hide', $_POST['onedrive_hide']);

            config('cache_type', $_POST['cache_type']);
            config('cache_expire_time', intval($_POST['cache_expire_time']));
            config('page_item', intval($_POST['page_item']));

            $_POST['root_path'] = empty($_POST['root_path']) ? '?' : '';
            config('root_path', $_POST['root_path']);
        }
        $config = config('@base');

        return view::load('settings')->with('config', $config);
    }

    public function cache()
    {
        require(ROOT."del.php");
        return view::load('cache')->with('message', $message);
    }

    public function images()
    {
        if ($_POST) {
            $config['home'] = empty($_POST['home']) ? false : true;
            $config['public'] = empty($_POST['public']) ? false : true;
            $config['exts'] = explode(' ', $_POST['exts']);
            config('images@base', $config);
        }
        $config = config('images@base');

        return view::load('images')->with('config', $config);
    }

    public function show()
    {
        if (!empty($_POST)) {
            foreach ($_POST as $n => $ext) {
                $show[$n] = explode(' ', $ext);
            }
            config('show', $show);
        }
        $names = [
            'stream' => '直接输出(<5M)，走本服务器流量(stream)',
            'image' => '图片(image)',
            'video' => 'Dplayer 视频(video)',
            'video2' => 'Dplayer DASH 视频(video2)/个人版账户不支持',
            'video5' => 'html5视频(video5)',
            'audio' => '音频播放(audio)',
            'code' => '文本/代码(code)',
            'doc' => '文档(doc)',
        ];
        $show = config('show');

        return view::load('show')->with('names', $names)->with('show', $show);
    }

    public function setpass()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if ($_POST['old_pass'] == config('password')) {
                if ($_POST['password'] == $_POST['password2']) {
                    config('password', $_POST['password']);
                    $message = '修改成功';
                } else {
                    $message = '两次密码不一致，修改失败';
                }
            } else {
                $message = '原密码错误，修改失败';
            }
        }

        return view::load('setpass')->with('message', $message);
    }

    public function install()
    {
        if (!empty($_GET['code'])) {
            return $this->install_3();
        }
        switch (intval($_GET['step'])) {
            case 1:
                return $this->install_1();
            case 2:
                return $this->install_2();
            default:
                return $this->install_0();
        }
    }

    public function drives()
    {
        echo '开发中';
    }

    public function sharepoint()
    {
        $config = include 'config/token.php';

        if ($_REQUEST['site'] == '') {
            echo '站点名称填写/sites/名称或者/teams/名称';
            echo '<form action="" method="post">
 　　<input type="text" name="site" value ="/sites/名称" />
 　　<input type="submit" value="站点id" />
 </form>';
            exit;
        }
        $request['headers'] = "Authorization: bearer {$config['access_token']}".PHP_EOL.'Content-Type: application/json'.PHP_EOL;
        $request['url'] = 'https://microsoftgraph.chinacloudapi.cn/v1.0/sites/root';
        $resp = fetch::get($request);
        $data = json_decode($resp->content, true);
        $hostname = $data['siteCollection']['hostname'];

        $getsiteid = 'https://microsoftgraph.chinacloudapi.cn/v1.0/sites/'.$hostname.':'.$_REQUEST['site'];
        $request['url'] = $getsiteid;
        $respp = fetch::get($request);
        $datass = json_decode($respp->content, true);

        echo $siteidurl = ($datass['id']);
        if (($datass['id']) == '') {
            config('requrl', '');
            echo '获取站点id失败刷新重试试';
            echo '站点名称填写/sites/名称或者/teams/名称';
            echo '<form action="" method="post">
 　　<input type="text" name="site" value ="/sites/名称" />
 　　<input type="submit" value="站点id" />
 </form>';
            exit;
        }
        echo  $b =
 'https://microsoftgraph.chinacloudapi.cn/v1.0/sites/'.$siteidurl.'/drive/root';

        config('requrl', $b);
        exit;

        echo '安装成功站点id'.$datass['id'];
        echo '<a href="/?/admin" target="_blank">进入后台刷新缓存生效 默认密码oneindex</a>';
    }

    public function install_0()
    {
        $check['php'] = version_compare(PHP_VERSION, '5.5.0', 'ge');
        $check['curl'] = function_exists('curl_init');
        $check['config'] = is_writable(ROOT.'config/');
        $check['cache'] = is_writable(ROOT.'cache/');

        return view::load('install/install_0')->with('title', '系统安装')
                        ->with('check', $check);
    }

    public function install_1()
    {
        if (!empty($_POST['client_secret']) && !empty($_POST['client_id']) && !empty($_POST['redirect_uri'])) {
           
         if ($_COOKIE["drivestype"]=="cn"){
              setcookie('oauth_url',"login.partner.microsoftonline.cn/common/oauth2/v2.0");
          
         }else
         {
                setcookie('oauth_url',"https://login.microsoftonline.com/common/oauth2/v2.0");
         }
          
  setcookie('drivestype',$_POST["drivestype"]);
   setcookie('client_secret',$_POST["client_secret"]);
   setcookie('client_id',$_POST["client_id"]);
 
   setcookie('redirect_uri',$_POST["redirect_uri"]);
  
  
  
  
  
  
            return view::direct('?step=2');
        }
        $redirect_uri = 'https://coding.mxin.ltd/api/onedrive.html';

        $ru = "https://developer.microsoft.com/en-us/graph/quick-start?appID=_appId_&appName=_appName_&redirectUrl={$redirect_uri}&platform=option-php";
        $deepLink = "/quickstart/graphIO?publicClientSupport=false&appName=oneindex&redirectUrl={$redirect_uri}&allowImplicitFlow=false&ru=".urlencode($ru);
        $app_url = 'https://apps.dev.microsoft.com/?deepLink='.urlencode($deepLink);

        return view::load('install/install_1')->with('title', '系统安装')
                            ->with('redirect_uri', $redirect_uri)
                            ->with('app_url', $app_url);
    }

      public function install_2()
    {
        
$scope = urlencode("offline_access files.readwrite.all");
			$redirect_uri = $_COOKIE["redirect_uri"];
			$url = $_COOKIE["oauth_url"]."/authorize?client_id={$client_id}&scope={$scope}&response_typ=code&redirect_uri={$redirect_uri}";

        
        
        
     
        return view::load('install/install_2')->with('title', '系统安装');
    }

    public function install_3()
    {
        $data = onedrive::authorize($_GET['code']);
        if (!empty($data['refresh_token'])) {
            config('refresh_token', $data['refresh_token']);
            config('@token', $data);
        }
       

        return view::load('install/install_3')->with('refresh_token', $data['refresh_token']);
    }
}
