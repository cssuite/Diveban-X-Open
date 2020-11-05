<?php
$content .= '
<script>
    function send()
    {
        var ip = $("#ip").val()
        var port = $("#port").val()
        var mode = $("#mode").val()
        $.ajax({
            type: "POST",
            url: "{url}admin.php?do=servers",
            data: "ip="+ip+"&port="+port+"&mode="+mode+"&save=1",
            success: function(html) {
                alert(html)
            }
        });
    }
</script>
    <ul class="breadcrumb alert alert-info">
        <li><a href="{url}admin.php">Главная</a> <span class="divider"></span></li>
        <li><a href="{url}admin.php?do=servers">Управления серверами</a> <span class="divider"></span></li>
        <li class="active">Добавление сервера</li>
    </ul>
    <div class="well alert-info" style="color: black;">
        <table class="table table-bordered" width="100%">
            <tr> <td><b><span style="color:red;">*</span>IP-адрес:</b> </td> <td><input class="form-control" type="text" style="width:300px" name="ip" id="ip"></td> </tr>
            <tr> <td><b><span style="color:red;">*</span>Порт:</b> </td>  <td><input class="form-control" type="text" style="width:300px" name="port" id="port"></td> </tr>
            <tr> <td><b><span style="color:red;">*</span>Мод игры:</b> </td>  <td><input  class="form-control" type="text" style="width:300px" name="mode" id="mode"></td> </tr>
        </table>
        <br /><input type="button" class="btn btn-inverse" value="Добавить" onclick="send()">
    </div>';