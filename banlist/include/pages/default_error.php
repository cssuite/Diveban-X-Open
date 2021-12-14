<?php
/*
* default_error.php
*
* @version Superban CMS 1.6
* @author Oleksandr Kornienko
* @copyright (C)2013 Superban.net. Все права защищены.
* @contacts Skype: magoga25 ICQ: 624338780
*/

if (!defined("GUARD")) { header('Content-type: text/html; charset=utf-8'); die('Доступ запрещен!'); } // Защита файла от прямого вызова.

function page_default_error() 
{
	Template::subtemplate_load('{index_content}', 'style/error.tpl');
	$status = $_SERVER['REDIRECT_STATUS'];
	$codes = array(
		400 => array('400 - Плохой запрос', 'Запрос не может быть обработан из-за синтаксической ошибки.'),
		403 => array('403 - Запрещено', 'Сервер отказывает в выполнении вашего запроса.'),
		404 => array('404 - Не найдено', 'Запрашиваемая страница не найдена на сервере.'),
		405 => array('405 - Метод не допускается', 'Указанный в запросе метод не допускается для заданного ресурса.'),
		408 => array('408 - Время ожидания истекло', 'Ваш браузер не отправил информацию на сервер за отведенное время.'),
		500 => array('500 - Внутренняя ошибка сервера', 'Запрос не может быть обработан из-за внутренней ошибки сервера.'),
		502 => array('502 - Плохой шлюз', 'Сервер получил неправильный ответ при попытке передачи запроса.'),
		504 => array('504 - Истекло время ожидания шлюза', 'Вышестоящий сервер не ответил за установленное время.'),
	);
	
	$title = $codes[$status][0];
	$content = $codes[$status][1];
	
	if ($codes[$status][0] == false || strlen($status) != 3) 
	{
		$title = $codes[404][0];
		$content = $codes[404][1];
	} 
	
	Template::tag('{title_name}', $title);
	Template::tag('{page_title}', $title);
	Template::tag('{page_content}', $content);
	return true;
}

	Engine::add_page(array('name'=>'page_default_error', 'url'=>'error', 'type'=>'default'));

?>