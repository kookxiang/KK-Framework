<?php
/**
 * KK's Laboratory (c) 2009-2014.
 * Project: KK-Framework
 * User: kookxiang
 */

namespace System\Module;
use System\Exception\ViewMissingException;

class Template {
	public static function isExists($viewName){
		if(file_exists(LIBRARY_DIR."Template/{$viewName}.htm")){
			return true;
		}elseif(file_exists(SYSTEM_DIR."Template/{$viewName}.htm")){
			return true;
		}else{
			return false;
		}
	}

	public static function getPath($viewName) {
		if(file_exists(LIBRARY_DIR."Template/{$viewName}.htm")){
			return LIBRARY_DIR."Template/{$viewName}.htm";
		}elseif(file_exists(SYSTEM_DIR."Template/{$viewName}.htm")){
			return SYSTEM_DIR."Template/{$viewName}.htm";
		}else{
			throw new ViewMissingException("Library/Template/{$viewName}.htm");
		}
	}

	public static function load($viewName){
		$viewFileOrigin = self::getPath($viewName);
		$viewFile = DATA_DIR."Template/{$viewName}.php";
		if(file_exists($viewFile))
			if(filemtime($viewFile) > filemtime($viewFileOrigin))
				return $viewFile;
		self::parse($viewName);
		return $viewFile;
	}

	private static function parse($viewName) {
		$headers = '';
		$fp = @fopen(self::getPath($viewName), 'rb');
		if(!$fp) return;
		$sourceCode = '';
		while(!feof($fp))
			$sourceCode .= fread($fp, 8192);

		$lock = new PHPLock($sourceCode);
		$lock->acquire();

		// variable with braces:
		$sourceCode = preg_replace('/\{\$([A-Za-z0-9_\[\]\->]+)\}/', '<?php echo \$\\1; ?>', $sourceCode);
		$sourceCode = preg_replace('/\{([A-Z][A-Z0-9_\[\]]*)\}/', '<?php echo \\1; ?>', $sourceCode);
		$lock->acquire();

		// PHP code:
		$sourceCode = preg_replace('/<php>(.+?)<\/php>/is', '<?php \\1; ?>', $sourceCode);
		$lock->acquire();

		// import:
		$sourceCode = preg_replace('/\<import template="([A-z0-9_\-\/]+)"[\/ ]*\>/i', '<?php include \System\Module\Template::load(\'\\1\'); ?>', $sourceCode);
		$lock->acquire();

		// loop:
		$sourceCode = preg_replace_callback('/\<loop(.*?)\>/is', array('\System\Module\Template', 'parseLoop'), $sourceCode);
		$sourceCode = preg_replace('/\<\/loop\>/i', '<?php } ?>', $sourceCode);
		$lock->acquire();

		// if:
		$sourceCode = preg_replace('/\<if (?:condition=)?"(.+?)"[\/ ]*\>/i', '<?php if(\\1) { ?>', $sourceCode);
		$sourceCode = preg_replace('/\<elseif (?:condition=)?"(.+?)"[\/ ]*\>/i', '<?php } elseif(\\1) { ?>', $sourceCode);
		$sourceCode = preg_replace('/\<else[\/ ]*\>/i', '<?php } else { ?>', $sourceCode);
		$sourceCode = preg_replace('/\<\/if\>/i', '<?php } ?>', $sourceCode);
		$lock->acquire();

		// header:
		preg_match_all('/\<meta header="(.+?)" content="(.+?)"[ \/]*\>/i', $sourceCode, $matches);
		foreach ($matches[0] as $offset => $string) {
			$headers .= "header('{$matches[1][$offset]}: {$matches[2][$offset]}');".PHP_EOL;
			$sourceCode = str_replace($string, '', $sourceCode);
		}
		$lock->acquire();

		// variable without braces
		$sourceCode = preg_replace('/\$([a-z][A-Za-z0-9_]+)/', '<?php echo \$\\1; ?>', $sourceCode);
		// unlock PHP code
		$lock->release();

		// clear space and tab
		$sourceCode = preg_replace('/^[ \t]*(.+)[ \t]*$/m', '\\1', $sourceCode);

		$output = '<?php'.PHP_EOL;
		$output .= 'if(!defined(\'IN_FRAMEWORK\'))';
		$output .= ' exit(\'Direct access is not allowed\');'.PHP_EOL;
		if($headers) $output .= $headers;
		$output .= '?>'.PHP_EOL;
		$output .= $sourceCode;
		$output = preg_replace('/\s*\?\>\s*\<\?php\s*/is', PHP_EOL, $output);

		self::createDir(dirname(DATA_DIR."Template/{$viewName}.php"));
		if(!file_exists(DATA_DIR."Template/{$viewName}.php"))
			@touch(DATA_DIR."Template/{$viewName}.php");
		if(!is_writable(DATA_DIR."Template/{$viewName}.php")){
			throw new Exception('Cannot write template file: '.DATA_DIR."Template/{$viewName}.php", -8);
		}
		file_put_contents(DATA_DIR."Template/{$viewName}.php", $output);
	}

	public static function parseLoop($match){
		$variable = self::preg_get($match[1], '/variable="([^"]+)"/i');
		if(!$variable)
			$variable = self::preg_get($match[1], '/^\s*"([^"]+)"/i');
		if(!$variable)
			throw new Exception('Cannot convert loop label: '.htmlspecialchars($match[0]), 102);
		$query = self::preg_get($match[1], '/query="([^"]+)"/i');
		if($query)
			return '<?php while ('.$variable.' = '.($query).'->getRow()) { ?>';
		$key = self::preg_get($match[1], '/key="([^"]+)"/i');
		$value = self::preg_get($match[1], '/value="([^"]+)"/i');
		return '<?php foreach ('.$variable.' as '.($key ? $key : '$key').' => '.($value ? $value : '$value').') { ?>';
	}

	private static function preg_get($subject, $pattern, $offset = 1){
		if(!preg_match($pattern, $subject, $matches)) return null;
		return $matches[ $offset ];
	}

	private static function createDir($dir, $permission = 0777){
		if(is_dir($dir)) return;
		self::createDir(dirname($dir), $permission);
		@mkdir($dir, $permission);
	}
}