<?php
/*
* core.php
*
* @version Superban CMS 1.6
* @author Oleksandr Kornienko
* @copyright (C)2013 Superban.net. Все права защищены.
* @contacts Skype: magoga25 ICQ: 624338780
*/

if (!defined("GUARD")) { header('Content-type: text/html; charset=utf-8'); die('Доступ запрещен!'); } // Защита файлов от прямого вызова

if(file_exists('install') and !file_exists('install/install.lock')) header('Location: install/');

function core_include($r) {
$r_dir = opendir('include/'.$r.'/');
	while(($r_file = readdir($r_dir))!=false)
		if($r_file != "." && $r_file != "..")
			include_once 'include/'.$r.'/'.$r_file;
}

core_include('classes');
core_include('functions');
core_include('pages');
?>