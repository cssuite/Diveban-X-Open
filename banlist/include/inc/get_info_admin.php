<?

define('GUARD', true);

include('../classes/Configuration.php');
include('../classes/DataBase.php');
include('../classes/User.php');

if (!isset($_POST['pid'])) die ("ErrorParams");

$id = intval($_POST['pid']);
$user = User::login();

$data = DataBase::getInstance()->fetchOne(Configuration::$banlist['admins_table'], ['id' => $id]);
$editBan = '$(\'#adminContentAdmin\').css(\'display\', \'none\')';

$data['date_start'] = date('d.m.Y [H:i]', strtotime($data['timedo']));
$data['date_end'] =  date('d.m.Y [H:i]', strtotime($data['timelast']));

$srv = (isset($data['access']) && strlen($data['access'])>5) ? $data['access'] : "Не указано";

if($data['timelast'] == 0){
    $data['date_end'] = "Неограниченный срок !";
}

if ( !$user['group'] ) $data['steamid'] = '<span style="font-style:italic;font-weight:bold">Скрытый</span>';
else {
    $editBan = '
    $(\'#adminEditAdmin\').attr({\'href\': \'admin.php?do=admins&edit='.$id.'\'});
    $(\'#adminDeleteAdmin\').attr({\'href\': \'admin.php?do=admins&delete='.$id.'\'});
    ';
}
echo '
$(\'#admin-name\').html(\' '.$data['name'].' \');
$(\'#admin-nick\').html(\''.$data['nick'].'\');
$(\'#admin-steam\').html(\' '.$data['steamid'].' \');
$(\'#admin-admin\').html(\''.$data['icq'].'\');
$(\'#admin-flags\').html(\''.$data['flags'].'\');
$(\'#admin-date-start\').html(\''.$data['date_start'].'\');
$(\'#admin-date-end\').html(\''.$data['date_end'].'\');
$(\'#admin-servers\').html(\''.$srv.'\');
'.$editBan.'
$(\'#admin_modal\').modal();
';
