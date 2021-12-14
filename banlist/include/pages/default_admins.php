<?php
/*
* default_admins.php
*
* @version Superban CMS 1.6
* @author Oleksandr Kornienko
* @copyright (C)2013 Superban.net. Все права защищены.
* @contacts Skype: magoga25 ICQ: 624338780
*/

if (!defined("GUARD")) { header('Content-type: text/html; charset=utf-8'); die('Доступ запрещен!'); } // Защита файла от прямого вызова.

function page_default_admins() 
{
    require_once("include/geoip.inc");

    $user = User::login();
    Template::subtemplate_load('{index_content}', 'style/main.tpl');

	$admins = DataBase::getInstance()->fetchAll(Configuration::$banlist['admins_table'], [], ['name' => 'timelast']);

	if ($admins) {
        $content = '
		<div class="well well-small">
			<ul class="breadcrumb alert alert-info">
				<li><a href="{url}index.php">Главная</a> <span class="divider"></span></li>
				<li class="active">Список Администрации</a></li>
			</ul>
			<table width="100%" class="table table-bordered table-hover" style="background-color:black">
				<thead class="alert-info" style="color:white;background-color:black">
					<th> Имя</th> 
					<th> Ник</th>
                    			<th> Skype</th>
                    			<th> Должность</th>
					<th> Флаги</center></th> 
					<th> Дата покупки</center></th> 
					<th> Срок</center></th> 
				</thead>
				<tbody>';

        $i = 0;
        foreach ($admins as $data) {
            if( $data['hide'] > 0)
                continue;

            $i++;

            $fons = 'style="background-color: #f5f5f5;cursor:pointer;"';
            $label = 'alert-success';
            $havedate = 'Осталось(ся) '.ceil((strtotime($data['timelast']) - time()) / 86400).' дн.';
            $data_end = date('d.m.Y [H:i]', strtotime($data['timelast']));

            if(($data['timelast'] < date("Y-m-d")) OR ($data['timelast'] == date("Y-m-d"))){
                $havedate = "Время истекло !";
                $fons = 'class="error"';
                $label = 'alert-danger';
            }
            if($data['timelast'] == 0){
                $havedate = "Неограниченный срок !";
                $fons = 'style="background-color: #f5f5f5;cursor:pointer;"';
                $label = 'alert-success';
                $data_end = "Неограниченный срок !";
            }

            if( isset($_GET['aid']) && $_GET['aid'] == $data['id'] )
                $fons = 'class="info" ';

            if($user['group']){
                $panel = '
				<a href="{url}admin.php?do=admins&edit='.$data['id'].'" class="btn btn-sm btn-primary"><i class="icon-pencil"></i></a>
				<a onclick="del'.$data['id'].'()" style="cursor: pointer;" class="btn btn-sm btn-danger"><i class="icon-trash"></i></a>';
            }

            if($data['icq'] != NULL) $icq = '<b>'.$data['icq'].'</b>'; else $icq = '<i>Не указано</i>';
            if($data['skype'] != NULL) $skype2 = '<b>'.$data['skype'].'</b>'; else $skype2 = '<i>Не указано</i>';

            if($data['icq'] != NULL) $icq2 = '<b>Должность:</b> '.$data['icq'].' <br />'; else $icq2 = '';
            if($data['skype'] != NULL) $skype = '<b>Skype:</b> '.$data['skype'].' <br />'; else $skype = '';
            if($data['vk'] != NULL) $vk = '<b>VK:</b> <a href="'.$data['vk'].'">Нажмите для перехода</a> <br />'; else $vk = '';
            if($data['steam'] != NULL) $steam = '<b>Steam:</b> <a href="'.$data['steam'].'">Нажмите для перехода</a> <br />'; else $steam = '';
            if($data['flags'] != NULL) $flags = '<b>Flags(Флаги доступа):</b> '.$data['flags'].' <br />'; else $flags = '';

            $srv = (isset($data['access']) && strlen($data['access'])>5) ? $data['access'] : "Не указано";
            $aceess = $data['flags'];

            $content .= '
			<script>
				function del'.$data['id'].'()
				{
					var answer = confirm ("Подтвердите ваш запрос на удаления администратора №'.$data['id'].'.")
					if (answer){
						window.location.href = "{url}admin.php?do=admins&delete='.$data['id'].'"
					} else {
						alert ("Запрос успешно отменен!")
					}
				}
			</script>
			<script>
			function go_mod'.$i.'() {
				$("#adm'.$i.'").modal();
			}
			</script>';

            $content .= '
            <tr '.$fons.' style="cursor:pointer;" onclick="go_mod'.$i.'()">
				<td><spanrel="tooltip" data-placement="top" data-original-title="Имя"><b>'.$data['name'].'</b></span></td>
				<td><spanrel="tooltip" data-placement="top" data-original-title="Ник"><b>'.$data['nick'].'</b></span></td>
               			<td><spanrel="tooltip" data-placement="top" data-original-title="Skype">'.$skype2.'</span></td>
                		<td><spanrel="tooltip" data-placement="top" data-original-title="icq">'.$icq.'</span></td>
				<td><center><spanrel="tooltip" data-placement="top" data-original-title="Сервера"><b>'.$aceess.'</span></center></td>
				<td><center><span class="badge" rel="tooltip" data-placement="top" data-original-title="Дата покупки"><font style="color: white;">'.date('d.m.Y [H:i]', strtotime($data['timedo'])).'</font></span></center></td>
				<td><center><span class="badge '.$label.'" rel="tooltip" data-placement="top" data-original-title="Осталось дней">'.$havedate.'</span></center></td>
			</tr>
            ';

            $content .= '
                <div class="modal fade" tabindex="-1" role="dialog" id="adm' . $i . '">
                    <div class="modal-dialog" role="document">
                         <div class="modal-content">
                            <div class="modal-header" style="background-color: #222222">
                                 <h4 align="center" style="color:whitesmoke" id="myModalLabel">
                                '.$data['nick'].' ('.$data['name'].')
                                <span class="pull-right" style="margin-right: 12px">' . $panel . '</span>
                                </h4>
                            </div>
                            <div class="modal-body">
                                <div class="alert alert-danger"><strong>Срок: '.$havedate.'</strong></div>
                                <div class="well well-large">
                                <b>Имя:</b> '.$data['name'].' <br />
                                <b>Ник:</b> '.$data['nick'].' <br />
                                '.$icq2.'
                                '.$skype.'
                                '.$vk.'
                                '.$steam.'
                                '.$flags.'
                                <b>Дата покупки:</b> '.date('d.m.Y [H:i]', strtotime($data['timedo'])).' <br />
                                <b>Дата окончания:</b> '.$data_end.'<br>
                                <b>Сервер(а):</b><br> '.$srv.' <br />
                            </div>
                            <div class="modal-footer" >
                                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                            </div>
                        </div>
                    </div>
                </div>
            ';
        }

        $content .= '</tbody></table>';
    } else {
        $content = '<div class="well well-small">
		<ul class="breadcrumb alert alert-info">
			<li><a href="{url}index.php">Главная</a> <span class="divider"></span></li>
			<li class="active">Список Администрации</a></li>
		</ul>
		<div class="alert alert-info" style="margin-bottom: 0px;">Список администрации пуст.</div>';
    }

    $content .= '
			<div style="text-align: center">
            <button class="btn btn-info" onclick="$(&#39;#info&#39;).slideToggle(&#39;slow&#39;)">Информация доступа</button><br /></div>

			<div id="info" style="display: none; text-align: center">
            <table style="margin: 0 auto; text-align: left">
				<br />
				<br />
                    <tr>
                        <td><b><font size="+2">Права доступа</font></td></b>
                        <td><b><font size="+2">Флаги доступа</font></td></b>

                    </tr>
                    <tr>
                        <td><br />
                            a - Иммунитет (не может быть кикнут / забанен и т.д)<br>b - Резервирование слотов (может использовать зарезервированные слоты)<br>c - Команда amx_kick<br>d - Команда amx_ban и amx_unban<br>e - Команда amx_slay и amx_slap<br>f - Команда amx_map<br>g - Команда amx_cvar (не все CVARы доступны)<br>h - Команда amx_cfg<br>i - amx_chat и другие команды чата<br>j - amx_vote и другие команды голосований (Vote)<br>k - Доступ к изменению значения команды sv_password (через команду amx_cvar)<br>l - Доступ к amx_rcon и rcon_password (через команду amx_cvar)<br>m - Уровень доступа A (для иных плагинов)<br>n - Уровень доступа B<br>o - Уровень доступа C<br>p - Уровень доступа D<br>q - Уровень доступа E<br>r - Уровень доступа F<br>s - Уровень доступа G<br>t - Уровень доступа H<br>u - Основной доступ<br>z - Игрок (не администратор)
                        </td>
                        <td class="vtop"><br />
                            a - Кикать игрока при вводе некорректного пароля<br>b - Тег клана<br>c - Для SteamID<br>d - Для IP<br>e - Пароль не требуется (важен только SteamID либо IP )<br>k - Имя или тег (С УчёТом РеГистРа!).
                        </td>
                    </tr>
            </table>
        </div></div>';
	
	Template::tag('{title_name}', 'Список Администрации');
	Template::tag('{page_content}', $content);
	return true;
}

	Engine::add_page(array('name'=>'page_default_admins', 'url'=>'admins', 'type'=>'default'));

?>