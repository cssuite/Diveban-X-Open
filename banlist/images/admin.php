<?php
/*
* admin.php
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

// Проверка на авторизацию
$user = User::login();
if(!isset($user['error'])){
	Engine::exec_page('admin');
} else {
	Template::subtemplate_load('{index_content}', 'style/main.tpl');
	Template::tag('{page_content}', '
	<div class="alert alert-error">
			<table width="90%">
				<tr>
					<td><img src="{url}style/img/oshibka.png" width="150" height="150"/></td>
					<td><center><h4 class="alert-heading">Ошибка!</h4> <p><b>Вы не авторизированы!<b></p></center></td>
				</tr>
			</table>
		</div>');
}

// Создание тегов
Template::tag('{url}', Configuration::$main['m_sitepatch']);
Template::tag('{web_logo}', Configuration::$main['m_logo']);
Template::tag('{web_fon}', Configuration::$main['m_fon']);
Template::tag('{global_name}', Configuration::$main['m_title']);
Template::tag('{title_desc}', Configuration::$main['m_desc']);
Template::tag('{title_keys}', Configuration::$main['m_keys']);
Template::tag('{web_site}', Configuration::$main['m_website']);
Template::tag('{title_name}', 'Панель управления');

echo Template::$compiler;
?>