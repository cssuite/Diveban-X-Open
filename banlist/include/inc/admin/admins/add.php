<?php
$serversOption = '';

$servers = DataBase::getInstance()->fetchAll(Configuration::$banlist['servers_table']);
foreach ($servers as $server) {
    $serversOption .= '<option value="' . $server['ip'] . ':' . $server['port'] . '">' . $server['mode'] . ' [' . $server['ip'] . ':' . $server['port'] . ']</option>';
}

$content = '
<script>$(document).ready(function(){ $("#box").bounceBox();});</script>
<script>
    function send()
    {
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
            data: "name="+name+"&nick="+nick+"&icq="+icq+"&skype="+skype+"&vk="+vk+"&steam="+steam+"&steamid="+steamid+"&passwd="+passwd+"&flags="+flags+"&access="+access+"&timedo="+timedo+"&timelast="+timelast+"&hide_adm="+hide_adm+"&save=1",
            success: function(html) {
                alert(html)
            }
        });
    }
</script>
    <ul class="breadcrumb alert alert-info">
        <li><a href="{url}admin.php">Главная</a> <span class="divider"></span></li>
        <li><a href="{url}admin.php?do=admins">Управления списком администрации</a> <span class="divider"></span></li>
        <li class="active">Добавление админа</li>
    </ul>
    <div class="well alert-info" style="color: black;">
        <table class="table table-bordered" width="100%">
            <tr> <td><b><span style="color:red;">*</span>Имя:</b> </td> <td><input class="form-control" type="text" name="name" id="name" style="width:300px"></td> </tr>
            <tr> <td><b><span style="color:red;">*</span>Ник:</b> </td> <td><input class="form-control" type="text" name="nick" id="nick" style="width:300px"></td> </tr>
            <tr> <td><b>Должность на сервере:</b> </td> <td><input class="form-control" type="text" name="icq" id="icq" style="width:300px"></td> </tr>
            <tr> <td><b>Skype(Логин):</b> </td> <td><input class="form-control" type="text" name="skype" id="skype" style="width:300px"></td> </tr>
            <tr> <td><b>VK(Ссылка на профиль):</b> </td> <td><input class="form-control" type="text" name="vk" id="vk" style="width:300px"></td> </tr>
            <tr> <td><b>Steam(Ссылка на профиль):</b> </td> <td><input class="form-control" type="text" name="steam" id="steam" style="width:300px"></td> </tr>
            <tr> <td><b>SteamID:</b> </td> <td><input class="form-control" type="text" name="steamid" id="steamid" style="width:300px"></td> </tr>
            <tr> <td><b>Password:</b> </td> <td><input class="form-control" type="text" name="passwd" id="passwd" style="width:300px"></td> </tr>
            <tr> <td><b>Flags(Флаги доступа):</b> </td> <td><input class="form-control" type="text" name="flags" id="flags" placeholder="Например:abcdefghijklmnopqrstu" style="width:300px"></td> </tr>
            <tr> <td><b><span style="color:red;">*</span>Сервер(а):</b> </td>
                <td>
                    <select name="access" id="access" multiple style="width:315px">
                        ' . $serversOption . '
                    </select>
                </td> </tr>
            <tr> <td><b><span style="color:red;">*</span>Дата покупки:</b> </td>  <td><input class="form-control" type="date" name="timedo" id="timedo" value="' . date("Y-m-d") . '" style="width:300px"></td> </tr>
            <tr> <td><b><span style="color:red;">*</span>Дата окончания:</b> </td>  <td><input class="form-control" type="date" name="timelast" id="timelast" value="' . date("Y-m-d") . '" style="width:300px"></td> </tr>
            <tr> <td></td><td><label class="checkbox-inline"><input type="checkbox" name="timelast_c" id="timelast_c" value="0"> <b>Неограниченный срок</b></label></td></tr>
            <tr> <td></td><td><label class="checkbox-inline"><input type="checkbox" name="hide_adm" id="hide_adm" value="0"> <b>Скрывать Админа</b></label></td></tr>
        </table>
        <br />
        <input type="button" class="btn btn-inverse" value="Добавить" onclick="send()">
        <br />
    </div>';