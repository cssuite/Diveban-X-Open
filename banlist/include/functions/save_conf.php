<?php
/*
* save_conf.php
*
* @version Superban CMS 1.6
* @author Oleksandr Kornienko
* @copyright (C)2013 Superban.net. Все права защищены.
* @contacts Skype: magoga25 ICQ: 624338780
*/

if (!defined("GUARD")) { header('Content-type: text/html; charset=utf-8'); die('Доступ запрещен!'); } // Защита файла от прямого вызова.

function save_conf($array) {
	$file = 'include/classes/Configuration.php';
	if(file_exists($file)) 
	{
		$file_content = fopen($file, "r");
		$data = '';
		while (!feof($file_content)) {
			$buffer = fgets($file_content, 4096);
			$data_edit = false;
			foreach($array as $key => $value) {
				if(stripos($buffer, $key)) {
					$data.= "		'".$key."' => '".stripslashes($value)."',"."\n";
					$data_edit = true;
				}
			}
			if(!$data_edit) $data.= $buffer;
		}
		fclose($file_content);
		$file_content = fopen($file, "w+");
		fwrite($file_content, $data);
		fclose($file_content);
	}
	else
	{
		echo 'Файл '.$file.' не найден';
	}
}
?>