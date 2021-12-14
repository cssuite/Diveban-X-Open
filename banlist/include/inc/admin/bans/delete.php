<?php
$banid= (int)($_GET['delete'] ?? 0);
DataBase::getInstance()->updateRow(Configuration::$banlist['table'], [
    'unbantime' => '-1',
    'adminst' => 'Web-сайт'
], [ 'banid' => $banid ]);
header('Location: index.php');