<?php
$serverID = $_GET['edit'] ?? 0;
$server = DataBase::getInstance()->fetchOne(Configuration::$banlist['servers_table'], [ 'id' => (int)$serverID]);

if ($server) {
    $content .= '
<script>
    function send()
    {
        var id = $("#id").val()
        var ip = $("#ip").val()
        var port = $("#port").val()
        var mode = $("#mode").val()
        $.ajax({
            type: "POST",
            url: "{url}admin.php?do=servers",
            data: "id="+id+"&ip="+ip+"&port="+port+"&mode="+mode+"&save=1",
            success: function(html) {
                alert(html)
            }
        });
    }
</script>
    <ul class="breadcrumb alert alert-info">
        <li><a href="{url}admin.php">Главная</a> <span class="divider"></span></li>
        <li><a href="{url}admin.php?do=servers">Управления серверами</a> <span class="divider"></span></li>
        <li class="active">Редактирование сервера ' . $data['ip'] . ':' . $data['port'] . '</li>
    </ul>
    <div class="well alert-info" style="color: black;">
        <table class="table table-bordered">
            <tr> <td><b><span style="color:red;">*</span>IP-адрес:</b> </td> <td><input class="form-control" type="text" name="ip" id="ip" style="width:300px" value="' . $data['ip'] . '"></td> </tr>
            <tr> <td><b><span style="color:red;">*</span>Порт:</b> </td>  <td><input class="form-control" type="text" name="port" id="port" style="width:300px" value="' . $data['port'] . '"></td> </tr>
            <tr> <td><b><span style="color:red;">*</span>Мод игры:</b> </td>  <td><input class="form-control" type="text" name="mode" id="mode" style="width:300px" value="' . $data['mode'] . '"></td> </tr>
        </table>
        <br />
        <input type="hidden" name="id" id="id" value="' . $data['id'] . '"><input type="button" class="btn btn-inverse" value="Сохранить" onclick="send()">
    </div>
</div>';
} else {
    $content .= '
<div class="alert alert-error">
    <table width="50%">
        <tr>
            <td><img src="{url}style/img/oshibka.png" width="150" height="150"/></td>
            <td><center><h4 class="alert-heading">Ошибка!</h4> <p><b>Сервер не найден.<b></p></center></td>
        </tr>
    </table>
</div>';
}