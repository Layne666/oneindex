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
  header('Location:/login.php');
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

   
}
