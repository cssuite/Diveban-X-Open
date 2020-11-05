<?php
/*
* Template.php
*
* @version Superban CMS 1.6
* @author Oleksandr Kornienko
* @copyright (C)2013 Superban.net. Все права защищены.
* @contacts Skype: magoga25 ICQ: 624338780
*/

if (!defined("GUARD")) { header('Content-type: text/html; charset=utf-8'); die('Доступ запрещен!'); } // Защита файла от прямого вызова.

class Template {
    static $dir = '.';
	static $compiler = array();
	static $tmp = array();

	static function template_load($name) {
        if ($name == '' || !file_exists(self::$dir . DIRECTORY_SEPARATOR . $name)) {
			header('Content-type: text/html; charset=utf-8');
			die ("Ошибка загрузки шаблона: ". $name); 
			return false;
		}
        self::$compiler = file_get_contents(self::$dir . DIRECTORY_SEPARATOR . $name);
        return self::$compiler;
    }
	
	static function subtemplate_load($find,$name) {
        if ($name == '' || !file_exists(self::$dir . DIRECTORY_SEPARATOR . $name)) {
			header('Content-type: text/html; charset=utf-8');
			die ("Ошибка загрузки шаблона: ". $name); 
			return false;
		}
		self::$compiler = str_replace($find, file_get_contents(self::$dir . DIRECTORY_SEPARATOR . $name), self::$compiler);
        return self::$compiler;
    }
	
	static function tag($find, $replace) {
		self::$compiler = str_replace($find, $replace, self::$compiler);
		return self::$compiler;
	}
	
	static function code_compiler($type) {
		if(isset(self::$tmp[$type])) {
			$code = '';
			foreach(self::$tmp[$type] as $key => $value)
				$code .= $value."\n";
			return $code;
		}
		return false;
	}
	
	static function code_add($type, $content) {
		return self::$tmp[$type][] = $content;
	}
}
?>