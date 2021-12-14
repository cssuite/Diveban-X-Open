<?php
$adminID = $_GET['edit'] ?? 0;
$data = DataBase::getInstance()->fetchOne(Configuration::$banlist['admins_table'], ['id' => (int)$adminID]);

if ($data) {
    $servers = DataBase::getInstance()->fetchAll(Configuration::$banlist['servers_table']);

    $serverOptions = '';
    foreach ($servers as $server) {
        $serverOptions .=  '<option value="'.$server['ip'].':'.$server['port'].'">'.$server['mode'].' ['.$server['ip'].':'.$server['port'].']</option>';
    }

    if($data['timelast'] == 0){
        $chekbox = 'checked';
    }

    if($data['hide'] == 1){
        $chekbox2 = 'checked';
    }
    $content = '
<script>
    function send()
    {
        var id = $("#id").val()
        var name = $("#name").val()
        var nick = $("#nick").val()
        var icq = $("#icq").val()
        var skype = $("#skype").val()
        var vk = $("#vk").val()
        var steam = $("#steam").val()
        var steamid = $("#steamid").val()
        var passwd = $("#passwd").val()
        var flags = $("#flags").val()
        var access = $("#access").val()
        var timedo = $("#timedo").val()

        if ($("#timelast_c").prop("checked")) {
            var timelast = $("#timelast_c").val()
        } else {
            var timelast = $("#timelast").val()
        }
        if ($("#hide_adm").prop("checked")) {
            var hide_adm = 1
        } else {
            var hide_adm = 0
        }
        $.ajax({
            type: "POST",
            url: "{url}admin.php?do=admins",
            data: "id="+id+"&name="+name+"&nick="+nick+"&icq="+icq+"&skype="+skype+"&vk="+vk+"&steam="+steam+"&steamid="+steamid+"&passwd="+passwd+"&flags="+flags+"&access="+access+"&timedo="+timedo+"&timelast="+timelast+"&hide_adm="+hide_adm+"&save=1",
            success: function(html) {
                alert(html)
            }
        });
    }
</script>
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
    <ul class="breadcrumb alert alert-info">
        <li><a href="{url}admin.php">Главная</a> <span class="divider"></span></li>
        <li><a href="{url}admin.php?do=admins">Управления списком администрации</a> <span class="divider"></span></li>
        <li class="active">Редактирование админки '.$data['nick'].' ('.$data['name'].')</li>
    </ul>
    <div class="well alert-info" style="color: black;">
        <table class="table table-bordered" width="100%">
            <tr> <td><b><span style="color:red;">*</span>Имя:</b> </td> <td><input class="form-control" type="text" name="name" id="name" value="'.$data['name'].'" style="width:300px"></td> </tr>
            <tr> <td><b><span style="color:red;">*</span>Ник:</b> </td> <td><input class="form-control" type="text" name="nick" id="nick" value="'.$data['nick'].'" style="width:300px"></td> </tr>
            <tr> <td><b>Должность:</b> </td> <td><input class="form-control" type="text" name="icq" id="icq" value="'.$data['icq'].'" style="width:300px"></td> </tr>
            <tr> <td><b>Skype(Логин):</b> </td> <td><input class="form-control" type="text" name="skype" id="skype" value="'.$data['skype'].'" style="width:300px"></td> </tr>
            <tr> <td><b>VK(Ссылка на профиль):</b> </td> <td><input class="form-control" type="text" name="vk" id="vk" value="'.$data['vk'].'" style="width:300px"></td> </tr>
            <tr> <td><b>Steam(Ссылка на профиль):</b> </td> <td><input class="form-control" type="text" name="steam" id="steam" value="'.$data['steam'].'" style="width:300px"></td> </tr>
            <tr> <td><b>Steam ID:</b> </td> <td><input class="form-control" type="text" name="steamid" id="steamid" value="'.$data['steamid'].'" style="width:300px"></td> </tr>
            <tr> <td><b>Пароль:</b> </td> <td><input class="form-control" type="text" name="passwd" id="passwd" value="'.$data['passwd'].'" style="width:300px"></td> </tr>
            <tr> <td><b>Flags(Флаги доступа):</b> </td> <td><input class="form-control" type="text" name="flags" id="flags" value="'.$data['flags'].'" placeholder="Например:abcdefghijklmnopqrstu" style="width:300px"></td> </tr>
            <tr> <td><b><span style="color:red;">*</span>Сервера:</b> </td>
                <td>
                    <select name="access" multiple id="access" style="width:315px">
                        '.$serverOptions.'
                    </select>
                </td> </tr>
            <tr> <td><b><span style="color:red;">*</span>Дата покупки:</b> </td>  <td><input class="form-control" type="date" name="timedo" id="timedo" value="'.$data['timedo'].'" style="width:300px"></td> </tr>
            <tr> <td><b><span style="color:red;">*</span>Дата окончания:</b> </td>  <td><input class="form-control" type="date" name="timelast" id="timelast" value="'.$data['timelast'].'" style="width:300px"></td> </tr>
            <tr> <td></td><td><label class="checkbox-inline"><input type="checkbox" name="timelast_c" id="timelast_c" value="0" '.$chekbox.'> <b>Неограниченный срок</b></label></td></tr>
            <tr> <td></td><td><label class="checkbox-inline"><input type="checkbox" name="hide_adm" id="hide_adm" value="0" '.$chekbox2.'> <b>Скрывать админа</b></label></td></tr>
        </table>
        <br />
        <input type="hidden" name="id" id="id" value="'.$data['id'].'"><input type="button" class="btn btn-success" value="Сохранить" onclick="send()">
        <a onclick="del'.$data['id'].'()" style="cursor: pointer;" class="btn btn-danger">Удалить</a>
        <br />
    </div>';
} else {
    $content .= '
<div class="alert alert-error">
    <table width="50%">
        <tr>
            <td><img src="{url}style/img/oshibka.png" width="150" height="150"/></td>
            <td><center><h4 class="alert-heading">Ошибка!</h4> <p><b>Админ не найден.<b></p></center></td>
        </tr>
    </table>
</div>';
}