<?php
/**
 * KK's Laboratory (c) 2009-2014.
 * User: kookxiang
 */

if(version_compare(PHP_VERSION, '5.3') < 0)
	exit('Sorry, This program is only available with PHP 5.3+.');

define('IN_FRAMEWORK', true);
define('ROOT', dirname(__FILE__).'/');
define('DATA_DIR', ROOT.'Data/');
define('SYSTEM_DIR', ROOT.'System/');
define('LIBRARY_DIR', ROOT.'Library/');

require SYSTEM_DIR.'Module/AutoLoader.php';
spl_autoload_register('\System\Module\AutoLoader::load');
set_exception_handler('\System\Module\Exception::handleException');

error_reporting(E_ALL & !E_NOTICE & !E_STRICT);
@ini_set('display_errors', 'On');
$db = new System\Module\Database();
$db->registerServer(array(
	'adapter' => 'MySQL',
	'host' => '127.0.0.1',
	'database' => 'test',
	'username' => 'root',
	'password' => 'Password',
	'charset' => 'utf-8',
));

$Router = new System\Module\Router();
$Router->startDispatch();	// 注册一个路由对象并开始分发
