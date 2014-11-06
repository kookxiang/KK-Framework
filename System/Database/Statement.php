<?php
/**
 * KK's Laboratory (c) 2009-2014.
 * Project: KK-Framework
 * User: kookxiang
 */
namespace System\Database;

class Statement{
	private $statement;

	public function __construct($sql){
		$this->statement = $sql;
	}

	/**
	 * 格式化 SQL 语句，将参数代入语句中
	 *
	 * %d    整型数字
	 * %f    浮点小数
	 * %s    字符串
	 * %a    枚举数组
	 */
	public function format(){
		$statement = $lastChar = '';
		$length = strlen($this->statement);
		$args = func_get_args();
		$index = 0;
		for($i = 0; $i < $length; $i++){
			$char = $this->statement{$i};
			if($lastChar == '%'){
				switch($char){
					case 'd':
						$statement .= self::formatInt($args[$index]);
						break;
					case 'f':
						$statement .= self::formatFloat($args[$index]);
						break;
					case 's':
						$statement .= self::formatString($args[$index]);
						break;
					case 'a':
						$statement .= self::formatArray($args[$index]);
						break;
				}
				$lastChar = $char;
				$index++;
			}elseif($char == '%'){
				$lastChar = $char;
			}else{
				$statement .= $lastChar = $char;
			}
		}
		$this->statement = $statement;
	}

	private static function formatInt($string){
		return intval($string);
	}

	private static function formatFloat($string){
		return sprintf('%F', $string);
	}

	private static function formatString($string){
		return "'".addslashes($string)."'";
	}

	private static function formatArray($array){
		$return = '';
		foreach($array as $value){
			if($return) $return .= ', ';
			$return .= "'".addslashes($value)."'";
		}
		return $return;
	}

	public function __toString(){
		return $this->statement;
	}
}