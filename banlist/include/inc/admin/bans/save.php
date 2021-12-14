<?php
$banid = $_POST['banid'] ?? 0;
$banname = $_POST['banname'] ?? '';
$reason = $_POST['reason'] ?? '';
$ip = $_POST['ip'] ?? '';
$adminName = $_POST['admin'] ?? '';
$bantype = $_POST['bantype'] ?? '';
$steam = $_POST['steam'] ?? '';
$unbantime = $_POST['unbantime'];

$errors = [];
if ($banname == '') $errors[] = 'ник';
if ($reason == '') $errors[] = 'причина';
if ($ip == '') $errors[] = 'ip';
if ($adminName == '') $errors[] = 'ник админа';
if ($bantype == '') $errors[] = 'тип бана';
if ($unbantime == '') $errors[] = 'время';
if ($steam == '') $errors[] = 'steamID';


if ( !$errors && $banid > 0 ) {
    DataBase::getInstance()->updateRow(Configuration::$banlist['table'], [
        'banname' => $banname,
        'reason' => $reason,
        'ip' => $ip,
        'admin' => $adminName,
        'bantype' => $bantype,
        'unbantime' => strtotime($unbantime),
        'steam' => $steam
    ], [ 'banid' => $banid ]);
}

if ($errors) {
    die("Вы не ввели ".implode(', ',$errors)." пользователя");
}

die("Сохранено");
