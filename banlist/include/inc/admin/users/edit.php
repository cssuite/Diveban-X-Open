<?php
$users = DataBase::getInstance()->fetchOne(Configuration::$banlist['users_table'], [ 'id' => $_GET['edit']]);

if ($users) {
    $content .= '<script>
    function send() {
        var id = $("#id").val()
        var name = $("#name").val()
        var password = $("#password").val()
        var group = $("#group").val()
        $.ajax({
            type: "POST",
            url: "{url}admin.php?do=users",
            data: "id=" + id + "&name=" + name + "&password=" + password + "&group=" + group + "&save=1",
            success: function (html) {
                alert(html)
            }
        });
    }
</script>
<ul class="breadcrumb alert alert-info">
			<li><a href="{url}admin.php">Главная</a> <span class="divider"></span></li>
			<li ><a href="{url}admin.php?do=users">Управление пользователями</a> <span class="divider"></span></li>
			<li class="active">Редактирование пользователя</li>
	</ul>
        <table class="table table-bordered" width="100%">
            <tr>
                <td><b><span style="color:red;">*</span>Имя:</b></td>
                <td><input type="text" class="form-control" name="name" id="name" value="' .$users['name']. '" style="width:300px"></td>
            </tr>
            <tr>
                <td><b><span style="color:red;">*</span>Пароль:</b></td>
                <td><input type="text" class="form-control" name="password" id="password" value="" style="width:300px"></td>
            </tr>
            <tr>
                <td><b><span style="color:red;">*</span>Группа:</b></td>
                <td>
                    <select class="form-control" name="group" id="group" style="width:315px">
                        <option value="1" ' .($data['group']==1?'selected="selected"':''). '>Гл. Администратор</option>
                        <option value="2" ' .($data['group']==2?'selected="selected"':''). '>Администратор</option>
                        <option value="3" ' .($data['group']==3?'selected="selected"':''). '>Модератор</option>
                    </select>
                </td>
            </tr>
        </table>
        <br/>
        <input type="hidden" name="id" id="id" value="' .$users['id']. '"><input type="button" value="Сохранить"
                                                                    class="btn btn-warning" onclick="send()">';
} else {
    $content .= '<div class="alert alert-error">
    <table width="50%">
        <tr>
            <td><img src="{url}style/img/oshibka.png" width="150" height="150"/></td>
            <td>
                <center><h4 class="alert-heading">Ошибка!</h4>
                    <p><b>Пользователь не найден.<b></p></center>
            </td>
        </tr>
    </table>
</div>';
}