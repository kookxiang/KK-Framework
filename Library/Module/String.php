<?php
/**
 * KK's Laboratory (c) 2009-2014.
 * Project: Qiandao.In
 * User: kookxiang
 */
namespace Module;

use System\Module\Exception;

class String {
	const TYPE_STRING = 0;
	const TYPE_EMAIL = 1;
	public static function fixStr($string, $minLen = 0, $maxLen = 0, $type = self::TYPE_STRING){
		$string = trim($string);
		if($minLen){
			if(strlen($string) < $minLen)
				throw new Exception("Illegal string '{$string}'");
		}
		if($maxLen){
			if(strlen($string) > $maxLen)
				throw new Exception("Illegal string '{$string}'");
		}
		switch($type){
			default:
			case self::TYPE_STRING:
				break;
			case self::TYPE_EMAIL:
				if(!preg_match('/^[A-z0-9._-]+@[A-z0-9._-]+\.[A-z0-9._-]+$/', $string))
					throw new Exception("{$string} seems not like an email address.");
				break;
		}
		return $string;
	}
} 