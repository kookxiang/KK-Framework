<?php
/**
 * KK's Laboratory (c) 2009-2014.
 * Project: Qiandao.In
 * User: kookxiang
 */
namespace Module;

class Password {
	const ENCRYPT_KEY = 'hvM3TeM8RImMGTQnyLXDkeFwhCBw4LAo';
	const ENCRYPT_TYPE_DEFAULT = 0;
	const ENCRYPT_TYPE_ENHANCE = 1;

	public static function verify($user, $password){
		list($user_password, $encrypt_type) = explode('T', $user['password']);
		if($encrypt_type == self::ENCRYPT_TYPE_DEFAULT){
			return $user_password == md5(self::ENCRYPT_KEY.md5($password).self::ENCRYPT_KEY);
		}elseif($encrypt_type == self::ENCRYPT_TYPE_ENHANCE){
			$salt = substr(md5($user['uid'].$user['username'].self::ENCRYPT_KEY), 8, 16);
			return $user_password == substr(md5(md5($password).$salt), 0, 30);
		}
		return false;
	}

	public static function encrypt($user, $password){
		$salt = substr(md5($user->uid.$user->username.self::ENCRYPT_KEY), 8, 16);
		return substr(md5(md5($password).$salt), 0, 30).'T'.self::ENCRYPT_TYPE_ENHANCE;
	}
}
