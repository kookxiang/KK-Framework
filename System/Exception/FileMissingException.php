<?php
/**
 * KK's Laboratory (c) 2009-2014.
 * User: kookxiang
 */
namespace System\Exception;
use System\Module\Exception;

class FileMissingException extends Exception {
	var $customView = 'FileNotExists';
	function __construct($filePath, $solution = ''){
		$filePath = str_replace(ROOT, '', $filePath);
		$errorMessage = 'The following file was missing: '.$filePath;
		if($solution) $errorMessage .= '<br>'.$solution;
		$this->message = $errorMessage;
		$this->code = 255;
	}
}