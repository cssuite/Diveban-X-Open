<?php
/*
* admin_bans.php
*
* @version Superban CMS 1.6
* @author Oleksandr Kornienko
* @copyright (C)2013 Superban.net. Все права защищены.
* @contacts Skype: magoga25 ICQ: 624338780
*/

if (!defined("GUARD")) { header('Content-type: text/html; charset=utf-8'); die('Доступ запрещен!'); } // Защита файла от прямого вызова.

function page_admin_bans() 
{
    Template::subtemplate_load('{index_content}', 'style/main.tpl');
    $user = User::login();
    $adminTab = admin_menu_main(2, 1);

    if ($user['group'] == 1) {
        $content = '
        <div class="well well-small">
            ' . $adminTab . '';

        if ( isset($_GET['edit']) ) {
            require_once "include/inc/admin/bans/edit.php";
        } else if (isset($_POST['save'])) {
            require_once "include/inc/admin/bans/save.php";
        }
        else if(isset($_GET['delete'])) {
            require_once "include/inc/admin/bans/delete.php";
        }
        else {
            header('Location: index.php');
        }

        $content.= '</div>';
    } else {
        $content = '
		<div class="alert alert-error">
			<table width="90%">
				<tr>
					<td><img src="{url}style/img/oshibka.png" width="150" height="150"/></td>
					<td><center><h4 class="alert-heading">Ошибка!</h4> <p><b>У вас не достаточно прав для редактирования списка, это могут делать только пользователи относящиеся к группе: "Гл. Администратор".<b></p></center></td>
				</tr>
			</table>
		</div></div>';
    }
	
	Template::tag('{title_name}', 'Управления списком забаненных');
	Template::tag('{page_content}', $content);
	return true;
}

	Engine::add_page(array('name'=>'page_admin_bans', 'url'=>'bans', 'type'=>'admin'));

?>