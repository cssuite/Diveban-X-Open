<?php
/*
* Engine.php
*
* @version Superban CMS 1.6
* @author Oleksandr Kornienko
* @copyright (C)2013 Superban.net. Все права защищены.
* @contacts Skype: magoga25 ICQ: 624338780
*/

if (!defined("GUARD")) { header('Content-type: text/html; charset=utf-8'); die('Доступ запрещен!'); } // Защита файла от прямого вызова.

class Engine 
{
    static $pages = array();
	static function add_page($array) {
		self::$pages[$array['type']][] = $array;
	}
	
	static function exec_page($type) {
		if(!isset($_GET['do'])) return eval('page_'.$type.'_main();');
		foreach(self::$pages[$type] as $key=>$value) {
			if($value['url']==$_GET['do']) {
				return eval($value['name'].'();');
			}
		}
		return eval('page_default_error();');
	}
}
?>