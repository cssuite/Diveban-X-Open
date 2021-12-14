<?php
$content .= '<script>
    function send() {
        var id = $("#id").val()
        var name = $("#name").val()
        var password = $("#password").val()
        var group = $("#group").val()
        $.ajax({
            type: "POST",
            url: "{url}admin.php?do=users",
            data: "name=" + name + "&password=" + password + "&group=" + group + "&save=1",
            success: function (html) {
                alert(html)
            }
        });
    }
</script>
<ul class="breadcrumb alert alert-info">
			<li><a href="{url}admin.php">Главная</a> <span class="divider"></span></li>
			<li ><a href="{url}admin.php?do=users">Управление пользователями</a> <span class="divider"></span></li>
			<li class="active">Добавление пользователя</li>
	</ul>
<table class="table table-bordered">
    <tr>
        <td><b><span style="color:red;">*</span>Имя:</b></td>
        <td><input type="text" class="form-control" name="name" id="name" style="width:300px"></td>
    </tr>
    <tr>
        <td><b><span style="color:red;">*</span>Пароль:</b></td>
        <td><input type="text" class="form-control" name="password" id="password" value="" style="width:300px"></td>
    </tr>
    <tr>
        <td><b><span style="color:red;">*</span>Группа:</b></td>
        <td>
            <select name="group" class="form-control" id="group" style="width:315px">
                <option value="1">Гл. Администратор</option>
                <option value="2">Администратор</option>
                <option value="3">Модератор</option>
            </select>
        </td>
    </tr>
</table><input type="button" class="btn btn-warning btn-inverse" value="Добавить" onclick="send()">';