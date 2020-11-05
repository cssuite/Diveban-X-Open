<?php
$ip = $_POST['ip'] ?? '';
$port = $_POST['port'] ?? 0;
$mode = $_POST['mode'] ?? '';

$error = [];
if (!$ip) $error[] = 'IP';
if (!$port) $error[] = 'порт';
if (!$mode) $error[] = 'мод';

if (!$error) {
    // If already exists
    if (isset($_POST['id'])) {
        DataBase::getInstance()->updateRow(Configuration::$banlist['servers_table'], ['ip' => $ip, 'mode' => $mode, 'port' => $port], ['id' => (int)$_POST['id']]);
    } else DataBase::getInstance()->insertRow(Configuration::$banlist['servers_table'], ['ip' => $ip, 'mode' => $mode, 'port' =>$port] );
}

if ($error) {
    die("Вы не ввели " . implode(', ', $error) . " сервера");
}

die("Сохранено");