<?php
/*
* default_main.php
*/

if (!defined("GUARD")) { header('Content-type: text/html; charset=utf-8'); die('Доступ запрещен!'); } // Защита файла от прямого вызова.

function page_default_main() 
{
    require_once("include/geoip.inc");

    $user = User::login();
    $table = Configuration::$banlist['table'];

	Template::subtemplate_load('{index_content}', 'style/main.tpl');

	$gi = geoip_open("include/GeoIP.dat",GEOIP_STANDARD);

	$sql_check_ip = DataBase::getInstance()->fetchOne($table, ['ip' => $_SERVER['REMOTE_ADDR'], 'ipcookie' => $_SERVER['REMOTE_ADDR']], [], 1, true);

	$banned = false;
	if ($sql_check_ip) {
	    // Если такой айпи есть в базе, то время не истек ли срок бана
	    $banned = Helper::isBanned($sql_check_ip['unbantime']);
    }

	if($banned){
		$check = '<div class="alert alert-error">
		<span style="color:black;font-weight:bold;">IP Адрес — '.$_SERVER['REMOTE_ADDR'].'. Вы забанены. <a href="{url}index.php?do=search&search='.$_SERVER['REMOTE_ADDR'].'"><i class="icon-eye-open"></i></a></span>
		</div>';
	} else {
		$check = '<div class="alert alert-success" style="border-color:">
		<span style="color:black;font-weight:bold;">IP Адрес — '.$_SERVER['REMOTE_ADDR'].'. Не волнуйтесь, все хорошо. Вы не в бане.</span>
		</div>';
	}

    $search = isset($_GET["search"]) ? DataBase::getInstance()->escape(trim($_GET["search"])) : false;
    $paginationHelper = Helper::getPagintaionLimit();

	if ($search) {
        $query = DataBase::getInstance()->query("Select * from `divebanx` where 
                               `ip` LIKE '%$search' or 
                               `ipcookie` LIKE '%$search' or 
                               `steam` LIKE '%$search' or 
                               `banname` LIKE '%$search' or
                               `name` LIKE '%$search' or 
                               `banid` LIKE  '%$search' ORDER BY `banid` DESC LIMIT $paginationHelper[0],$paginationHelper[1]");

        $query = DataBase::getInstance()->fetchAssoc($query);
        $queryPagination = $query;
    } else {
	    $query = DataBase::getInstance()->fetchAll($table, [], ['name' => 'banid', 'type' => 'DESC'], $paginationHelper);
        $queryPagination =DataBase::getInstance()->fetchAll($table, [], ['name' => 'banid', 'type' => 'DESC']);
    }

	$pagination = pagination(['query' => $queryPagination,'page_num'=>Configuration::$pagination['p_main'],'url'=>'index.php?do=main']);

	if(!empty($_GET['ok'])) $ok = '<div class="alert alert-success"><center><strong>Бан успешно удален из Бан-листа!</strong></center></div>';

	$content = $check.'
	<div id="loading" style = "display:none;">
		<div class="cssload-container">
			<div class="cssload-speeding-wheel"></div>
		</div>
	</div>
	<div class="well well-small">
	'.$ok.'
	<ul class="breadcrumb alert alert-info">
		<li class="active">Главная<span class="divider"></span></li>
	</ul>';

	require_once ('include/inc/modal.php');
    require_once ('include/inc/admin_modal.php');
	$content .= '
	<table class="table">
        <thead class="alert-info" style="color:black">
			<th><i class="icon-flag "></i></th>
            <th><i class="icon-calendar "></i> Дата[Время]</th>
			<th><i class="icon-hand-right "></i> Ник игрока</th>
            <th><i class="icon-user "></i> Ник админа</th>
            <th><i class="icon-fire "></i> Причина</th>
            <th><i class="icon-hdd "></i> Сервер</th>
			<th><i class="icon-calendar "></i> Срок</th>
        </thead>';
	require_once("include/inc/motd.php");
	$content .= '
	</table></div>
	'.$pagination['pages'].'';
	
	Template::tag('{title_name}', 'Главная страница');
	Template::tag('{page_content}', $content);
	geoip_close($gi);
	return true;
}

	Engine::add_page(array('name'=>'page_default_main', 'url'=>'main', 'type'=>'default'));

?>