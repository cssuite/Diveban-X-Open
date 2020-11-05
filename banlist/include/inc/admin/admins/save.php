<?php
$name = $_POST['name'] ?? '';
$nick = $_POST['nick'] ?? '';
$icq = $_POST['icq'] ?? '';
$skype = $_POST['skype'] ?? '';
$vk = $_POST['vk'] ?? '';
$steam = $_POST['steam'] ?? '';
$steamID = $_POST['steamid'] ?? '';
$passwd = $_POST['passwd'] ?? '';
$flags = $_POST['flags'] ?? '';
$access = $_POST['access'] ?? '';
$timedo = $_POST['timedo'] ?? '';
$timelast = $_POST['timelast'] ?? '';
$hide = $_POST['hide'] ?? '';

$errors = [];
if ($name == '') $errors[] = 'имя';
if ($nick == '') $errors[] = 'ник';


if (!$errors) {
    // If already exists
    if (isset($_POST['id'])) {
        DataBase::getInstance()->updateRow(Configuration::$banlist['admins_table'],
            [
                'name' => $name,
                'nick' => $nick,
                'icq' => $icq,
                'skype' => $skype,
                'vk' => $vk,
                'steam' => $steam,
                'steamid' => $steamID,
                'passwd' => $passwd,
                'flags' => $flags,
                'access' => $access,
                'timedo' => $timedo,
                'timelast' => $timelast,
                'hide' => $hide
            ], ['id' => (int)$_POST['id']]);
    } else DataBase::getInstance()->insertRow(Configuration::$banlist['admins_table'],
        [
            'name' => $name,
            'nick' => $nick,
            'icq' => $icq,
            'skype' => $skype,
            'vk' => $vk,
            'steam' => $steam,
            'steamid' => $steamID,
            'passwd' => $passwd,
            'flags' => $flags,
            'access' => $access,
            'timedo' => $timedo,
            'timelast' => $timelast,
            'hide' => $hide
        ]);
}

if ($errors) {
    die("Вы не ввели " . implode(', ', $errors) . " пользователя");
}

die("Сохранено");
