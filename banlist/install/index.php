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
session_start();
include '../include/classes/Template.php';
include '../include/classes/Templater.php';
include '../include/classes/Configuration.php';

function save_conf($array)
{
    $file = '../include/classes/Configuration.php';
    if (file_exists($file)) {
        $data = '';
        $file_content = fopen($file, "r");
        while (!feof($file_content)) {
            $buffer = fgets($file_content, 4096);
            $data_edit = false;
            foreach ($array as $key => $value) if (stripos($buffer, $key)) {
                $data .= "		'" . $key . "' => '" . stripslashes($value) . "'," . "\n";
                $data_edit = true;
            }
            if (!$data_edit) $data .= $buffer;
        }
        fclose($file_content);
        $file_content = fopen($file, "w+");
        fwrite($file_content, $data);
        fclose($file_content);
    } else
        return 'Файл ' . $file . ' не найден';
}

Template::template_load('index.tpl');

if (!file_exists('install.lock')) {
    if (isset($_GET['step']))
        if ($_GET['step'] == 1) {
            $error = false;
            $chmod_list = array('include', 'include/classes', 'include/classes/Configuration.php', 'install');
            $content = '<div style="padding:5px;"> 
				<div style="font: bold 16px Arial;"><b>Проверка прав файлов.</b></div><br>
				<div style="text-align:left;">';
            foreach ($chmod_list as $chmod_name) {
                if (substr(sprintf('%o', fileperms('../' . $chmod_name)), -4) == '0777')
                    $content .= '<font color="green">Права на запись файла или папки "<b>' . $chmod_name . '</b>" выстевлены.</font><br>';
                else {
                    $content .= '<font color="red">Поставьте права 777 на файл "<b>' . $chmod_name . '</b>"</font><br>';
                    $error = true;
                }
            }
            $content .= '</div><br /><a href="{url}index.php?step=1" class="btn btn-info" >Обновить</a>';
            if (!$error) $content .= ' <br><hr><a href="{url}index.php"><button class="btn btn-info" ><i class="icon-chevron-left icon-white"></i> Назад</button></a> <a href="{url}index.php?step=2"><button class="btn btn-info" >Продолжить <i class="icon-chevron-right icon-white"></i></button></a>';
            $content .= '</div>';
        } else if ($_GET['step'] == 2) {
            $server = $_POST['db_serv'] ?? 'localhost';
            $user = $_POST['db_user'] ?? 'root';
            $password = $_POST['db_pass'] ?? '';
            $db = $_POST['db_name'] ?? '';

            $content = '<div style="padding:5px;">
			<div style="font: bold 16px Arial;"><b>Подключение к базе данных.</b></div><br>
			<form method="POST" action="{url}index.php?step=2">
			    <div class="form-group">
                    <label>Сервер БД</label>
                    <input type="text" class="form-control" name="db_serv" value="' . $server . '">
                </div>
                <div class="form-group">
                    <label>Пользователь</label>
                    <input type="text" class="form-control" name="db_user" value="' . $user . '">
                </div>
                <div class="form-group">
                    <label>Пароль</label>
                    <input type="text" class="form-control" name="db_pass" value="' . $password . '">
                </div>
                <div class="form-group">
                    <label>Название БД</label>
                    <input type="text" class="form-control" name="db_name" value="' . $db . '">
                </div>
                <button type="submit" name="connect" class="btn btn-default">Подключиться</button></form>';
            if (isset($_POST['connect'])) {
                $error = false;
                $database = mysqli_connect($_POST['db_serv'], $_POST['db_user'], $_POST['db_pass'],$_POST['db_name']);

                if (mysqli_connect_errno()) {
                    $error = mysqli_connect_error();
                }
                if ($error) {
                    $content .= '<hr><div class="alert alert-error"><b>Ошибка подключения к базе данных: "' . $error . '"</b></div>';
                } else {
                    $content .= '<hr><div class="alert alert-success"><b>Подключение к базе данных прошло успешно!</b></div><a href="{url}index.php?step=1"><button class="btn btn-info" ><i class="icon-chevron-left icon-white"></i> Назад</button></a> <a href="{url}index.php?step=3"><button class="btn btn-info" >Продолжить <i class="icon-chevron-right icon-white"></i></button></a>';
                    save_conf(array('db_serv' => $_POST['db_serv'], 'db_user' => $_POST['db_user'], 'db_pass' => $_POST['db_pass'], 'db_name' => $_POST['db_name']));
                    $sql = file_get_contents('diveban.sql');
                    $sql_query = explode(";", $sql);
                    foreach ($sql_query as $sql_row)
                        if (isset($sql_row) and !empty($sql_row)) mysqli_query($database, $sql_row) or die(mysqli_error($database));
                }
            }
            $content .= '</div>';
        } else if ($_GET['step'] == 3) {
            $content = '<div style="padding:5px;">
			<div style="font: bold 16px Arial;"><b>Создание Администратора.</b></div><br>
			<form method="POST" action="{url}index.php?step=3">
				<table class="table" width="100%">
					<tr><td><b>Ник Администратора:</b> </td> <td align="left" colspan="3"><input class="form-control" type="text" name="name"></td></tr>
					<tr><td><b>Пароль Администратора:</b> </td> <td align="left"><input class="form-control" type="text" name="password"></td></tr>
					<tr><td><b>Повторите пароль Администратора:</b> </td> <td align="left"><input class="form-control" type="text" name="password2"></td></tr>
				</table><br /><input type="submit" class="btn btn-info" name="add" value="Создать">
			</form>
			';
            if (isset($_POST['add'])) {
                if (mb_strtolower($_POST['password']) == mb_strtolower($_POST['password2'])) {
                    $error = false;
                    $database = mysqli_connect(Configuration::$db['db_serv'], Configuration::$db['db_user'], Configuration::$db['db_pass'], Configuration::$db['db_name']) or $error = mysqli_connect_error();
                    @mysqli_query($database,"INSERT INTO `suite_users` (`name`, `password`, `group`) VALUES ('" . mysql_escape_string(mb_strtolower($_POST['name'])) . "', '" . md5(mb_strtolower($_POST['password'])) . "', '1')") or $error = mysqli_error($database);
                    if ($error) {
                        $content .= '<div class="alert alert-error"><b>Ошибка базы данных: "' . $error . '"</b></div>';
                    } else {
                        $content .= '<hr><div class="alert alert-success"><b>Администратор успешно создан!</b></div><a href="{url}index.php?step=2"><button class="btn btn-info" ><i class="icon-chevron-left icon-white"></i> Назад</button></a> <a href="{url}index.php?step=4"><button class="btn btn-info" >Продолжить <i class="icon-chevron-right icon-white"></i></button></a>';
                        $_SESSION['admin_login'] = mysql_escape_string(mb_strtolower($_POST['name']));
                        $_SESSION['admin_password'] = mb_strtolower($_POST['password']);
                    }
                } else {
                    $content .= '<div class="alert alert-error"><b>Пароли не совпадают.</b></div>';
                }
            }
            $content .= '</div>';
        } else if ($_GET['step'] == 4) {
            $popover = '<span data-html="true" data-placement="right" rel="popover" data-trigger="hover" data-content="<b>Направо:</b> right <br /> <b>Налево:</b> left <br /> <b>Вверх:</b> top <br /> <b>Вниз:</b> bottom" data-original-title="Вывод подсказки"><i class="material-icons" style="font-size: 18px">question_answer</i></span>';
            $content = '<div style="padding:5px;">
			<form method="POST" action="{url}index.php?step=5">
				<div class="well well-small" style="color: black; background-color: #d9edf7">
					<table class="table" width="100%">
						<div class="alert alert-success">
							<h4 class="alert-heading">Настройка Мета - Тегов</h4>
						</div>
						<tr> <td><b style="margin-right: 255px;"><span style="color:red;">*</span> Название:</b> </td> <td><input class="form-control" type="text" name="conf[m_title]" style="width:300px" value="Бан-лист сервера."></td> </tr>
						<tr> <td><b><span style="color:red;">*</span> Описания:</b> </td> <td><input class="form-control" type="text" name="conf[m_desc]" style="width: 300px;" value="Список забаненных игроков на сервере."></td> </tr>
						<tr> <td><b><span style="color:red;">*</span> Ключевые слова:</b> </td> <td><input class="form-control" type="text" name="conf[m_keys]" style="width: 300px;" value="Бан-лист, банлист, бан лист, бан , лист , список забаненных, список, забаненных, баны, bans, ban list, banlist, ban, list, Ник, Причина, Истекает, Забанен, Разбан"></td> </tr>
					</table>
					<table class="table" width="100%">	
						<div class="alert alert-success">
						<h4 class="alert-heading">Глобальные настройки</h4>
						</div>
						<tr> <td><b><span style="color:red;">*</span> Url бан-листа ( http://site.ru/bans/ или http://site.ru/ ):<br /> <span style="color:red;">( Обязательно с флешем " / " )</span></b> </td> <td><input type="text" class="form-control" name="conf[m_sitepatch]" style="width:300px"></td> </tr>
						<tr> <td><b><span style="color:red;">*</span> Ссылка на Логотип:</b> </td> <td><input type="text" class="form-control" name="conf[m_logo]" style="width:300px" value="http://csbans.ru/uploads/posts/2013-02/thumbs/1360593865_amxbans5.png"></td> </tr>
						<tr> <td><b><span style="color:red;">*</span> Ссылка на Фон:</b> </td> <td><input type="text" class="form-control" name="conf[m_fon]" style="width:300px" value="/style/img/background.jpg"></td> </tr>
						<tr> <td><b><span style="color:red;">*</span> Вывод подсказки:</b>'.$popover.'</td> <td><input type="text" class="form-control" name="conf[m_popover]" style="width:300px" value="top"></td> </tr>
						<tr> <td><b><span style="color:red;">*</span> Количество банов на одной странице:</b> </td> <td><input class="form-control" type="text" name="conf[p_main]" style="width:300px" value="15"></td> </tr>
					</table>	
					<table class="table" width="100%">
						<div class="alert alert-success">
							<h4 class="alert-heading">Другое</h4>
						</div>
						<tr> <td><b style="margin-right: 195px;"><span style="color:silver;">*</span> Форум/Сайт/Группа:</b> </td> <td><input class="form-control" type="text" name="conf[m_website]" style="width:300px"></td> </tr>
						<tr> <td><b><span style="color:silver;">*</span> Тема на Unban:</b> </td> <td><input type="text" class="form-control" name="conf[m_webtheme]" style="width:300px"></td> </tr>
					</table>	
					<br /><input type="submit" name="add" value="Сохранить" class="btn btn-info">
				</div>
			</form>
			</div>
			';
        } else if ($_GET['step'] == 5) {

            $create_file = fopen("install.lock", "w+");
            if (isset($_POST['conf'])) save_conf($_POST['conf']);
            $content = '
			<div style="padding:10px;" align="left">
				<div class="alert alert-success"><div style="font: bold 16px Arial;text-align:center;"><b>Скрипт установлен!</b></div></div>
				<div class="alert alert-danger">
					<div style="font: bold 14px Arial;"><b>Информация:</b></div><br>
					<b>Ваш логин: ' . $_SESSION['admin_login'] . '</b><br>
					<b>Ваш пароль: ' . $_SESSION['admin_password'] . '</b><br><br>
				</div>
				<a href="../admin.php"><button class="btn btn-info" ><i class="icon-user icon-white"></i> Вход в панель администратора</button></a>  <a href="../index.php"><button class="btn btn-info" ><i class="icon-home icon-white"></i> Перейти на сайт</button></a>
			</div>';
            //mail("superban2014@mail.ru", "http://".$_SERVER['SERVER_NAME']."/ Installed", "http://".$_SERVER['SERVER_NAME']."/ - Installed! [ ".date('d.m.Y H:m:s')." ]", "From: ".$_SERVER['SERVER_NAME']." \r\n");
        } else {
            $content = '<div style="padding:5px;">Страница не найдена.</div>';
        }
    else {
        $content .= '
		<div style="padding:10px;" align="left">
			<div style="font: bold 14px Arial;"><b>Возможности скрипта:</b></div><br>
			 <b>
			 + Приятный дизайн.<br>
			 + Панель Администратора.<br>
			 + Редактирование и удаление банов.<br>
			 + Редактирование времени бана.<br>
			 + Упрощенная система управления администраторами сайта.<br>
			 + Система групп администраторов сайта: Гл. Администратор, Администратор и Модератор.<br>
			 + Возможность автоматического поиска и показа на сайте сходного ip.<br>
			 + Добавление и редактирование игровых серверов на сайте.<br>
			 + Мониторинг игровых серверов.<br>
			 + Редактирование настроек сайта из панели администратора.<br>
			 + Статистика банов, последний бан.<br>
			 + Навигация на сайте.<br>
			 + Быстрый поиск забаненого игрока.<br>
			 + Список администрации сервера.</b>
			 
			<div style="text-align:center;"><a href="{url}index.php?step=1"><button class="btn btn-info btn-info" >Установить <i class="icon-play icon-white"></i></button></a></div>
		</div>
		';
    }
} else {
    $content .= '
	
	<div class="alert alert-error" style="margin: 0;">
		<h4 class="alert-heading">Ошибка!</h4><p>Установка скрипта заблокирована, для разблокировки удалите файл "<b>install.lock</b>" из папки "<b>install</b>".</p>
	</div>
	';
}

$steps = '';
$step = $_GET['step'] ? $_GET['step'] : 0;
$steps_list = array(
    0 => array('16%', 'Ознакомление'),
    1 => array('32%', 'Проверка файлов'),
    2 => array('48%', 'Подключение к БД'),
    3 => array('64%', 'Создание администратора'),
    4 => array('80%', 'Редактирование настроек'),
    5 => array('100%', 'Завершение установки'),
);

$num_steps = $steps_list[$step][0];
$steps = $steps_list[$step][1];

Template::tag('{num_steps}', $num_steps);
Template::tag('{steps}', $steps);
Template::tag('{page_content}', $content);

Template::tag('{title_name}', 'Установка скрипта.');
Template::tag('{global_name}', Configuration::$main['m_title']);
Template::tag('{title_desc}', Configuration::$main['m_desc']);
Template::tag('{title_keys}', Configuration::$main['m_keys']);

Template::tag('{url}', '');
Template::tag('{web_site}', Configuration::$main['m_website']);
Template::tag('{web_logo}', Configuration::$main['m_logo']);
Template::tag('{web_fon}', Configuration::$main['m_fon']);

echo Template::$compiler;
?>