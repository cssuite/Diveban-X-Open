<?php
/*
* adminpanel.php
*
* @version Superban CMS 1.6
* @author Oleksandr Kornienko
* @copyright (C)2013 Superban.net. Все права защищены.
* @contacts Skype: magoga25 ICQ: 624338780
*/

if (!defined("GUARD")) {
    header('Content-type: text/html; charset=utf-8');
    die('Доступ запрещен!');
} // Защита файла от прямого вызова.

function admin_panel()
{
    $user = User::login();
    if ($user['error'] == 'yes') {
        $addon = '
		<ul class="nav navbar-nav navbar-right">
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        Войти <i class="icon-user"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                        <div class="navbar-login">
                        
                        <form method="POST">
                        <div class="form-group"><input type="text" name="name" class="form-control" placeholder="Логин"></div>
                        <div class="form-group"><input type="password" name="password" class="form-control" placeholder="Пароль"></div>
                        <button type="submit" name="login" class="btn btn-success"><b>Войти</b></button>
                         </form>
                         </div>
                        </li>
                    </ul>
                </li>
            </ul>
		';
    } else {
        $addon = '
	    <ul class="nav navbar-nav navbar-right">
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        Панель администратора <i class="icon-user"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <div class="navbar-login">
                                <div class="row">
                                    <div class="col-lg-4">
                                    </div>
                                    <div class="col-lg-8">
                                        <p class="text-left"><strong>' .$user['name']. '</strong></p>
                                        <p class="text-left small"><label class="badge badge-info">' .User::group($user['group']). '</label></p>
                                        <p class="text-left">
                                            <a href="{url}admin.php" class="btn btn-primary btn-block btn-sm">Панель администратора</a>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <div class="navbar-login navbar-login-session">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <p>
                                            <a href="index.php?do=logout" class="btn btn-danger btn-block">Выйти</a>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                </li>
            </ul>
	    ';
    }

    return Template::tag('{adminpanel}', $addon);
}

?>