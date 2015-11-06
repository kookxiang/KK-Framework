<?php
/**
 * KK Forum
 * A simple bulletin board system
 * Author: kookxiang <r18@ikk.me>
 */

if (!defined('ROOT_PATH')) {
    exit('This file could not be access directly.');
}

/**
 * Site Name
 */
define('SITE_NAME', 'KK Framework Demo Site');

/**
 * Encrypt Key:
 * This key is used to encrypt password and other information.
 * Don't touch it after application install finished.
 */
define('ENCRYPT_KEY', 'Please generate key and paste here');

/**
 * Cookie Key:
 * Password which used to encrypt cookie info.
 * If this key is leaked, generate it again and all the users will forced logout.
 */
define('COOKIE_KEY', 'Please generate key and paste here');

/**
 * Rewrite setting:
 * remove "index.php" from url, needs to config apache/nginx manually
 */
define('USE_REWRITE', true);

/**
 * HTTPS support:
 * Use HTTPS connection when necessary, needs to config apache/nginx manually
 */
define('HTTPS_SUPPORT', true);

/**
 * Enable debug mode:
 * Disable debug mode will hide backtrace information, which is helpful for developer
 */
define('DEBUG_ENABLE', true);

/**
 * Check template and resource file update automatically
 * You can turn off this on production environment.
 */
define('TEMPLATE_UPDATE', true);

/**
 * Create compressed CSS / JS file automatically
 * You should turn on this on production environment.
 */
define('OPTIMIZE_RES', true);

/**
 * Use Uglify-JS 2 to compress javascript file.
 * You must install Uglify-JS 2 on your server to use this feature.
 *
 * For more information, please refer to https://github.com/mishoo/UglifyJS2
 */
define('ENABLE_UGLIFYJS', false);

/**
 * Use Clean-CSS to compress CSS StyleSheet.
 * You must install Clean-CSS on your server to use this feature.
 *
 * For more information, please refer to https://github.com/jakubpawlowicz/clean-css
 */
define('ENABLE_CLEANCSS', false);

/**
 * Base URL:
 * To manually config this, uncomment the following line and change the URL
 * To use auto detect, keep this commented
 */
// define('BASE_URL', 'http://www.kookxiang.com');
Core\Request::autoDetectBaseURL();

/**
 * Database Connection:
 */
Core\Database::register('mysql:dbname=test;host=localhost;charset=UTF8', 'root', '');
