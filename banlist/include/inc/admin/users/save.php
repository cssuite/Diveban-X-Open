<?php
$name = $_POST['name'];
$password = $_POST['password'];
$group = $_POST['group'];

$error = [];
if( !$name ) $error[] =  'имя';
if( !$group) $error[] =  'группу';
if( !$password) $error[] =  'пароль';

if ( !$error ) {
    // If already exists
    if (isset($_POST['id'])) {
        DataBase::getInstance()->updateRow(Configuration::$banlist['users_table'], [ 'name' => $name, 'password' => md5($password), 'group' => (int)$group ], [ 'id' => (int)$_POST['id']]);
    } else DataBase::getInstance()->insertRow(Configuration::$banlist['users_table'], [ 'id' => ' ', 'name' => $name, 'password' => md5($password), 'group' => (int)$group] );
}

if ($error) {
    die("Вы не ввели ".implode(', ',$error)." пользователя");
}

die("Сохранено");