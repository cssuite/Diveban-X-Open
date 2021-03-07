<?php

$admins = DataBase::getInstance()->fetchAll(Configuration::$banlist['admins_table'], [], ['name' => 'timelast']);

    $content .= '
		<div class="well well-small">
			<ul class="breadcrumb alert alert-info">
				<li><a href="{url}index.php">Главная</a> <span class="divider"></span></li>
				<li class="active">Управления списком администрации</a></li>
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
					<th><center><i class="icon-wrench"></i> Функции</center></th>
				</thead>
				<tbody>';

    $i = 0;
    foreach ($admins as $data) {

        $i++;

        $fons = 'style="background-color: #f5f5f5;cursor:pointer;"';
        $label = 'alert-success';
        $havedate = 'Осталось(ся) '.ceil((strtotime($data['timelast']) - time()) / 86400).' дн.';
        $data_end = date('d.m.Y [H:i]', strtotime($data['timelast']));

        if(($data['timelast'] < date("Y-m-d")) OR ($data['timelast'] == date("Y-m-d"))){
            $havedate = "Время истекло !";
            $fons = 'class="alert-danger"';
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
			</script>';

        $content .= '
            <tr '.$fons.' style="cursor:pointer;">
				<td><spanrel="tooltip" data-placement="top" data-original-title="Имя"><b>'.$data['name'].'</b></span></td>
				<td><spanrel="tooltip" data-placement="top" data-original-title="Ник"><b>'.$data['nick'].'</b></span></td>
               			<td><spanrel="tooltip" data-placement="top" data-original-title="Skype">'.$skype2.'</span></td>
                		<td><spanrel="tooltip" data-placement="top" data-original-title="icq">'.$icq.'</span></td>
				<td><center><spanrel="tooltip" data-placement="top" data-original-title="Сервера"><b>'.$aceess.'</span></center></td>
				<td><center><span class="badge" rel="tooltip" data-placement="top" data-original-title="Дата покупки"><font style="color: white;">'.date('d.m.Y [H:i]', strtotime($data['timedo'])).'</font></span></center></td>
				<td><center><span class="badge '.$label.'" rel="tooltip" data-placement="top" data-original-title="Осталось дней">'.$havedate.'</span></center></td>
			    <td>
            <center>
                <div class="btn-group">
                    <a data-toggle="dropdown" href="#" class="btn btn-sm dropdown-toggle btn-info"><i class="icon-wrench icon-white"></i> <b>Функции</b> </a>
                    <ul style="left: 15px;" class="dropdown-menu">
                        <li><a href="{url}admin.php?do=admins&edit='.$data['id'].'"><i class="icon-pencil"></i> Редакт.</a></li>
                        <li><a onclick="del'.$data['id'].'()" style="cursor: pointer;"><i class="icon-trash"></i> Удалить</a></li>
                    </ul>
                </div>
            </center>
        </td>
			</tr>
            ';
    }

    $content .= '</tbody></table>';
    $content .= '
    <br />
    <center><a href="{url}admin.php?do=admins&add" title="Добавить Админа/Vip(а)"><button class="btn btn-sm btn-warning" ><i class="icon-plus icon-white"></i> <b>Добавить Админа/Vip(а)</b></button></a></center>';