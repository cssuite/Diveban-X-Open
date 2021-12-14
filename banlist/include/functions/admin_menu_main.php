<?php
if (!defined("GUARD")) { header('Content-type: text/html; charset=utf-8'); die('Доступ запрещен!'); } // Защита файла от прямого вызова.

function admin_menu_main(int $activeTab = 1,int $activeLink = 1) {
    return '
    <div class="tabbable">
					<ul class="nav nav-tabs">
						<li class="'. admin_is_tab_active(1, $activeTab).'"><a href="#tab1" data-toggle="tab"><b>Админцентр</b></a></li>
						<li class="'. admin_is_tab_active(2, $activeTab).'"><a href="#tab2" data-toggle="tab"><b>Сервер</b></a></li>
					</ul>
					<div class="tab-content">
						<div class="tab-pane '. admin_is_tab_active(1, $activeTab).'" id="tab1">
							<a href="{url}admin.php" class="btn btn-inverse '. admin_is_link_disable(1, 1, $activeTab, $activeLink) .'">Информация о системе</a>
							<a href="{url}admin.php?do=settings" class="btn btn-inverse '. admin_is_link_disable(2, 1, $activeTab, $activeLink) .'">Глобальные настройки</a>
							<a href="{url}admin.php?do=users" class="btn btn-inverse '. admin_is_link_disable(3, 1, $activeTab, $activeLink) .'">Управление пользователями</a>
							<br></br>
						</div>
						<div class="tab-pane '. admin_is_tab_active(2, $activeTab).'" id="tab2">
							<a href="{url}admin.php?do=bans" class="btn btn-inverse '. admin_is_link_disable(1, 2, $activeTab, $activeLink) .'">Управление банами</a>
							<a href="{url}admin.php?do=servers" class="btn btn-inverse '. admin_is_link_disable(2, 2, $activeTab, $activeLink) .'">Управление серверами</a>
							<a href="{url}admin.php?do=admins" class="btn btn-inverse '. admin_is_link_disable(3, 2, $activeTab, $activeLink) .'">Список администрации</a>
							<br></br>
						</div>
					</div>
				</div>
    ';
}

function admin_is_tab_active($tabId, $activeTab) {
    return $tabId == $activeTab ? 'active' : '';
}

function admin_is_link_disable($urlID, $tabId, $activeTab, $activeLink) {
    return $tabId == $activeTab && $urlID == $activeLink ? 'disabled' : '';
}