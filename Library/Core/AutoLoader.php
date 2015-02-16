<?php
/**
 * KK Forum
 * A simple bulletin board system
 * Author: kookxiang <r18@ikk.me>
 */
namespace Core;

class AutoLoader {
	/**
	 * Load a class automatically
	 * @param $className string Name of class
	 */
	public static function loadClassFile($className){
		$classPath = LIBRARY_PATH.ltrim(str_replace('\\', '/', $className), '/').'.php';
		if(file_exists($classPath)){
			include $classPath;
		}
	}

	/**
	 * Register self as autoloader
	 */
	public static function register(){
		if(defined('KK_FORUM_AUTOLOADER')) return;
		spl_autoload_register(array('\\Core\\AutoLoader', 'loadClassFile'));
		define('KK_FORUM_AUTOLOADER', true);
	}
}
AutoLoader::register();
