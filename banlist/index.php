<?php
/*
* index.php
*
* @version Superban CMS 1.6
* @author Oleksandr Kornienko
* @copyright (C)2013 Superban.net. Все права защищены.
* @contacts Skype: magoga25 ICQ: 624338780
*/

define('GUARD', true);
// Ядро
include 'include/core.php';

// Глобальний стиль
Template::template_load('style/index.tpl');

// Авторизация
admin_panel();

// Загрузка страниц
Engine::exec_page('default');

// Создание тегов
Template::tag('{url}', Configuration::$main['m_sitepatch']);
Template::tag('{web_logo}', Configuration::$main['m_logo']);
Template::tag('{web_fon}', Configuration::$main['m_fon']);
Template::tag('{global_name}', Configuration::$main['m_title']);
Template::tag('{title_desc}', Configuration::$main['m_desc']);
Template::tag('{title_keys}', Configuration::$main['m_keys']);
Template::tag('{web_site}', Configuration::$main['m_website']);
Template::tag('{web_theme}', Configuration::$main['m_webtheme']);

echo Template::$compiler;
?>