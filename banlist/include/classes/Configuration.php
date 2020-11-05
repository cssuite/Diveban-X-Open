<?php
/*
* Configuration.php
*
* @version Superban CMS 1.6
* @author Oleksandr Kornienko
* @copyright (C)2013 Superban.net. Все права защищены.
* @contacts Skype: magoga25 ICQ: 624338780
*/

if (!defined("GUARD")) { header('Content-type: text/html; charset=utf-8'); die('Доступ запрещен!'); } // Защита файла от прямого вызова.

class Configuration 
{
	static $main = array(			// Глобальные настройки
		'm_title' => 'Бан-лист сервера.',
		'm_desc' => 'Список забаненных игроков на сервере.',
		'm_keys' => 'Бан-лист, банлист, бан лист, бан , лист , список забаненных, список, забаненных, баны, bans, ban list, banlist, ban, list, Ник, Причина, Истекает, Забанен, Разбан',
		'm_sitepatch' => '',
		'm_logo' => '',
		'm_fon' => '',
		'm_popover' => 'top',
		'm_website' => '',
		'm_webtheme' => '',
		'm_purse' => '',
		'm_cost' => '',
		'm_secret_key' => '',
		);
	static $pagination = array( 	// Постраничная навигация
		'p_main' => '50',
		);
	static $db = array( 			// Настройки mysql
        'db_serv' => '',
        'db_user' => '',
        'db_pass' => '',
        'db_name' => '',
		);

	static $banlist = [
	    'table' => 'divebanx',
        'users_table' => 'suite_users',
        'servers_table' => 'suite_servers',
        'admins_table'  => 'diveban_admins'
	    ];
}
?>