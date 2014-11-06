<?php
/**
 * KK's Laboratory (c) 2009-2014.
 * Project: KK-Framework
 * User: kookxiang
 */
namespace System\Module;

class Exception extends \Exception {
	private $trace;
	var $customView;

	/**
	 * 打印一个异常
	 * @param string $message 错误说明
	 * @param int $code 错误码
	 * @param \Exception $previous 上一错误
	 * @param array $trace Trace Log
	 */
	function __construct($message = '', $code = 0, \Exception $previous = NULL, $trace = array()){
		parent::__construct($message, $code, $previous);
		$this->trace = $trace;
		if(!$trace) $this->trace = debug_backtrace();
	}

	/**
	 * 获得错误追溯信息
	 * @return string 错误追溯信息
	 */
	private function getBackTrace() {
		$backtrace = $this->trace;
		krsort($backtrace);
		$trace = '';
		foreach ($backtrace as $error) {
			if($error['function'] == 'spl_autoload_call') continue;
			if($error['function'] == 'getBackTrace') continue;
			$error['line'] = $error['line'] ? " (Line:{$error['line']})" : '';
			$file = ltrim(str_replace(rtrim(ROOT, '/'), '', $error['file']), '/\\');
			if(!$file) $file = "{$error['class']}{$error['type']}{$error['function']}";
			$file = str_replace('\\', '/', $file);
			$trace .= "{$file}{$error['line']}\r\n";
		}
		return $trace;
	}

	public static function handleException(\Exception $e){
		@ob_end_clean();
		$instance = new self($e->getMessage(), intval($e->getCode()), $e, $e->getTrace());
		$trace = $instance->getBackTrace();
		if($e instanceof Exception){
			$viewName = $e->customView() ? $e->customView : 'Exception';
		}else{
			$viewName = 'Exception';
		}
		include Template::load($viewName);
		exit();
	}

	function customView(){
		if(!$this->customView) return false;
		return Template::isExists($this->customView);
	}
}
