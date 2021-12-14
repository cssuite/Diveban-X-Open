<?php
/*
* default_logout.php
*
* @version Superban CMS 1.6
* @author Oleksandr Kornienko
* @copyright (C)2013 Superban.net. Все права защищены.
* @contacts Skype: magoga25 ICQ: 624338780
*/

if (!defined("GUARD")) { header('Content-type: text/html; charset=utf-8'); die('Доступ запрещен!'); } // Защита файла от прямого вызова.

function page_default_logout() 
{
	User::logout();
	header('Location: index.php');
}

	Engine::add_page(array('name'=>'page_default_logout', 'url'=>'logout', 'type'=>'default'));

?>