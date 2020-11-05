<?php
/*
* default_main.php
*/

if (!defined("GUARD")) { header('Content-type: text/html; charset=utf-8'); die('Доступ запрещен!'); } // Защита файла от прямого вызова.

function page_default_banid()
{
    require_once("include/geoip.inc");

    $user = User::login();
    $table = Configuration::$banlist['table'];

    Template::subtemplate_load('{index_content}', 'style/main.tpl');

	$gi = geoip_open("include/GeoIP.dat",GEOIP_STANDARD);

	$content = '
	<div class="well well-small">
	<ul class="breadcrumb alert alert-info">
		<li><a href="{url}index.php">Главная</a><span class="divider"></span></li>
		<li class="active">Бан #'.$_GET['pid'].'</li>
	</ul>';

	$data = DataBase::getInstance()->fetchOne($table, ['banid' => intval($_GET['pid'])]);

    $nickname = $data['banname'];
    $steamID = $data['steam'];
    $ip =  $user['group'] ? $data['ip'] : '<em>Скрытый</em>';
    $admin = $data['admin'];
    $reason = $data['reason'];
    $date_ban = date("d.m.Y [H:i]", $data["bantime"]);
    $last_visit = date("d.m.Y [H:i]", $data["time"]);
    $server = $data['Server']. '['.$data['ServerIp'].']';
    $map = $data['map'];
    $kicks = $data['Bans_Kick'];

    $geo =  geoip_country_code_by_addr($gi, $data['ip']) == NULL ? 'Неизвестно' : geoip_country_name_by_addr($gi, $data['ip']);

    $unbantime = (int)$data['unbantime'];

    $date_unban = '';
    if ( $unbantime == 0) {
        $bantime = '<span rel="tooltip" data-placement="right" class="label label-important" data-original-title="Бан активный">Навсегда</span>';
        $unban = '<em>Никогда</em>';
    }

    if ( $unbantime == -1) {
        $bantime = '<span rel="tooltip" data-placement="right" title="Разбанен" class="label label-success">Разбанен</span>';
        $unban = "<em>Осталось: <font color='#adff2f'>Истек</font> </em>";
    }

    if ($unbantime > 0) {
        $date_unban = date("d.m.Y [H:i]", $unbantime);

        if (time() < $unbantime) {
            $bantime = '<span rel="tooltip" data-placement="right" class="label label-danger" data-original-title="Бан активный">' . GetNormalTime(($data['unbantime'] - $data['bantime']) / 60) . '</span>';
            $unban = "<em>Осталось: ".GetTimeLenght(($unbantime - time()) / 60)."</em>";
        } else {
            $bantime = '<span rel="tooltip" data-placement="right" title="Бан истек" class="label label-success">Бан истек</span>';
            $unban = "<em>Осталось: <font color='#adff2f'>Истек</font> </em>";
        }
    }

    if ( !$user['group'] ) $ip = '<span style="font-style:italic;font-weight:bold">Скрытый</span>';

	$content .= '
    
    <h2>Общая информация об игроке</h2><table class="table table-bordered table-hover" style="width: 75%">
    <tr><td width="30%"><strong>Ник игрока</strong></td><td>'.$nickname.'</td></tr>
    <tr><td width="30%"><strong>SteamID</strong></td><td>'.$steamID.'</td></tr>
    <tr><td width="30%"><strong>IP</strong></td><td>'.$ip.'</td></tr>
    <tr><td width="30%"><strong>Админ</strong></td><td>'.$admin.'</td></tr>
    <tr><td width="30%"><strong>Причина</strong></td><td>'.$reason.'</td></tr>
    <tr><td width="30%"><strong>Дата Бана</strong></td><td>'.$date_ban.'</td></tr>
    <tr><td width="30%"><strong>Дата Разбана</strong></td><td>'.$date_unban.'</td></tr>
    <tr><td width="30%"><strong>Срок</strong></td><td>'.$bantime.'</td></tr>
    <tr><td width="30%"><strong>Истекает</strong></td><td>'.$unban.'</td></tr>
    <tr><td width="30%"><strong>Последний визит</strong></td><td>'.$last_visit.'</td></tr>
    <tr><td width="30%"><strong>Сервер</strong></td><td>'.$server.'</td></tr>
    <tr><td width="30%"><strong>Карта</strong></td><td>'.$map.'</td></tr>
    <tr><td width="30%"><strong>Кики</strong></td><td>'.$kicks.'</td></tr>
    <tr><td width="30%"><strong>Местонахождение</strong></td><td>'.$geo.'</td></tr>
    ';

	$content .= '</table>';

	if ( $user['group'] ) {

	    $bantype = $data['bantype'];
	    $last_name = $data['name'];
	    $uid = $data['uid'];
	    $cdkey = $data['cdkey'];
	    $divID = $data['diveid'];
	    $ipcookie = $data['ipcookie'];
        $adminID = $data['adminip'];
        $adminst = $data['adminst'] ? $data['adminst'] : 'Не разбанен\Удален';


        $ban_type_pop = ' <strong>A:</strong> AuthID(SteamID)<br>
                          <strong>I:</strong> IP<br>
                          <strong>C:</strong> Cookie<br>
                          <strong>U:</strong> Unique ID<br>
                          <strong>S\F:</strong> Subnet\ Full subnet<br>
                          <strong>K:</strong> CD-KEY<br>
                          <strong>D:</strong> DiveID<br>
 ';
        $content .= '<hr>
	    <h2>Админ информация</h2>
        <table class="table table-bordered table-hover"  style="width: 75%">
        <tr data-placement="'.Configuration::$main['m_popover'].'" rel="popover" data-trigger="hover" data-html="true" data-content="'.$ban_type_pop.'"><td width="30%"><strong>Тип Бана <i class="icon-book" </strong></td><td>'.$bantype.'</td></tr>
        <tr><td width="30%"><strong>Последний Ник</strong></td><td>'.$last_name.'</td></tr>
        <tr><td width="30%"><strong>UniqueID</strong></td><td>'.$uid.'</td></tr>
        <tr><td width="30%"><strong>CD-Key</strong></td><td>'.$cdkey.'</td></tr>
        <tr><td width="30%"><strong>DivID</strong></td><td>'.$divID.'</td></tr>
        <tr><td width="30%"><strong>IP Cookie</strong></td><td>'.$ipcookie.'</td></tr>
        <tr><td width="30%"><strong>Разбанен Админом</strong></td><td>'.$adminst.' ['.$adminID.']</td></tr>
	';

        $content .= '</table>';
    }

    $content .= '</div>';

	Template::tag('{title_name}', 'Информация об игроке');
	Template::tag('{page_content}', $content);

	geoip_close($gi);
	return true;
}

Engine::add_page(array('name'=>'page_default_banid', 'url'=>'banid', 'type'=>'default'));

?>