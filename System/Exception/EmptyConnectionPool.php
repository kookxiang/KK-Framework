<?php
/**
 * KK's Laboratory (c) 2009-2014.
 * Project: KK-Framework
 * User: kookxiang
 */
namespace System\Exception;
use System\Module\Exception;

class EmptyConnectionPool extends Exception {
	protected $message = 'No available database connection';
	protected $code = -2;
}