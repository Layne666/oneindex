<?php
class fetch {
	public static $headers = "User-Agent:Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/56.0.2924.87 Safari/537.36";
	public static $cookies;
	public static $curl_opt;
	public static $proxy;

	public static $max_connect = 10;

	public static function init($opt = array()) {
		self::$curl_opt = array(
			CURLOPT_RETURNTRANSFER => 1, //true, $head 有请求的返回值
			CURLOPT_BINARYTRANSFER => true, //返回原生的Raw输出
			CURLOPT_HEADER => true, //启用时会将头文件的信息作为数据流输出。
			CURLOPT_FAILONERROR => true, //显示HTTP状态码，默认行为是忽略编号小于等于400的HTTP信息。
			CURLOPT_AUTOREFERER => true, //当根据Location:重定向时，自动设置header中的Referer:信息。
			CURLOPT_FOLLOWLOCATION => false, //跳转
			CURLOPT_CONNECTTIMEOUT => 3, //在发起连接前等待的时间，如果设置为0，则无限等待。
			CURLOPT_TIMEOUT => 5, //设置cURL允许执行的最长秒数。
			CURLOPT_ENCODING => 'gzip,deflate',
			CURLOPT_SSL_VERIFYHOST => false,
			CURLOPT_SSL_VERIFYPEER => false,
		);
		foreach ($opt as $k => $v) {
			self::$curl_opt[$k] = $v;
		}
	}

	/**
	 * fetch::get('http://www.google.com/');
	 * fetch::post('http://www.google.com/', array('name'=>'foo'));
	 */
	public static function __callstatic($method, $args) {
		if (is_null(self::$curl_opt)) {
			self::init();
		}
		@list($request, $post_data, $callback) = $args;
		if (is_callable($post_data)) {
			$callback = $post_data;
			$post_data = null;
		}

		//single_curl
		if (is_string($request) || !empty($request['url'])) {
			$request = self::bulid_request($request, $method, $post_data, $callback);
			return self::single_curl($request);
		} elseif (is_array($request)) {
			//rolling_curl
			foreach ($request as $k => $r) {
				$requests[$k] = self::bulid_request($r, $method, $post_data, $callback);
			}
			return self::rolling_curl($requests);
		}
	}

	private static function bulid_request($request, $method = 'GET', $post_data = null, $callback = null) {
		//url
		if (is_string($request)) {
			$request = array('url' => $request);
		}
		empty($request['method']) && $request['method'] = $method;
		empty($request['post_data']) && $request['post_data'] = $post_data;
		empty($request['callback']) && $request['callback'] = $callback;
		return $request;
	}

	private static function bulid_ch(&$request) {
		// url
		$ch = curl_init($request['url']);
		// curl_opt
		$curl_opt = empty($request['curl_opt']) ? array() : $request['curl_opt'];
		$curl_opt = $curl_opt + (array) self::$curl_opt;
		// method
		$curl_opt[CURLOPT_CUSTOMREQUEST] = strtoupper($request['method']);
		// post_data
		if (!empty($request['post_data'])) {
			$curl_opt[CURLOPT_POST] = true;
			$curl_opt[CURLOPT_POSTFIELDS] = $request['post_data'];
		}
		// header
		$headers = @self::bulid_request_header($request['headers'], $cookies);
		$curl_opt[CURLOPT_HTTPHEADER] = $headers;

		// cookies
		$request['cookies'] = empty($request['cookies']) ? fetch::$cookies : $request['cookies'];
		$cookies = empty($request['cookies']) ? $cookies : self::cookies_arr2str($request['cookies']);
		if (!empty($cookies)) {
			$curl_opt[CURLOPT_COOKIE] = $cookies;
		}

		//proxy
		$proxy = empty($request['proxy']) ? self::$proxy : $request['proxy'];
		if (!empty($proxy)) {
			$curl_opt[CURLOPT_PROXY] = $proxy;
		}

		//setopt
		curl_setopt_array($ch, $curl_opt);

		$request['curl_opt'] = $curl_opt;
		$request['ch'] = $ch;

		return $ch;
	}

	private static function response($raw, $ch) {
		$response = (object) curl_getinfo($ch);
		$response->raw = $raw;
		//$raw = fetch::iconv($raw, $response->content_type);
		$response->headers = substr($raw, 0, $response->header_size);
		$response->cookies = fetch::get_respone_cookies($response->headers);
		fetch::$cookies = array_merge((array) fetch::$cookies, $response->cookies);
		$response->content = substr($raw, $response->header_size);
		return $response;
	}

	private static function single_curl($request) {
		$ch = self::bulid_ch($request);
		$raw = curl_exec($ch);
		$response = self::response($raw, $ch);
		curl_close($ch);
		if (is_callable($request['callback'])) {
			call_user_func($request['callback'], $response, $request);
		}
		return $response;
	}

	private static function rolling_curl($requests) {
		$master = curl_multi_init();
		$map = array();
		// start the first batch of requests
		do {
			$k = key($requests);
			$request = current($requests);
			next($requests);
			$ch = self::bulid_ch($request);
			curl_multi_add_handle($master, $ch);
			$key = (string) $ch;
			$map[$key] = array($k, $request['callback']);
		} while (count($map) < self::$max_connect && count($map) < count($requests));

		do {
			while (($execrun = curl_multi_exec($master, $running)) == CURLM_CALL_MULTI_PERFORM);
			if ($execrun != CURLM_OK) {
				break;
			}

			// a request was just completed -- find out which one
			while ($done = curl_multi_info_read($master)) {
				$key = (string) $done['handle'];

				list($k, $callback) = $map[$key];

				// get the info and content returned on the request
				$raw = curl_multi_getcontent($done['handle']);
				$response = self::response($raw, $done['handle']);
				$responses[$k] = $response;

				// send the return values to the callback function.
				if (is_callable($callback)) {
					$key = (string) $done['handle'];
					unset($map[$key]);
					call_user_func($callback, $response, $requests[$k], $k);
				}

				// start a new request (it's important to do this before removing the old one)
				$k = key($requests);
				if (!empty($k)) {
					$k = key($requests);
					$request = current($requests);
					next($requests);
					$ch = self::bulid_ch($request);
					curl_multi_add_handle($master, $ch);
					$key = (string) $ch;
					$map[$key] = array($k, $request['callback']);
					curl_multi_exec($master, $running);
				}

				// remove the curl handle that just completed
				curl_multi_remove_handle($master, $done['handle']);
			}

			// Block for data in / output; error handling is done by curl_multi_exec
			if ($running) {
				curl_multi_select($master, 10);
			}

		} while ($running);

		return $responses;
	}

	private static function bulid_request_header($headers, &$cookies) {
		if (is_array($headers)) {
			$headers = join(PHP_EOL, $headers);
		}
		if (is_array(self::$headers)) {
			self::$headers = join(PHP_EOL, self::$headers);
		}
		$headers = self::$headers.PHP_EOL .$headers;

		foreach (explode(PHP_EOL, $headers) as $k => $v) {
			@list($k, $v) = explode(':', $v, 2);
			if (empty($k) || empty($v)) {
				continue;
			}
			$k = implode('-', array_map('ucfirst', explode('-', $k)));
			$tmp[$k] = $v;
		}

		foreach ((array) $tmp as $k => $v) {
			if ($k == 'Cookie') {
				$cookies = $v;
			} else {
				$return[] = $k . ':' . $v;
			}
		}
		return (array) $return;
	}

	public static function iconv(&$raw, $content_type) {
		@list($tmp, $charset) = explode('CHARSET=', strtoupper($content_type));

		if (empty($charset) && stripos($content_type, 'html') > 0) {
			preg_match('@\<meta.+?charset=([\w]+)[\'|\"]@i', $raw, $matches);
			$charset = empty($matches[1]) ? null : $matches[1];
		}

		return empty($charset) ? $raw : iconv($charset, "UTF-8//IGNORE", $raw);
	}

	public static function get_respone_cookies($raw) {
		$cookies = array();
		if(strpos($raw, PHP_EOL) != false){
		    $lines = explode(PHP_EOL, $raw);
		}elseif(strpos($raw, "\r\n") != false){
		    $lines = explode("\r\n", $raw);
		}elseif(strpos($raw, '\r\n') != false){
		    $lines = explode('\r\n', $raw);
		}
		
		foreach ((array)$lines as $line) {
			if (substr($line, 0, 11) == 'Set-Cookie:') {
				list($k, $v) = explode('=', substr($line, 11), 2);
				list($v, $tmp) = explode(';', $v);
				$cookies[trim($k)] = trim($v);
			}
		}
		return $cookies;
	}

	public static function cookies_arr2str($arr) {
		$str = "";
		foreach ((array) $arr as $k => $v) {
			$str .= $k . "=" . $v . "; ";
		}
		return $str;
	}
}