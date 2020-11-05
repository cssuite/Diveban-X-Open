<?php
/*
* time.php
*
* @version Superban CMS 1.6
* @author Oleksandr Kornienko
* @copyright (C)2013 Superban.net. Все права защищены.
* @contacts Skype: magoga25 ICQ: 624338780
*/

if (!defined("GUARD")) { header('Content-type: text/html; charset=utf-8'); die('Доступ запрещен!'); } // Защита файла от прямого вызова.

function numberEnd($number, $titles)
{
	$cases = array (2, 0, 1, 1, 1, 2);
	return $titles[ ($number%100>4 && $number%100<20)? 2 : $cases[min($number%10, 5)] ];
}

function GetTimeLenght($bantime)
{
	 $year = 0;
	 $month = 0;
	 $week = 0;
	 $days = 0;
	 $hours = 0;
	 $minutes = round($bantime);
	 
	 while($minutes >= 60)
	 {
		$minutes -= 60;
		$hours ++; 
	 }
	 while( $hours >= 24 )
	 {
		$hours -= 24;
		$days++;
	 }
	 while( $days >= 7 )
	 {
		$days -= 7;
		$week++;
	 }
	 while( $week >= 4 )
	 {
		$week -= 4;
		$month++;
	 }
	 while( $month >= 12 )
	 {
		$month -= 12;
		$year++;
	 }
	 $add_before = false;
	 $bantime = '<font color = "green">';
	 if($year)
	 {
		 if($year< 5)
		 $year == 1 ? $bantime .= ''.$year.' год ' : $bantime .= ''.$year.' года ';
		 else
		 $bantime .= ''.$year.' лет';
		 
		 $add_before = true;
	 }
	 if($month)
	 {
		 $bantime .= ''.$month.' меся'.numberEnd($month, array('ц', 'ца', 'ев')).' ';
		 $add_before = true;
	 }
	 if($week)
	 {
		 $bantime .= ''.$week.' недел'.numberEnd($week, array('я', 'и', 'ь')).' ';
		 $add_before = true;
	 }
	 if($days)
	 {
		 $bantime .= ''.$days.' д'.numberEnd($days, array('ень', 'ня', 'ней')).' ';
		 $add_before = true;
	 }
	 if($hours)
	 {
		 $bantime .= ''.$hours.' ча'.numberEnd($hours, array('с', 'са', 'сов')).' ';
		 $add_before = true;
	 }
	 if($minutes)
	 {
		 $bantime .= ''.round($minutes).' мину'.numberEnd($minutes, array('та', 'ты', 'т')).' ';
		 $add_before = true;
	 }
	 
	 if(!$add_before || $minutes < 0)
	 	$bantime = '<font color = "green">Уже истек</font>';
		else  $bantime .= '</font>';
		
	return $bantime;
}

function GetNormalTime($bantime)
{
	if($bantime < 60)
	{
		$bantime = round($bantime).' мин.';
	}
	elseif($bantime == 60)
	{
		$bantime = round($bantime / 60).' час';
	}
	elseif($bantime > 60 && $bantime <= 240)
	{
		$bantime = round($bantime / 60).' часа';
	}
	elseif($bantime > 240 && $bantime <= 1200)
	{
		$bantime = round($bantime / 60).' часов';
	}
	elseif($bantime == 1440 || $bantime == 30240)
	{
		$bantime = round($bantime/60/24).' день';
	}
	elseif(($bantime > 1440 && $bantime <= 5760) || ($bantime >= 31680 && $bantime <= 34560))
	{
		$bantime = round($bantime/60/24).' дня';
	}
	elseif(($bantime >= 7200 && $bantime <= 28800) || ($bantime >= 36000 && $bantime < 43200))
	{
		$bantime = round($bantime/60/24).' дней';
	}
	elseif($bantime == 43200)
	{
		$bantime = round($bantime/60/24/30).' месяц';
	}
	elseif($bantime >= 43200 && $bantime <=175317)
	{
		$bantime = round($bantime/60/24/30).' месяца';
	}
	elseif($bantime >= 219144 && $bantime <=876582)
	{
		$bantime = round($bantime/60/24/30).' месяцев';
	}
	else
	{
		$bantime = round($bantime/60/24/30).' месяцев';
	}
	
	return $bantime;
}
?>