<?php
$serverID= (int)($_GET['delete'] ?? 0);
DataBase::getInstance()->query("DELETE FROM `".Configuration::$banlist['servers_table']."` WHERE `id` = $serverID");
header('Location: index.php');