<?php
/*
* User.php
*
* @version Superban CMS 1.6
* @author Oleksandr Kornienko
* @copyright (C)2013 Superban.net. Все права защищены.
* @contacts Skype: magoga25 ICQ: 624338780
*/

if (!defined("GUARD")) { header('Content-type: text/html; charset=utf-8'); die('Доступ запрещен!'); } // Защита файла от прямого вызова.

class User 
{
    public static $user = null;

	static function login()
    {
        if ( self::$user) return self::$user;

        $database = DataBase::getInstance();

        if (self::isAuthByPost()) {
            self::$user = $database->fetchOne('suite_users', ['name' => $_POST['name'], 'password' => md5($_POST['password'])]);

            if (self::$user) {
                setcookie("name", $_POST['name'], (time() + (60 * 60 * 24 * 7)), "/");
                setcookie("password", md5($_POST['password']), (time() + (60 * 60 * 24 * 7)), "/");
                return self::$user;
            }

            return array('error' => true, 'text' => 'Не правильный логин или пароль');
        }

        if (self::isAuthByCookie()) {
            self::$user = $database->fetchOne('suite_users', ['name' => $_COOKIE['name'], 'password' => $_COOKIE['password']]);

            if (self::$user) {
                return self::$user;
            }

            return array('error' => true, 'text' => 'Вы не авторизированы');
        }

        return array('error' => true, 'text' => 'Вы не авторизированы #2');
    }

    static function isAuthByPost() {
	    return isset($_POST['login']) and isset($_POST['name']) and isset($_POST['password']);
    }

    static function isAuthByCookie() {
        return isset($_COOKIE['name']) and isset($_COOKIE['password']);
    }

	static function logout() {
		setcookie("name", '', (time() - 60), "/");
		setcookie("password", '', (time() - 60), "/");
		unset($_COOKIE['name']);
		unset($_COOKIE['password']);
	}
	
	static function group($id) {
		$groups = array('1'=>'Гл. Администратор', '2'=>'Администратор', '3'=>'Модератор');
		if(isset($groups[$id])) 
			return $groups[$id];
		else 
			return 'Ошибка';
	}
	
	static function group_img($id) {
		$groups = array('1'=>'images/admin/gladmin.png', '2'=>'images/admin/admin.png', '3'=>'images/admin/moder.png');
		if(isset($groups[$id])) 
			return $groups[$id];
		else 
			return 'Ошибка';
	}
	
//	static function account($type, $s) {
//		DataBase::connect();
//		if($type == 'id') {
//			$s = abs((int)$s);
//			$query = mysql_query("SELECT * FROM superban_users WHERE id='".$s."'");
//			if(mysql_num_rows($query))
//				return mysql_fetch_array($query);
//			else
//				return array('error'=>true, 'text'=>'Пользователь не найден');
//		}
//		elseif($type == 'name')
//		{
//			$query = mysql_query("SELECT * FROM superban_users WHERE name='".mysql_escape_string($s)."'");
//			if(mysql_num_rows($query))
//				return mysql_fetch_array($query);
//			else
//				return array('error'=>true, 'text'=>'Пользователь не найден');
//		}
//	}
}
?>