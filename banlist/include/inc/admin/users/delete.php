<?php
if(abs((int)$_GET['delete']) == 1){
    header('Location: admin.php?do=users');
} else {
    DataBase::getInstance()->query("DELETE FROM `".Configuration::$banlist['users_table']."` WHERE id='".abs((int)$_GET['delete'])."'");
    header('Location: admin.php?do=users');
}