<?php
/*
* admin_main.php
*
* @version Superban CMS 1.6
* @author Oleksandr Kornienko
* @copyright (C)2013 Superban.net. Все права защищены.
* @contacts Skype: magoga25 ICQ: 624338780
*/

if (!defined("GUARD")) { header('Content-type: text/html; charset=utf-8'); die('Доступ запрещен!'); } // Защита файла от прямого вызова.

function page_admin_main() 
{
	Template::subtemplate_load('{index_content}', 'style/main.tpl');
	$user = User::login();
	
	require_once("include/inc/clear.php");
	
	$d_error = ini_get('display_errors') ? 'Вкл' : 'Выкл';
	$m_bcmath = extension_loaded('bcmath') ? 'Да' : 'Нет';
	$m_gmp = extension_loaded('gmp') ? 'Да' : 'Нет';
	$m_gd = extension_loaded('gd') ? 'Да' : 'Нет';

	$adminTab = admin_menu_main(1, 1);
	$content = '
	<div class="well well-small">
		'. $adminTab .'
		
		<ul class="breadcrumb alert alert-info">
			<li class="active">Главная<span class="divider"></span></li>
		</ul>

		<div class="row">
			<div class="col-lg-8" style="width: 600px;">
				<table width="100%" class="table table-bordered">
					<thead class="alert-info" style="color: black;">
						<th>Информация о системе</th>
						<th><i class="icon-bullhorn"></i></th>
					</thead>
					<tr>
						<td><b>Версия SuiteCMS</b></td>
						<td>3.0</td>
					</tr>
					<tr>
						<td><b>Версия PHP</b></td>
						<td>'.PHP_VERSION.'</td>
					</tr>
					<tr>
						<td><b>Версия Mysql</b></td>
						<td>'.mysqli_get_server_info(DataBase::getInstance()->mysqli).'</td>
					</tr>
					<tr>
						<td><b>post_max_size</b></td>
						<td>'.ini_get('post_max_size').'</td>
					</tr>
					<tr>
						<td><b>upload_max_filesize</b></td>
						<td>'.ini_get('upload_max_filesize').'</td>
					</tr>
					<tr>
						<td><b>max_execution_time</b></td>
						<td>'.ini_get('max_execution_time').'</td>
					</tr>
					<tr>
						<td><b>display_errors</b></td>
						<td>'.$d_error.'</td>
					</tr>
					<tr class="alert alert-info" style="color: black;">
						<td><b>Модули</b></td>
						<td><i class="icon-tasks"></i></td>
					</tr>
					<tr>
						<td><b>bcmath</b></td>
						<td>'.$m_bcmath.'</td>
					</tr>
					<tr>
						<td><b>gmp</b></td>
						<td>'.$m_gmp.'</td>
					</tr>	
					<tr>
						<td><b>gd</b></td>
						<td>'.$m_gd.'</td>
					</tr>
				</table>
			</div>
			
			'.$cont.'
			
			<div class="col-lg-4">	
				<table width="100%" class="table table-bordered">
					<thead class="alert-success" style="color: black;">
						<th>Панель Администратора</th>
						<th><center><i class="icon-lock"></i></center></th>
					</thead>
					<tr>
						<td><b>Вы вошли под учетной записью:</b></td>
						<td><center>'.$user['name'].'</center></td>
					</tr>
					<tr>
						<td><b>Группа:</b> </td>
						<td><img src="'.User::group_img($user['group']).'">'.substr(User::group($user['group']), 0, 10).'</td>
					</tr>
					<tr>
						<td colspan="2"><a href="index.php?do=logout" class="btn btn-block btn-inverse">Выход</a></td>
					</tr>
				</table>
			</div>
		</div>
	</div>';

	Template::tag('{title_name}', 'Панель управления');
	Template::tag('{page_content}', $content);
	return true;
}

	Engine::add_page(array('name'=>'page_admin_main', 'url'=>'main', 'type'=>'admin'));

?>