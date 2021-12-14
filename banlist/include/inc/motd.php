<?php
/*
* motd.php
*
* @version Superban CMS 1.6
* @author Oleksandr Kornienko
* @copyright (C)2013 Superban.net. Все права защищены.
* @contacts Skype: magoga25 ICQ: 624338780
*/

if (!defined("GUARD")) { header('Content-type: text/html; charset=utf-8'); die('Доступ запрещен!'); } // Защита файла от прямого вызова.
$bans = $query;
$admins = DataBase::getInstance()->fetchAll('diveban_admins', ['hide' => 0]);

foreach ($bans as $ban) {
    $unbantime = (int)$ban['unbantime'];

    if ( $unbantime == 0) {
        $unban = '<span rel="tooltip" data-placement="right" class="label label-danger" data-original-title="Бан активный">Навсегда</span>';
        $fons = ($ban['ip'] == $_SERVER['REMOTE_ADDR'] OR $ban['ipcookie'] == $_SERVER['REMOTE_ADDR']) ? 'class="warning"' : 'class="danger"';
    }

    if ( $unbantime == -1) {
        $unban = '<span rel="tooltip" data-placement="right" title="Разбанен" class="label label-success">Разбанен</span>';
        $fons = 'class="success"';
    }

    if ($unbantime > 0) {
        if (time() < $unbantime) {
            $unban = '<span rel="tooltip" data-placement="right" class="label label-danger" data-original-title="Бан активный">' . GetNormalTime(($ban['unbantime'] - $ban['bantime']) / 60) . '</span>';
            $fons = ($ban['ip'] == $_SERVER['REMOTE_ADDR'] OR $ban['ipcookie'] == $_SERVER['REMOTE_ADDR']) ? 'class="warning"' : 'class="danger"';
        } else {
            $unban = '<span rel="tooltip" data-placement="right" title="Бан истек" class="label label-success">Бан истек</span>';
            $fons = '';
        }
    }

    $reason = $ban['reason'] ?: 'Не Указана';
    $admin = Helper::searchFromAdminArray($admins, $ban['admin'], $ban['adminip']);

    $admin_name = '';
    if ($admin) {
        $admin_name = '(<a href="#" onclick="on_click_admin( '.$admin['id'].' )" style="color:#0088cc">'.$admin['nick'].'</a>)';
    }

    if( geoip_country_code_by_addr($gi, $ban['ip']) == NULL){
        $geo = 'CLEAR';
    } else {
        $geo = geoip_country_code_by_addr($gi, $ban['ip']);
    }

    $content .= '
	<tr '.$fons.' data-html="true" data-placement="'.Configuration::$main['m_popover'].'"  style="cursor:pointer;" onclick="on_click( '.$ban['banid'].' )" rel="popover" data-trigger="hover" data-content="<b>Админ:</b> '.$ban['admin'].'<br><b>Попыток входа:</b> '.$ban['Bans_Kick'].'" data-original-title="Добавлен: '.date('d.m.Y [H:i]', $ban['bantime']).'">
		<td><center><img alt="" src="country/'.$geo.'.png" /></center></td>
	   <td><b>'.date('d.m.Y [H:i]', $ban['bantime']).'</b></td>
		<td><b>'.$ban['banname'].'</b></td>
		<td><b>'.$ban['admin'].$admin_name.'</b></td>
		<td><b>'.$reason.'</b></td>
        <td><b>'.$ban['Server'].' ['.$ban['ServerIp'].']</b></td>
		<td>'.$unban.'</td>
    </tr>
	';
}
?>