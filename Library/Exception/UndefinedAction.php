<?php
/**
 * KK's Laboratory (c) 2009-2014.
 * Project: Qiandao.In
 * User: kookxiang
 */
namespace Exception;
use System\Module\Exception;
class UndefinedAction extends Exception {
	function __construct($message = 'Undefined Action', $code = 0, $previous = null){
		parent::__construct($message, $code, $previous);
	}
}
