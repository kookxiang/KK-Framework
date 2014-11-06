<?php
/**
 * KK's Laboratory (c) 2009-2014.
 * User: kookxiang
 */
namespace System\Module;
use System\Exception\UndefinedAction;

class Router {
	/**
	 * 判断当前请求是 HTTP 请求还是 HTTPS 请求
	 * @return bool 是否为 HTTPS 请求
	 */
	public static function isSecureRequest(){
		if(!defined('IS_HTTPS_REQUEST'))
			define('IS_HTTPS_REQUEST', ($_SERVER['HTTPS'] && $_SERVER['HTTPS'] != 'off'));
		return IS_HTTPS_REQUEST;
	}

	/**
	 * 开始路由分发
	 */
	public function startDispatch(){
		if(!defined('SITE_URL')) {
			if (self::isSecureRequest()) {
				define('SITE_URL', 'https://' . $_SERVER['SERVER_NAME']);
			} else {
				define('SITE_URL', 'http://' . $_SERVER['SERVER_NAME']);
			}
		}

		$path = trim($_SERVER['PATH_INFO'], '/');
		if(preg_match('/[^A-z0-9\-\/]/', $path)) throw new Exception('Router refuse: illegal request', 3);
		if(!$path) $path = 'Index';
		$this->dispatch($path);
	}

	/**
	 * 根据 Path 寻找对应路由
	 * @param string $path 页面路径
	 * @throws UndefinedAction
	 */
	public static function dispatch($path){
		list($router, $path) = explode('/', $path, 2);
		$router = ucfirst($router);
		$path = ucfirst($path);
		$className = "\\Router\\{$router}";
		$router = new $className();
		if(!$path) $path = 'Index';
		if(method_exists($router, $path))
			$router->$path();
		else
			throw new UndefinedAction();
	}

	/**
	 * 取得页面的 URL，用于处理 Rewrite
	 * @param string $target 目标页面
	 * @return string
	 */
	public static function generateURL($target){
		return $target;
	}

	/**
	 * HTTP 302 强制页面跳转
	 * @param string $target 目的页面
	 */
	public static function redirect($target){
		header('Location: '.self::generateURL($target));
		exit();
	}

	/**
	 * 显示消息并跳转页面
	 * @param string $text 消息内容
	 * @param string $link 目的页面地址
	 * @param int $timeout 跳转时间
	 */
	public static function showRedirect($text, $link = null, $timeout = 3){
		if($link !== null) $link = self::generateURL($link);
		include Template::load('Redirect');
		exit();
	}

	/**
	 * 强制 HTTPS 访问当前页面
	 * 如果用户通过 HTTP 访问，则 302 跳转到 HTTPS
	 */
	public static function forceHttps(){
		if(!self::isSecureRequest()) self::redirect('https://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
	}

	/**
	 * 强制 HTTP 访问当前页面
	 * 如果用户通过 HTTPS 访问，则 302 跳转到 HTTP
	 */
	public static function forceHttp(){
		if(self::isSecureRequest()) self::redirect('http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
	}
}
