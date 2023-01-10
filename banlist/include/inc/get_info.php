<?php

define('GUARD', true);

include('../classes/Configuration.php');
include('../classes/DataBase.php');
include('../classes/User.php');

if (!isset($_POST['pid'])) die ("ErrorParams");

$user = User::login();

$data = DataBase::getInstance()->fetchOne(Configuration::$banlist['table'], ['banid' => intval($_POST['pid'])]);
$editBan = '$(\'#adminContent\').css(\'display\', \'none\')';

if ( !$user['group'] ) $data['ip'] = '<span style="font-style:italic;font-weight:bold">Скрытый</span>';
else {
    $editBan = '
    $(\'#adminEdit\').attr({\'href\': \'admin.php?do=bans&edit='.$data['banid'].'\'});
    $(\'#adminDelete\').attr({\'href\': \'admin.php?do=bans&delete='.$data['banid'].'\'});
    ';
}

$unbantime = !intval($data["unbantime"]) ? 'Никогда'  : date("d.m.Y [H:i]", $data["unbantime"]);
echo '
$(\'#detail-nick\').html(\' '.$data['banname'].' \');
$(\'#detail-steam\').html(\''.$data['steam'].'\');
$(\'#detail-ip\').html(\' '.$data['ip'].' \');
$(\'#detail-add\').html(\''.date("d.m.Y [H:i]", $data["bantime"]).'\');
$(\'#detail-remove\').html(\''.$unbantime.'\');
$(\'#detail-admin\').html(\''.$data['admin'].'\');
$(\'#detail-reason\').html(\''.$data['reason'].'\');
$(\'#detail-last-name\').html(\''.$data['name'].'\');
$(\'#detail-last-visit\').html(\''.date("d.m.Y [H:i]", $data["time"]).'\');
$(\'#detail-kick\').html(\''.$data['Bans_Kick'].'\');
$(\'#loading\').hide();
$(\'#viewpid\').attr({\'href\': \'index.php?do=banid&pid='.$data['banid'].'\'});
'.$editBan.'
$(\'#Details\').modal();
';
