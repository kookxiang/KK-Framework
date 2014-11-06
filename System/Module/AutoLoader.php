<?php
/**
 * KK's Laboratory (c) 2009-2014.
 * User: kookxiang
 */
namespace System\Module;
use System\Exception\SystemFileMissing;
use System\Exception\FileMissingException;

class AutoLoader{
	/**
	 * 自动加载所需模块
	 * @param $className String 模块名
	 */
	public static function load($className){
		if(strpos($className, 'System\\') === 0){
			self::loadSystemModule($className);
		}else{
			self::loadLibraryModule($className);
		}
	}

	/**
	 * 加载系统模块
	 * @param $className String 模块名
	 * @throws SystemFileMissing 找不到需加载的模块时抛出异常
	 */
	private static function loadSystemModule($className){
		$classPath = ROOT.ltrim(str_replace('\\', '/', $className), '/').'.php';
		if(file_exists($classPath)){
			include $classPath;
		}else{
			throw new SystemFileMissing($classPath, 'You should download the missing file manually.');
		}
	}

	/**
	 * 加载自定义模块
	 * @param $className String 模块名
	 * @throws FileMissingException 找不到需加载的模块时抛出异常
	 */
	private static function loadLibraryModule($className){
		$classPath = LIBRARY_DIR.ltrim(str_replace('\\', '/', $className), '/').'.php';
		if(file_exists($classPath)){
			include $classPath;
		}else{
			throw new FileMissingException($classPath);
		}
	}
}
