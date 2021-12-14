<?php
$users = DataBase::getInstance()->fetchAll(Configuration::$banlist['users_table'], []);

$content .= '
    <ul class="breadcrumb alert alert-info">
			<li><a href="{url}admin.php">Главная</a> <span class="divider"></span></li>
			<li class="active">Управление пользователями</li>
	</ul>
    <table class="table table-bordered table-hover">
		    <thead class="alert alert-info" style="color: black;">
                <th><i class="icon-hand-right"></i> Имя</th> 
                    <th><i class="icon-user"></i> Группа</th> 
                        <th><center><i class="icon-wrench"></i> Функции</center></th> 
            </thead>
		    <tbody>
';

foreach ($users as $data) {
    $content .= '<script>
    function del' . $data['id'] . '()
    {
        var answer = confirm("Подтвердите ваш запрос на удаления пользователя №' . $data['id'] . '.")
        if (answer) {
            window.location.href = "{url}admin.php?do=users&delete=' . $data['id'] . '"
        } else {
            alert("Запрос успешно отменен!")
        }
    }
</script>
<tr>
    <td><b>' . $data['name'] . '</b></td>
    <td><b><img src="' . User::group_img($data['group']) . '"> ' . User::group($data['group']) . '</b></td>
    <td class="text-center">
        <div class="btn-group">
            <a data-toggle="dropdown" href="#" class="btn btn-sm dropdown-toggle btn-info"><i
                    class="icon-wrench icon-white"></i> <b>Функции</b></a>
            <ul style="left: 75px;" class="dropdown-menu">
                <li><a href="{url}admin.php?do=users&edit=' . $data['id'] . '"><i class="icon-pencil"></i> Редакт.</a></li>
                <li><a onclick="del' . $data['id'] . '()" style="cursor: pointer;"><i class="icon-trash"></i> Удалить</a></li>
            </ul>
        </div>
    </td>
</tr>
					';
}
$content .= '</tbody></table><br/>
<center><a href="{url}admin.php?do=users&add">
    <button class="btn btn-sm btn-warning btn-inverse"><i class="icon-plus icon-white"></i> <b>Добавить пользователя</b>
    </button>
</a></center>';