<?php
/**
 * KK's Laboratory (c) 2009-2014.
 * Project: KK-Framework
 * User: kookxiang
 */
namespace System\Exception;
use System\Database\Adapter;
use System\Module\Exception;

class DatabaseException extends Exception {
	/**
	 * 数据库查询错误
	 * @param string $sql 查询语句
	 * @param Adapter $adapter 数据库适配器
	 */
	function __construct($sql, $adapter){
		$message = 'A database error happen during request: '.$adapter->getErrorMessage().'<br>QUERY: '.htmlspecialchars($sql);
		$code = 30000 + $adapter->getErrorNumber();
		parent::__construct($message, $code);
	}
}
