<?php
$id= (int)($_GET['delete'] ?? 0);
DataBase::getInstance()->delete(Configuration::$banlist['admins_table'], [ 'id' => $id ]);
header('Location: index.php');