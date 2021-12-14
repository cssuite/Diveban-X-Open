<?php
$search = isset($_GET["bans"]) ? mysql_real_escape_string(trim($_GET["bans"])) : false;

if ($search) {
    $table = Configuration::$banlist['table'];
    $paginationHelper = Helper::getPagintaionLimit();
    $query = DataBase::getInstance()->query("Select * from `divebanx` where 
                               `ip` LIKE '%$search' or 
                               `ipcookie` LIKE '%$search' or 
                               `steam` LIKE '%$search' or 
                               `banname` LIKE '%$search' or
                               `name` LIKE '%$search' or 
                               `banid` LIKE  '%$search' ORDER BY `banid` DESC LIMIT $paginationHelper[0],$paginationHelper[1]");

    $pagination = pagination(['query' => $query,'page_num'=>Configuration::$pagination['p_main'],'admin.php?do=bans&bans='.$search]);
}

$content .= '
<ul class="breadcrumb alert alert-info">
    <li><a href="{url}admin.php">Главная</a> <span class="divider"></span></li>
    <li class="active">Управление списком забаненных</li>
</ul>

<div class="well alert-info" style="color: black;">
    <h3>Админ поиск</h3>
    <b>Введите в поле ID\SteamID\IP\Ник для поиска по банам.</b>
    <form method="GET">
        <div class="form-group">
            <input type="hidden" name="do" value="bans">
            <input type="text" name="bans" value="' .$search. '" class="form-control">
        </div>
        <button class="btn btn-info" type="submit"><b>Искать</b></button>
    </form>
</div>
';

if ( mysqli_affected_rows(DataBase::getInstance()->mysqli) ) {
    $content .= '
			<div class="well well-small">
			<table class="table table-bordered table-hover">
			<thead class="alert-info" style="color: black;">
				<th><i class="icon-hand-right"></i> Ник игрока</th>
				<th><i class="icon-user"></i> Ник админа</th>
				<th><i class="icon-fire"></i> Причина</th>
				<th><center><i class="icon-calendar"></i> Срок</center></th> 
				<th><center><i class="icon-wrench"></i> Функции</center></th>
			</thead>
			<tbody>';

    while($ban = mysqli_fetch_assoc($query)) {
        $unbantime = (int)$ban['unbantime'];

        if ( $unbantime == 0) {
            $unban = '<span rel="tooltip" data-placement="right" class="label label-important" data-original-title="Бан активный">Навсегда</span>';
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

        $content .= '
				<script>
					function del'.$data['banid'].'()
					{
						var answer = confirm ("Подтвердите ваш запрос на удаления бана №'.$data['banid'].'.")
						if (answer){
							window.location.href = "{url}admin.php?do=bans&delete='.$data['banid'].'"
						} else {
							alert ("Запрос успешно отменен!")
						}
					}
				</script>
				<tr '.$fons.' data-placement="'.Configuration::$main['m_popover'].'" rel="popover" data-trigger="hover" data-content="<b>Забанен админом:</b> '.$data['admin'].' <br /> <b>Причина:</b> '.$reason.'" data-original-title="Добавлен: '.date('d.m.Y [H:i]', $data['time']).'">
					<td><b>'.$data['banname'].'</b></td>
					<td><b>'.$data['admin'].'</b></td>
					<td><b>'.$reason.'</b></td>
					<td><center>'.$unban.'</center></td>
					<td>
					<center>
					<div class="btn-group">
					<button class="btn btn-mini dropdown-toggle btn-inverse" data-toggle="dropdown"><i class="icon-wrench icon-white"></i> <b>Функции</b></button>
					<ul style="left: 25px;" class="dropdown-menu">
						<li><a href="{url}admin.php?do=bans&edit='.$data['banid'].'"><i class="icon-pencil"></i> Редакт.</a></li>
						<li><a onclick="del'.$data['banid'].'()" style="cursor: pointer;"><i class="icon-trash"></i> Удалить</a></li>
					</ul>
					</div>
					</center></td>
				</tr>';
    }

    $content .= '</tbody></table></div>
			'.$pagination['pages'].'';
}