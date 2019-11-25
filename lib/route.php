<?php
class route {
	public static $uri;
	private static $method;
	private static $root = '/';
	static $runed = false;

	public static $patterns = array(
		'#any' => '[^/]+',
		'#num' => '[0-9]+',
		'#all' => '.*',
	);

	public static function __callstatic($method, $args) {
		if (self::$runed) {
			return;
		}

		if (empty(self::$method)) {
			self::init();
		}

		$method = strtoupper($method);
		if ($method != self::$method && !in_array($method, ['ANY', 'ERROR', 'ON'])) {
			return;
		}
		$pattern = trim(array_shift($args), '\/');
		$pattern = self::$root . $pattern;

		if (self::uri_match($pattern, self::$uri)) {
			if (is_string($args[0]) && strpos($args[0], '@') > 0) {
				list($class, $action) = explode('@', $args[0]);
				$object = new $class();
				$args[0] = array($object, $action);
			}

			$return = call_user_func($args[0]);
			if (is_array($return)) {
				print json_encode($return);
			} else {
				print (string) $return;
			}
			self::$runed = true;
		}
	}

	public static function init() {
		if (!empty(self::$method)) {return;}
		self::$uri = self::get_uri();
		self::$method = empty($_POST['_METHOD']) ? $_SERVER['REQUEST_METHOD'] : $_POST['_METHOD'];
		if (defined('CONTROLLER_PATH')) {
			spl_autoload_register(function ($class) {
				$file = CONTROLLER_PATH . $class . '.php';
				if (file_exists($file)) {
					include $file;
				}
			});
		}
	}

	public static function auto($controller_path) {
		self::init();
		$uri = self::get_uri();
		list($tmp, $controller, $action) = explode('/', $uri);
		$controller = empty($controller) ? 'IndexController' : ucfirst($controller) . 'Controller';
		$action = empty($action) ? 'index' : $action;
		$file = $controller_path . $controller . '.php';
		if (file_exists($file)) {
			include $file;
			if (is_callable(array($controller, $action))) {
				$obj = new $controller();
				print (string) $obj->$action();
				return;
			}
		}
	}

	public static function group($middleware, $callback){
		self::init();
		if (is_string($middleware) && strpos($middleware, '@') > 0) {
			list($class, $action) = explode('@', $middleware);
			$object = new $class();
			$result = $object->$action();
		}elseif(is_callable($middleware)){
			$result = $middleware();
		}

		if($result == true && is_callable($callback)){
			return $callback();
		}
	}

	public static function resource($name, $controller) {
		self::get('/' . $name, $controller . '@index');
		self::get('/' . $name . '/add', $controller . '@add');
		self::post('/' . $name, $controller . '@store');
		self::get('/' . $name . '/{id:#num}', $controller . '@show');
		self::get('/' . $name . '/{id:#num}/edit', $controller . '@edit');
		self::post('/' . $name . '/{id:#num}', $controller . '@update');
		self::get('/' . $name . '/{id:#num}/delete', $controller . '@delete');
	}

	public static function uri_match($pattern, $uri) {
		$pattern = ($pattern == '/') ? '/' : rtrim($pattern, '\/');

		$ps = explode('/', $pattern);

		$searches = array_keys(static::$patterns);
		$replaces = array_values(static::$patterns);

		foreach($ps as &$p){
				$p = str_replace($searches, $replaces, $p);
				$p = preg_replace("`\{(\w+)\:([^\)]+)\}`", '(?P<$1>$2)', $p);	
		}

		$pattern = join('/',$ps);

		if (preg_match("`^{$pattern}$`", $uri)) {
			preg_match_all("`^{$pattern}$`", $uri, $matches, PREG_PATTERN_ORDER);
			foreach ($matches as $key => $value) {
				if (!is_int($key)) {
					$_GET[$key] = $matches[$key][0];
				}
			}
			return true;
		}
	}

	public static function get_uri() {
		$file = basename($_SERVER['PHP_SELF']);
		$path = dirname($_SERVER['PHP_SELF']);
		$req_uri = $_SERVER['REQUEST_URI'];

		if ($path != '/' && strpos($req_uri, $path) === 0) {
			$req_uri = substr($req_uri, strlen($path));
		}

		if (strpos($req_uri, '/?/') === 0) {
			$req_uri = parse_url($req_uri, PHP_URL_QUERY);
			list($req_uri) = explode('&', $req_uri);
			unset($_GET[$req_uri]);
		}
		$uri = parse_url($req_uri, PHP_URL_PATH);
		return '/' . trim($uri, '\/');
	}
}