<?php
$servers = DataBase::getInstance()->fetchAll(Configuration::$banlist['servers_table']);

$content .= '
    <ul class="breadcrumb alert alert-info">
			<li><a href="{url}admin.php">Главная</a> <span class="divider"></span></li>
			<li class="active">Управление серверами</li>
	</ul>
  <table width="100%" class="table table-bordered table-hover">
        <thead class="alert-info" style="color: black;">
        <th><i class="icon-list-alt"></i> Название</th>
        <th><i class="icon-random"></i> Адрес</th>
        <th><center><i class="icon-picture"></i> Карта</center></th>
        <th><center><i class="icon-user"></i> Игроки</center></th>
        <th><i class="icon-star-empty"></i> Мод</th>
        <th><center><i class="icon-wrench"></i> Функции</center></th>
        </thead>
		    <tbody>
';

foreach ($servers as $data) {
    $server = lgsl_query_live('halflife', $data['ip'], $data['port'], $data['port'], $data['port'], 's');
    if ($server['b']['status'] == 1) {
        if (file_exists('images/maps/' . $server['s']['map'] . '.jpg')) $image_map = '{url}images/maps/' . $server['s']['map'] . '.jpg';
        else $image_map = '{url}images/maps/none.jpg';

        $play_fon = round(($server['s']['players'] / $server['s']['playersmax']) * 100);
        if ($server['s']['players'] > 16) {
            $color_bar = 'success';
        } else {
            $color_bar = 'danger';
        }
        if (geoip_country_code_by_addr($gi, $data['ip']) == NULL) {
            $geo = 'CLEAR';
        } else {
            $geo = geoip_country_code_by_addr($gi, $data['ip']);
        }

        $content .= '
        <script>
            function del' . $data['id'] . '()
            {
                var answer = confirm ("Подтвердите ваш запрос на удаления сервера №' . $data['id'] . '.")
                if (answer){
                    window.location.href = "{url}admin.php?do=servers&delete=' . $data['id'] . '"
                } else {
                    alert ("Запрос успешно отменен!")
                }
            }
        </script>
        <tr>
            <td><span rel="tooltip" data-placement="top" data-original-title="Название сервера"><img alt="" src="images/country/' . $geo . '.png" /> <b>' . $server['s']['name'] . '</b></span></td>
            <td><span rel="tooltip" data-placement="top" data-original-title="Адрес сервера"><a href="steam://connect/' . $data['ip'] . ':' . $data['port'] . '"><i class="icon-share-alt"></i> <b>' . $data['ip'] . ':' . $data['port'] . '</b></a></span></td>
            <td>'. $server['s']['map'].'</td>
            <td>
                <div class="progress">
					    <div class="progress-bar progress-bar-danger progress-bar-striped active" role="progressbar" aria-valuenow="'.$play_fon.'"
					    aria-valuemin="0" aria-valuemax="100" style="width:'.$play_fon.'%">
					    <span style="color:black;font-weight: 700">' . $server['s']['players'] . '/' . $server['s']['playersmax'] . ' (' . $play_fon . '%)</span>
					    </div>
					</div>
            </td>
            <td><span rel="tooltip" data-placement="top" data-original-title="Мод сервера"><b>' . $data['mode'] . '</b></span></td>
            <td>
                <center>
                    <div class="btn-group">
                        <button class="btn btn-sm dropdown-toggle" data-toggle="dropdown"><i class="icon-wrench icon-white"></i> <b>Функции</b></button>
                        <ul class="dropdown-menu">
                            <li><a href="{url}admin.php?do=servers&edit=' . $data['id'] . '"><i class="icon-pencil"></i> Редакт.</a></li>
                            <li><a onclick="del' . $data['id'] . '()" style="cursor: pointer;"><i class="icon-trash"></i> Удалить</a></li>
                        </ul>
                    </div>
                </center>
            </td>
        </tr>';
    } else {
        $content .= '
        <script>
            function del' . $data['id'] . '()
            {
                var answer = confirm ("Подтвердите ваш запрос на удаления сервера №' . $data['id'] . '.")
                if (answer){
                    window.location.href = "{url}admin.php?do=servers&delete=' . $data['id'] . '"
                } else {
                    alert ("Запрос успешно отменен!")
                }
            }
        </script>
        <tr class="alert-danger">
            <td colspan="5"><img alt="" src="images/country/CLEAR.png" /> <b>' . $data['ip'] . ':' . $data['port'] . ' (Сервер выключен)</b></td>
            <td>
                <center>
                    <div class="btn-group">
                        <button class="btn btn-sm dropdown-toggle btn-info" data-toggle="dropdown"><i class="icon-wrench icon-white"></i> <b>Функции</b></button>
                        <ul style="left: 25px;" class="dropdown-menu">
                            <li><a href="{url}admin.php?do=servers&edit=' . $data['id'] . '"><i class="icon-pencil"></i> Редакт.</a></li>
                            <li><a onclick="del' . $data['id'] . '()" style="cursor: pointer;"><i class="icon-trash"></i> Удалить</a></li>
                        </ul>
                    </div>
                </center>
            </td>
        </tr>';
    }
}

$content .= '</tbody></table><br/>
<center><a href="{url}admin.php?do=servers&add">
    <button class="btn btn-sm btn-warning btn-inverse"><i class="icon-plus icon-white"></i> <b>Добавить сервер</b>
    </button>
</a></center>
</div>';