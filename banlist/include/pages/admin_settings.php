<?php
/*
* admin_settings.php
*
* @version Superban CMS 1.6
* @author Oleksandr Kornienko
* @copyright (C)2013 Superban.net. Все права защищены.
* @contacts Skype: magoga25 ICQ: 624338780
*/

if (!defined("GUARD")) { header('Content-type: text/html; charset=utf-8'); die('Доступ запрещен!'); } // Защита файла от прямого вызова.

function page_admin_settings() 
{
	Template::subtemplate_load('{index_content}', 'style/main.tpl');
	$user = User::login();
    $adminTab = admin_menu_main(1, 2);
	
	if(($user['group'] == 1) or ($user['group'] == 2))
	{
		if(isset($_POST['save']))
		{
			$list = json_decode(stripslashes($_POST['conf']), true);
			save_conf($list);
			die("Успех");
		} else {	
			$content = '
			<script>
				function send()
				{
					var array = {};
					var test;
					var b=document.getElementsByTagName("input");
					for (var i=0;i<document.getElementsByTagName("input").length;i++) {
						if (b[i].type=="text") {
							array[b[i].className]= b[i].value;
						}
					}
					array = JSON.stringify(array);
					$.ajax({
						type: "POST",
						url: "{url}admin.php?do=settings",
						data: "conf="+array+"&save=1",
						success: function(html) {
							alert(html)
						}
					});
				}
			</script>
			<div class="well well-small">
				'.$adminTab.'
				<ul class="breadcrumb alert alert-info">
					<li><a href="{url}admin.php">Главная</a> <span class="divider"></span></li>
					<li class="active">Глобальные настройки</li>
				</ul>
				<div class="well alert-info" style="color: black;">
					<table class="table" width="100%">
						<div class="alert alert-success">
							<h4 class="alert-heading">Настройка Мета - Тегов</h4>
						</div>
						<tr> <td><b style="margin-right: 255px;"><span style="color:red;">*</span> Название:</b> </td> <td><input type="text" name="m_title" class="m_title" style="width:300px" value="'.Configuration::$main['m_title'].'"></td> </tr>
						<tr> <td><b><span style="color:red;">*</span> Описания:</b> </td> <td><input type="text" name="m_desc" class="m_desc" style="width: 300px;" value="'.Configuration::$main['m_desc'].'"></td> </tr>
						<tr> <td><b><span style="color:red;">*</span> Ключевые слова:</b> </td> <td><input type="text" name="m_keys" class="m_keys" style="width: 300px;" value="'.Configuration::$main['m_keys'].'"></td> </tr>
					</table>
					<table class="table" width="100%">	
						<div class="alert alert-success">
						<h4 class="alert-heading">Глобальные настройки</h4>
						</div>
						<tr> <td><b><span style="color:red;">*</span> Url бан-листа ( http://site.ru/bans/ или http://site.ru/ ):<br /> <span style="color:red;">( Обязательно с флешем " / " )</span></b> </td> <td><input type="text" name="m_sitepatch" class="m_sitepatch" style="width:300px" value="'.Configuration::$main['m_sitepatch'].'"></td> </tr>
						<tr> <td><b><span style="color:red;">*</span> Ссылка на Логотип:</b> </td> <td><input type="text" name="m_logo" class="m_logo" style="width:300px" value="'.Configuration::$main['m_logo'].'"></td> </tr>
						<tr> <td><b><span style="color:red;">*</span> Ссылка на Фон:</b> </td> <td><input type="text" name="m_fon" class="m_fon" style="width:300px" value="'.Configuration::$main['m_fon'].'"></td> </tr>
						<tr> <td><b><span style="color:red;">*</span> Вывод подсказки:</b> <span data-placement="right" rel="popover" data-trigger="hover" data-content="<b>Направо:</b> right <br /> <b>Налево:</b> left <br /> <b>Вверх:</b> top <br /> <b>Вниз:</b> bottom" data-original-title="Вывод подсказки"><i class="icon-question-sign"></i></span></td> <td><input type="text" name="m_popover" class="m_popover" style="width:300px" value="'.Configuration::$main['m_popover'].'"></td> </tr>
						<tr> <td><b><span style="color:red;">*</span> Количество банов на одной странице:</b> </td> <td><input type="text" name="p_main" class="p_main" style="width:300px" value="'.Configuration::$pagination['p_main'].'"></td> </tr>
					</table>	
					<table class="table" width="100%">
						<div class="alert alert-success">
							<h4 class="alert-heading">Настройка Webmoney</h4>( <a target="_blank" href="https://merchant.webmoney.ru">Merchant WebMoney</a> )
						</div>
						<tr> <td><b style="margin-right: 230px;"><span style="color:silver;">*</span> Кошелек WMR:</b> </td> <td><input type="text" name="m_purse" class="m_purse" style="width:300px" value="'.Configuration::$main['m_purse'].'"></td> </tr>
						<tr> <td><b><span style="color:silver;">*</span> Цена за платный разбан:</b> </td> <td><input type="text" name="m_cost" class="m_cost" style="width:300px" value="'.Configuration::$main['m_cost'].'"></td> </tr>
						<tr> <td><b><span style="color:silver;">*</span> Секретный ключ Webmoney:</b> </td> <td><input type="text" name="m_secret_key" class="m_secret_key" style="width:300px" value="'.Configuration::$main['m_secret_key'].'"></td> </tr>
					</table>
					<table class="table" width="100%">
						<div class="alert alert-success">
							<h4 class="alert-heading">Другое</h4>
						</div>
						<tr> <td><b style="margin-right: 195px;"><span style="color:silver;">*</span> Форум/Сайт/Группа:</b> </td> <td><input type="text" name="m_website" class="m_website" style="width:300px" value="'.Configuration::$main['m_website'].'"></td> </tr>
						<tr> <td><b><span style="color:silver;">*</span> Тема на Unban:</b> </td> <td><input type="text" name="m_webtheme" class="m_webtheme" style="width:300px" value="'.Configuration::$main['m_webtheme'].'"></td> </tr>
					</table>	
					<br /><input type="button" value="Сохранить" class="btn btn-success btn-inverse" onclick="send()">
				</div>
			</div>';
		}
	} else {
		$content = '
		<div class="alert alert-error">
			<table width="90%">
				<tr>
					<td><img src="{url}style/img/oshibka.png" width="150" height="150"/></td>
					<td><center><h4 class="alert-heading">Ошибка!</h4> <p><b>У вас не достаточно прав для редактирования серверов, это могут делать только пользователи относящиеся к группам "Гл. Администратор" и "Администратор".<b></p></center></td>
				</tr>
			</table>
		</div>';
	}
	
	Template::tag('{title_name}', 'Глобальные настройки бан-листа');
	Template::tag('{page_content}', $content);
	return true;
}

	Engine::add_page(array('name'=>'page_admin_settings', 'url'=>'settings', 'type'=>'admin'));

?>