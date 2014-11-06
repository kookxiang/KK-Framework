<?php
/**
 * KK's Laboratory (c) 2009-2014.
 * Project: Qiandao.In
 * User: kookxiang
 */
namespace Module;
use Exception\WrongPasswordException;
use System\Module\Exception;
use Exception\UserNotExistsException;

class User {
	public $uid;
	public $username;
	private $email;
	public $formhash;
	const COOKIE_KEY = 'Jn0KsK7GAgsC2tU4f4wErMmuIWrvNW4X';
	private static $instance;

	/**
	 * 获取已登陆的用户对象
	 * @return User 用户对象
	 */
	public static function getInstance() {
		if (!self::$instance) self::$instance = new self();
		return self::$instance;
	}

	public function __construct() {
		$cookie = Encrypt::decode($_COOKIE['auth'], self::COOKIE_KEY);
		if (!$cookie) return;
		list($this->uid, $this->username) = explode("\t", $cookie);
		$this->generateFormHash();
	}

	private function generateFormHash(){
		$info = array(
			substr(time(), 0, -7),
			$this->username,
			$this->uid,
			self::COOKIE_KEY,
			ROOT,
		);
		$this->formhash = substr(md5(implode($info, '')), 8, 8);
	}

	private function saveLoginStatus(){
		$cookie = Encrypt::encode($this->uid . "\t" . $this->username, self::COOKIE_KEY);
		self::setCookie('auth', $cookie, 86400*30);
	}

	private static function setCookie($key, $value = null, $ttl = 0, $httpOnly = true){
		if($ttl > 0) $ttl += time();
		if($value === null) $ttl = -1;
		return setcookie($key, $value, $ttl, '/', 'payment.ikk.me', false, $httpOnly);
	}

	/**
	 * 退出登陆
	 */
	public function logOut(){
		self::setCookie('auth', null);
		$this->uid = 0;
		$this->username = null;
		$this->generateFormHash();
	}

	/**
	 * 使用用户名/密码登陆
	 * @param $username String 用户名
	 * @param $password String 密码
	 * @throws \Exception\UserNotExistsException 用户不存在
	 * @throws \Exception\WrongPasswordException 密码错误
	 * @return User 登陆后的用户对象
	 */
	public function loginByAuth($username, $password) {
		global $db;
		$user = $db->query('SELECT * FROM member WHERE username=%s', $username)->getRow();
		if (!$user) throw new UserNotExistsException;
		if(!Password::verify($user, $password)) throw new WrongPasswordException();
		$this->uid = $user['uid'];
		$this->username = $user['username'];
		$this->saveLoginStatus();
		$this->generateFormHash();
		return $this;
	}

	/**
	 * 注册新用户
	 * @param $username String 用户名
	 * @param $password String 密码
	 * @param $email String 邮箱
	 * @return User 新注册用户的对象
	 * @throws \System\Module\Exception
	 */
	public function registerNewUser($username, $password, $email){
		global $db;
		$this->username = String::fixStr($username, 6, 32);
		$this->email = String::fixStr($email, 5, 0, String::TYPE_EMAIL);
		if($db->query('SELECT * FROM member WHERE username=%s OR email=%s', $this->username, $this->email)->getRow())
			throw new Exception('用户名 / 邮箱冲突，请勿重复注册', 20001);
		$this->uid = $db->query('INSERT INTO member SET username=%s, email=%s', $this->username, $this->email)->getInsertId();
		$password = Password::encrypt($this, String::fixStr($password));
		$db->query('UPDATE member SET password=%s WHERE uid=%d', $password, $this->uid);
		$this->saveLoginStatus();
		return $this;
	}
}
