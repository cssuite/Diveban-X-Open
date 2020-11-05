<?php
/*
* default_search.php
*
* @version Superban CMS 1.6
* @author Oleksandr Kornienko
* @copyright (C)2013 Superban.net. Все права защищены.
* @contacts Skype: magoga25 ICQ: 624338780
*/

if (!defined("GUARD")) { header('Content-type: text/html; charset=utf-8'); die('Доступ запрещен!'); } // Защита файлов от прямого вызова

function page_default_search() 
{
	require_once("include/geoip.inc");
	Template::subtemplate_load('{index_content}', 'style/main.tpl');
	DataBase::connect();	
	$user = User::login();
	$gi = geoip_open("include/GeoIP.dat",GEOIP_STANDARD);
    $table = Configuration::$banlist['table'];
	
	$search = isset($_GET["search"]) ? mysql_real_escape_string(trim($_GET["search"])) : false;
	if(!empty($search)) {
		$pagination = pagination(array('query'=>'SELECT * FROM `'.$table.'` WHERE `ip` LIKE \'%'.$search.'\' OR `ipcookie` LIKE \'%'.$search.'\' OR `steam` LIKE \'%'.$search.'\' OR `banname` LIKE \'%'.$search.'\' OR `name` LIKE \'%'.$search.'\' OR `banid` LIKE \'%'.$search.'\' OR `uid` LIKE \'%'.$search.'\' ORDER BY `banid` DESC', 'page_num'=>Configuration::$pagination['p_main'], 'url'=>'index.php?do=search&search='.$search));
		$bans = mysql_query($pagination['query']);
	}
	
	$content = '
	<div class="well well-small">
		<ul class="breadcrumb alert alert-info">
			<li><a href="{url}index.php">Главная</a> <span class="divider"></span></li>
			<li class="active">Поиск по банам</li>
		</ul>
		<div class="well" style="color: white;background-color:black">
			<h3>Поиск по банам</h3>
			<b>Введите в поле ниже ник или его часть, steamID или ip адрес, а также uniqueID, для поиска по банам.</b>
			<form method="GET">
				<div class="input-append">
					<input type="hidden" name="do" value="search"><input type="text" name="search" value="'.$search.'" class="span2" style="width:790px !important;" id="appendedInputButton" size="16"><button class="btn btn-primary" type="submit"><b>Искать</b></button>
				</div>
			</form>
		</div>
	</div>';
	
	if($bans) 
	{
		$i = $pagination['count'];
		$content .= '<div class="well well-small">';

        $content .= '
	<table class="table table-bordered table-hover"  style="">
        <thead class="alert-info" style="color:black">
			<th><i class="icon-flag "></i></th>
            <th><i class="icon-calendar "></i> Дата[Время]</th>
			<th><i class="icon-hand-right "></i> Ник игрока</th>
            <th><i class="icon-user "></i> Ник админа</th>
            <th><i class="icon-fire "></i> Причина</th>
            <th><i class="icon-hdd "></i> Сервер</th>
			<th><i class="icon-calendar "></i> Срок</th>
			<th><i class="icon-th-large"></i> Подробно</th>
        </thead>';

		require_once("include/inc/motd.php");
		$content .= '
		</table></div>
		'.$pagination['pages'].'';
	}
	
	Template::tag('{title_name}', 'Поиск по банам');
	Template::tag('{page_content}', $content);
	DataBase::close();
	geoip_close($gi);
	return true;
}

	Engine::add_page(array('name'=>'page_default_search', 'url'=>'search', 'type'=>'default'));

?>