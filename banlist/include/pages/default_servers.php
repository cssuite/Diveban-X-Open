<?php
/*
* default_servers.php
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

function page_default_servers()
{
    require_once("include/geoip.inc");

    $user = User::login();
    $table = Configuration::$banlist['table'];

    Template::subtemplate_load('{index_content}', 'style/main.tpl');

    $gi = geoip_open("include/GeoIP.dat", GEOIP_STANDARD);

    $bans_nums = DataBase::getInstance()->countRow($table);
    $active_bans = mysqli_fetch_array(DataBase::getInstance()->query("SELECT COUNT(*) FROM `" . $table . "` WHERE `unbantime` >  " . time() . " OR `unbantime` = 0"))[0];
    $users_nums = DataBase::getInstance()->countRow(Configuration::$banlist['users_table']);
    $lastban = DataBase::getInstance()->fetchOne($table, [], ['name' => 'banid', 'type' => 'DESC'], 1);

    $servers = DataBase::getInstance()->fetchAll(Configuration::$banlist['servers_table']);

    $content = '
	<div class="well well-small">
		<ul class="breadcrumb alert alert-info" style="margin: 0;">
			<li><a href="{url}index.php">Главная</a> <span class="divider"></span></li>
			<li class="active">Список серверов</li>
		</ul>
	</div>
	<div class="row">
	<div class="col-lg-8">
		<div class="well well-small">
		<table width="100%" class="table table-bordered table-hover">
		<thead class="alert alert-info" style="color: white;background-color:black">
			<th><i class="icon-list-alt icon-white"></i> Название</th>
			<th><i class="icon-picture icon-white"></i> Карта</th>
			<th><i class="icon-user icon-white"></i> Игроки</th>
			<th><center><i class="icon-barcode icon-white"></i></center></th>
		</thead>
		';
    $i = 0;

    if ($servers) {
        foreach ($servers as $data) {
            $server = lgsl_query_live('halflife', $data['ip'], $data['port'], $data['port'], $data['port'], 's');

            if ($server['b']['status'] != 1) {
                $content .= '
				<tr class="error"> 
					<td colspan="4"><img alt="" src="images/country/CLEAR.png" /> <b>' . $data['ip'] . ':' . $data['port'] . ' (Сервер выключен)</b></td> 
				</tr>';
                continue;
            }

            $i++;

            $image_map = '';
            if (file_exists('images/maps/' . $server['s']['map'] . '.jpg')) $image_map = '{url}images/maps/' . $server['s']['map'] . '.jpg';
            $play_fon = round(($server['s']['players'] / $server['s']['playersmax']) * 100);

            if ($server['s']['players'] >= $server['s']['playersmax'] / 2) {
                $color_bar = 'success';
            } else {
                $color_bar = 'danger';
            }

            if (geoip_country_code_by_addr($gi, $data['ip']) == NULL) {
                $geo = 'CLEAR';
                $geo_name = 'Неизвестно';
            } else {
                $geo = geoip_country_code_by_addr($gi, $data['ip']);
                $geo_name = geoip_country_name_by_addr($gi, $data['ip']);
            }

            if ($user['group']) {
                $panel = '
					<a href="{url}admin.php?do=servers&edit=' . $data['id'] . '" class="btn btn-sm btn-primary"><i class="icon-pencil"></i></a>
					<a onclick="del' . $data['id'] . '()" style="cursor: pointer;" class="btn btn-sm btn-danger"><i class="icon-trash"></i></a>';
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
            ';

            $content .= '
				<tr>
					<td><span rel="tooltip" data-placement="top" data-original-title="Название сервера"><img alt="" src="country/' . $geo . '.png" /> <b>' . $server['s']['name'] . '</b></span></td> 
					<td><strong>' . $server['s']['map'] . '</strong></td>
					<td>
					<div class="progress">
					    <div class="progress-bar progress-bar-danger progress-bar-striped active" role="progressbar" aria-valuenow="'.$play_fon.'"
					    aria-valuemin="0" aria-valuemax="100" style="width:'.$play_fon.'%">
					    <span style="color:black;font-weight: 700">' . $server['s']['players'] . '/' . $server['s']['playersmax'] . ' (' . $play_fon . '%)</span>
					    </div>
					</div>
					</td>
					<td><center><a data-toggle="modal" href="#serv' . $i . '" class="btn btn-sm btn-info" href="#"><i class="icon-fullscreen icon-white"></i></a></center></td>
				</tr>';

            $content .= '
            <div class="modal fade" tabindex="-1" role="dialog" id="serv' . $i . '">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header" style="background-color: #222222">
                             <h4 align="center" style="color:whitesmoke" id="myModalLabel"> <img alt="" src="country/' . $geo . '.png" /> 
                                ' . $server['s']['name'] . '
                                <span class="pull-right" style="margin-right: 12px">' . $panel . '</span>
                                </h4>
                        </div>
                        <div class="modal-body">
                            <div class="progress">
					            <div class="progress-bar progress-bar-danger progress-bar-striped active" role="progressbar" aria-valuenow="'.$play_fon.'"
					            aria-valuemin="0" aria-valuemax="100" style="width:'.$play_fon.'%">
					            <span style="color:black;font-weight: 700">' . $server['s']['players'] . '/' . $server['s']['playersmax'] . ' (' . $play_fon . '%)</span>
					            </div>
					        </div>
					        <div class="row">
							    <div class="col-lg-8">
                                    <div class="well well-large">
                                        <b><i class="icon-list-alt"></i> Название:</b> ' . $server['s']['name'] . '<br />
                                        <b><i class="icon-share-alt"></i> Адрес:</b> <a href="steam://connect/' . $data['ip'] . ':' . $data['port'] . '">' . $data['ip'] . ':' . $data['port'] . '</a><br />
                                        <b><i class="icon-picture"></i> Карта:</b> ' . $server['s']['map'] . '<br />
                                        <b><i class="icon-user"></i> Игроки:</b> ' . $server['s']['players'] . '/' . $server['s']['playersmax'] . '<br />
                                        <b><i class="icon-star-empty"></i> Мод:</b> ' . $data['mode'] . '<br />
                                        <b><i class="icon-map-marker"></i> Местонахождения :</b> ' . $geo_name . '
                                    </div>
                                </div>
							<div class="col-lg-4">
								<a href="#" class="thumbnail" style="text-decoration: none;">
									<img src="' . $image_map . '" alt="Карта сервера" data-src="holder.js/160x120" style="width: auto; height: auto;">
									<center><i class="icon-picture"></i> <b>' . $server['s']['map'] . '</b></center>
								</a>
								<a href="steam://connect/' . $data['ip'] . ':' . $data['port'] . '" class="btn btn-danger btn-sm"><b>Подключится</b></a>
							</div>
						</div>
						<iframe src="{url}include/inc/players.php?ip=' . $data['ip'] . ':' . $data['port'] . '" style="width: 100%;padding: 4px;  -webkit-border-radius: 6px;  -moz-border-radius: 6px;  border-radius: 6px;" height="100"></iframe>
                        </div>
                        <div class="modal-footer" >
                            <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                        </div>
                    </div>
                </div>
            </div>';
        }
    } else $content .= '<tr class="error"> <td colspan="4"><b>Список серверов пуст.</b></td> </tr>';
    $content .= '</table></div></div>
	<div class="col-lg-4">
		<div class="well well-small">
			<table width="100%" class="table table-bordered">
				<thead class="alert" style="color: white;background-color:black">
					<th><i class="icon-align-justify icon-white"></i> Статистика</th>
					<th></th>
				</thead>
				<tr>
					<td><b>Количество банов</b></td>
					<td><span class="badge progress-bar-danger">' . $bans_nums . '</span></td>
				</tr>
				<tr>
					<td><b>Количество активных банов</b></td>
					<td><span class="badge progress-bar-danger">' . $active_bans . '</span></td>
				</tr>
				<tr>
					<td><b>Количество серверов</b></td>
					<td><span class="badge progress-bar-danger">' . count($servers) . '</span></td>
				</tr>
				<tr>
					<td><b>Количество Админов/Vip(ов)</b></td>
					<td><span class="badge progress-bar-danger">' . $users_nums . '</span></td>
				</tr>
			</table>
		</div>';

    $data = $lastban;
    if ($data) {
        $reason = $data['reason'] ?: 'Не Указана';

        $content .= '
		<div class="well well-small">
			<table width="100%" class="table table-bordered">
				<thead class="alert" style="color: white;background-color:black">
					<th colspan="2"><i class="icon-share-alt"></i> Последний бан</th>
				</thead>
				<tr>
					<td><b>Ник</b></td>
					<td>' . $data['banname'] . '</td>
				</tr>
				<tr>
					<td><b>Причина</b></td>
					<td>' . $reason . '</td>
				</tr>
				<tr>
					<td><b>Дата бана</b></td>
					<td>' . date("d.m.Y [H:i:s]", $data['bantime']) . '</td>
				</tr>
			</table>
		</div>';
    }

    $content .= '</div></div>';

    Template::tag('{title_name}', 'Список серверов');
    Template::tag('{page_content}', $content);

    geoip_close($gi);
    return true;
}

Engine::add_page(array('name' => 'page_default_servers', 'url' => 'servers', 'type' => 'default'));

?>