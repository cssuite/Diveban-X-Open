<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 09.07.18
 * Time: 13:41
 */

if (!defined("GUARD")) { header('Content-type: text/html; charset=utf-8'); die('Доступ запрещен!'); } // Защита файла от прямого вызова.

$user = User::login();

DataBase::connect();
$data = mysql_fetch_assoc( mysql_query("SELECT * FROM `".Configuration::$banlist['table']."` WHERE `banid` = '".intval($_GET)."'"));
$nickname = $data['banname'];
$steamID = $data['steam'];
$ip = $data['ip'];
$admin = $data['admin'];
$reason = $data['reason'];
$date_ban = date("d.m.Y [H:i]", $data["bantime"]);
$date_unban ='';
$bantime = '';
$last_visit = date("d.m.Y [H:i]", $data["time"]);
$server = $data['Server']. '['.$data['ServerIp'].']';
$map = $data['map'];
$kicks = $data['Bans_Kick'];
$geo = '';

if( geoip_country_code_by_addr($gi, $data['ip']) == NULL){
    $geo = 'CLEAR';
} else {
    $geo = geoip_country_name_by_addr($gi, $data['ip']);
}

if( intval($data['unbantime']) > 0 ) {
    $date_unban = date("d.m.Y [H:i]", $data["unbantime"]);
    $bantime = '<span rel="tooltip" data-placement="right" class="label label-important" data-original-title="Бан активный">' . GetNormalTime( ($data['unbantime'] - $data['bantime']) / 60 ) . '</span>';
    $unban = "<em>Осталось: ".GetTimeLenght(GetNormalTime( ($data['unbantime'] - $data['bantime']) / 60 ))."</em>";
} elseif ( intval($data['unbantime']) == 0) {
    $date_unban = 'Невозможно получить дату';
    $bantime = '<span rel="tooltip" data-placement="right" class="label label-important" data-original-title="Бан активный">Навсегда</span>';
    $unban = '<em>Никогда</em>';
}
else {
    $date_unban = 'Невозможно получить дату';
    $bantime = '<span rel="tooltip" data-placement="right" title="Разбанен" class="label label-success">Разбанен</span>';
    $unban = '<em>Истек</em>';
}

if ( !$user['group'] ) $ip = '<span style="font-style:italic;font-weight:bold">Скрытый</span>';