<?php
$banid = $_GET['edit'] ?? 0;
$data = DataBase::getInstance()->fetchOne(Configuration::$banlist['table'], ['banid' => (int)($banid)]);

if ($data) {
    $ban_edit = date('Y-m-d', $data['unbantime']);
    if($data['unbantime'] == 0){
        $ban_edit = '';
        $chekbox = 'checked';
    } else if($data['unbantime'] == -1) {
        $ban_edit = '';
    }

    $ban_type_pop = ' <strong>A:</strong> AuthID(SteamID)<br>
                          <strong>I:</strong> IP<br>
                          <strong>C:</strong> Cookie<br>
                          <strong>U:</strong> Unique ID<br>
                          <strong>S\F:</strong> Subnet\ Full subnet<br>
                          <strong>K:</strong> CD-KEY<br>
                          <strong>D:</strong> DiveID<br>
 ';

    $content = '<script>
    function send() {
        var banid = $("#banid").val();
        var banname = $("#banname").val();
        var reason = $("#reason").val();
        var ip = $("#ip").val();
        var steam = $("#steam").val();
        var uid = $("#uid").val();
        var admin = $("#admin").val();
        var unbantime = $("#unbantime_c").prop("checked") ?  $("#unbantime_c").val() : $("#unbantime").val();
        var bantype = $("#bantype").val();
        
        $.ajax({
            type: "POST",
            url: "{url}admin.php?do=bans",
            data: "banid=" + banid + "&banname=" + banname + "&reason=" + reason + "&ip=" + ip + "&steam=" + steam + "&uid=" + uid + "&admin=" + admin + "&unbantime=" + unbantime + "&bantype=" + bantype + "&save=1",
            success: function (html) {
                alert(html)
            }
        });
    }

    function del' .$data['banid']. '()
    {
        var answer = confirm("Подтвердите ваш запрос на удаления бана №' .$data['banid']. '.");
        if (answer) {
            window.location.href = "{url}admin.php?do=bans&delete=' .$data['banid']. '"
        } else {
            alert("Запрос успешно отменен!")
        }
    }
</script>
    <ul class="breadcrumb alert alert-info">
        <li><a href="{url}admin.php">Главная</a> <span class="divider"></span></li>
        <li><a href="{url}admin.php?do=bans">Управления списком забаненных</a> <span class="divider"></span></li>
        <li class="active">Редактирование бана игрока: ' .$data['banname']. '</li>
    </ul>
    <div class="well alert-info" style="color: black;">
        <table class="table table-bordered">
            <tr>
                <td><b><span style="color:red;">*</span> Ник:</b></td>
                <td><input type="text" class="form-control" name="banname" id="banname" style="width:300px" value="' .$data['banname']. '"></td>
            </tr>
            <tr>
                <td><b><span style="color:red;">*</span> Причина:</b></td>
                <td><input type="text" class="form-control" name="reason" id="reason" style="width:300px" value="' .$data['reason']. '"></td>
            </tr>
            <tr>
                <td><b><span style="color:red;">*</span> IP Адрес:</b></td>
                <td><input type="text" class="form-control" name="ip" id="ip" style="width:300px" value="' .$data['ip']. '"></td>
            </tr>
            <tr>
                <td><b>Steam:</b></td>
                <td><input type="text" class="form-control" name="steam" id="steam" style="width:300px" value="' .$data['steam']. '"></td>
            </tr>
            <tr>
                <td><b>UID:</b></td>
                <td><input type="text" class="form-control" name="bantype" id="uid" style="width:300px" value="' .$data['uid']. '"></td>
            </tr>
            <tr>
                <td><b>ТипБана:</b> <i data-placement="'.Configuration::$main['m_popover'].'" rel="popover" data-trigger="hover" data-html="true" data-content="'.$ban_type_pop.'" class="icon-info"></i> </td>
                <td><input type="text" class="form-control" name="bantype" id="bantype" style="width:300px" value="' .$data['bantype']. '"></td>
            </tr>
            <tr>
                <td><b><span style="color:red;">*</span> Ник админа:</b></td>
                <td><input type="text" class="form-control" name="admin" id="admin" style="width:300px" value="' .$data['admin']. '"></td>
            </tr>
            <tr>
                <td><b><span style="color:red;">*</span> Истекает:</b></td>
                <td><input type="date" class="form-control" name="unbantime" id="unbantime" style="width:300px" value="' .$ban_edit. '"></td>
            </tr>
            <tr>
                <td></td>
                <td>
                <label class="checkbox-inline"><input type="checkbox" name="unbantime_c" id="unbantime_c" value="0" ' .$chekbox. '>Навсегда</label>
                    </td>
            </tr>
        </table>
        <br/>
        <input type="hidden" name="banid" id="banid" value="' .$data['banid']. '">
        <input type="button"
                                                                                              value="Сохранить"
                                                                                              class="btn btn-success"
                                                                                              onclick="send()">
        <a onclick="del' .$data['banid']. '()" style="cursor: pointer;" class="btn btn-danger">Удалить</a>
    </div>';
}
else $content .= '
			<div class="alert alert-error">
				<table width="50%">
					<tr>
						<td><img src="{url}style/img/oshibka.png" width="150" height="150"/></td>
						<td><center><h4 class="alert-heading">Ошибка!</h4> <p><b>Игрок не найден.<b></p></center></td>
					</tr>
				</table>
			</div>';