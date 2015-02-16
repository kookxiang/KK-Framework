<?php
/**
 * KK Forum
 * A simple bulletin board system
 * Author: kookxiang <r18@ikk.me>
 */

// Initialize constants
define('FORUM_PATH', dirname(__FILE__).DIRECTORY_SEPARATOR);
define('LIBRARY_PATH', FORUM_PATH.'Library/');
define('DATA_PATH', FORUM_PATH.'Data/');
@ini_set('display_errors', 'Off');
@ini_set('expose_php', false);

// Register autoloader
require LIBRARY_PATH.'Core/AutoLoader.php';

// Register error handler
Core\Error::registerHandler();

// Initialize config
@include DATA_PATH.'Config.php';

$requestPath = Core\Request::getRequestPath();
$requestPath = strtolower($requestPath);
list(, $controller, $action) = explode('/', $requestPath, 3);

switch(true){
	case $controller == 'test':
		echo 'Hello world~';
		break;
	default:
		throw new Core\Error('The request URL is not exists', 404);
}
