<?php
/*
* clear.php
*
* @version Superban CMS 1.6
* @author Oleksandr Kornienko
* @copyright (C)2013 Superban.net. Все права защищены.
* @contacts Skype: magoga25 ICQ: 624338780
*/

if (!defined("GUARD")) { header('Content-type: text/html; charset=utf-8'); die('Доступ запрещен!'); } // Защита файла от прямого вызова.
	
	if (!empty($_POST['clear']))
	{
	    DataBase::getInstance()->query("DELETE FROM `".Configuration::$banlist['table']."` WHERE `unbantime` < ".time()." AND `unbantime` != 0 OR `unbantime` = -1");
		$showmsg = '<tr><td colspan="2"><div class="alert alert-success" style="margin: 0;">Баны успешно очищены!</div></td></tr>';
	} else if (!empty($_POST['opt'])) {
        DataBase::getInstance()->query("OPTIMIZE TABLE `".Configuration::$banlist['table']."`");
		$showmsg = '<tr><td colspan="2"><div class="alert alert-success" style="margin: 0;">База данных успешно оптимизирована!</div></td></tr>';
	}
		
	$cont = '
	<script>
		function myFunction()
		{
			var answer = confirm ("Подтвердите ваш запрос на очищения истекших банов?")
			if (answer){
				$.ajax({
					type: "POST",
					url: "{url}admin.php",
					data: "clear=1",
					success: alert ("Баны успешно очищены!"),
				});
			} else {
				alert ("Запрос успешно отменен!")
			}
		}
	</script>
	<div class="col-lg-4">
		<table width="100%" class="table table-bordered">
			<thead class="alert-danger" style="color: black;">
				<th>Операции с БД</th>
				<th><center><i class="icon-retweet"></i></center></th>
			</thead>
				<tr>
					<td><b>Очистить истекшие баны:</b></td>
					<td><center><input class="btn btn-danger btn-sm" type="button" value="ОК" onclick="myFunction()"></center></td>
				</tr>
			<form action="" method="POST">
				<tr>
					<td><b>Оптимизировать базу:</b></td>
					<td><center><input class="btn btn-danger btn-sm" type="submit" value="ОК" name="opt"></center></td>
				</tr>
			</form>
			'.$showmsg.'
		</table>
		<br />
	</div>';
?>