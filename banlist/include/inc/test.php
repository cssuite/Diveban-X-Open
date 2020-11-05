<?php
if($user['group'] == 1)
{
if(isset($_GET['edit']))
{
$users = mysql_query('SELECT * FROM `diveban_admins` WHERE id='.abs((int)$_GET['edit']).'');
$qt = '';

$query = mysql_query('SELECT * FROM `superban_servers` ');
while ($server = mysql_fetch_assoc($query))
{
$qt .= '<option value="'.$server['ip'].':'.$server['port'].'">'.$server['mode'].' ['.$server['ip'].':'.$server['port'].']</option>';
}
if(mysql_num_rows($users) > 0){
while ($data = mysql_fetch_assoc($users))
{
if($data['timelast'] == 0){
$chekbox = 'checked';
}

if($data['hide'] == 1){
$chekbox2 = 'checked';
}
$content = '
<script>$(document).ready(function(){ $("#box").bounceBox();});</script>
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
                $("#box_result").empty();
                $("#box_result").append(html);
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
<div class="well well-small">
    <div class="tabbable">
        <ul class="nav nav-tabs">
            <li><a href="#tab1" data-toggle="tab"><b>Админцентр</b></a></li>
            <li class="active"><a href="#tab2" data-toggle="tab"><b>Сервер</b></a></li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane" id="tab1">
                <a href="{url}admin.php" class="btn btn-small btn-inverse">Информация о системе</a>
                <a href="{url}admin.php?do=settings" class="btn btn-small btn-inverse">Глобальные настройки</a>
                <a href="{url}admin.php?do=users" class="btn btn-small btn-inverse">Управления пользователями</a>
                <br></br>
            </div>
            <div class="tab-pane active" id="tab2">
                <a href="{url}admin.php?do=bans" class="btn btn-small btn-inverse">Управления банами</a>
                <a href="{url}admin.php?do=servers" class="btn btn-small btn-inverse">Управления серверами</a>
                <a href="{url}admin.php?do=admins" class="btn btn-small btn-inverse disabled">Список администрации</a>
                <br></br>
            </div>
        </div>
    </div>
    <ul class="breadcrumb alert alert-info">
        <li><a href="{url}admin.php">Главная</a> <span class="divider"></span></li>
        <li><a href="{url}admin.php?do=admins">Управления списком администрации</a> <span class="divider"></span></li>
        <li class="active">Редактирование админки '.$data['nick'].' ('.$data['name'].')</li>
    </ul>
    <div class="well alert-info" style="color: black;">
        <table width="100%">
            <tr> <td><b><span style="color:red;">*</span>Имя:</b> </td> <td><input type="text" name="name" id="name" value="'.$data['name'].'" style="width:300px"></td> </tr>
            <tr> <td><b><span style="color:red;">*</span>Ник:</b> </td> <td><input type="text" name="nick" id="nick" value="'.$data['nick'].'" style="width:300px"></td> </tr>
            <tr> <td><b>Должность:</b> </td> <td><input type="text" name="icq" id="icq" value="'.$data['icq'].'" style="width:300px"></td> </tr>
            <tr> <td><b>Skype(Логин):</b> </td> <td><input type="text" name="skype" id="skype" value="'.$data['skype'].'" style="width:300px"></td> </tr>
            <tr> <td><b>VK(Ссылка на профиль):</b> </td> <td><input type="text" name="vk" id="vk" value="'.$data['vk'].'" style="width:300px"></td> </tr>
            <tr> <td><b>Steam(Ссылка на профиль):</b> </td> <td><input type="text" name="steam" id="steam" value="'.$data['steam'].'" style="width:300px"></td> </tr>
            <tr> <td><b>Steam ID:</b> </td> <td><input type="text" name="steamid" id="steamid" value="'.$data['steamid'].'" style="width:300px"></td> </tr>
            <tr> <td><b>Пароль:</b> </td> <td><input type="text" name="passwd" id="passwd" value="'.$data['passwd'].'" style="width:300px"></td> </tr>
            <tr> <td><b>Flags(Флаги доступа):</b> </td> <td><input type="text" name="flags" id="flags" value="'.$data['flags'].'" placeholder="Например:abcdefghijklmnopqrstu" style="width:300px"></td> </tr>
            <tr> <td><b><span style="color:red;">*</span>Сервера:</b> </td>
                <td>
                    <select name="access" multiple id="access" style="width:315px">
                        '.$qt.'
                    </select>
                </td> </tr>
            <tr> <td><b><span style="color:red;">*</span>Дата покупки:</b> </td>  <td><input type="date" name="timedo" id="timedo" value="'.$data['timedo'].'" style="width:300px"></td> </tr>
            <tr> <td><b><span style="color:red;">*</span>Дата окончания:</b> </td>  <td><input type="date" name="timelast" id="timelast" value="'.$data['timelast'].'" style="width:300px"></td> </tr>
            <tr> <td></td><td><label class="checkbox"><input type="checkbox" name="timelast_c" id="timelast_c" value="0" '.$chekbox.'> <b>Неограниченный срок</b></label></td></tr>
            <tr> <td></td><td><label class="checkbox"><input type="checkbox" name="hide_adm" id="hide_adm" value="0" '.$chekbox2.'> <b>Скрывать админа</b></label></td></tr>
        </table>
        <br />
        <input type="hidden" name="id" id="id" value="'.$data['id'].'"><input type="button" class="btn btn-inverse" value="Сохранить" onclick="send()">
        <a onclick="del'.$data['id'].'()" style="cursor: pointer;" class="btn btn-danger">Удалить</a>
        <br />
    </div>
</div>';
}
} else {
$content = '
<div class="alert alert-error">
    <table width="50%">
        <tr>
            <td><img src="{url}style/img/oshibka.png" width="150" height="150"/></td>
            <td><center><h4 class="alert-heading">Ошибка!</h4> <p><b>Пользователь не найден.<b></p></center></td>
        </tr>
    </table>
</div>';
}
} else if(isset($_POST['save']) and isset($_POST['name']) and isset($_POST['nick'])) {
if($_POST['name'] == '') $error[] =  'имя';
if($_POST['nick'] == '') $error[] =  'ник';
if(!isset($error)) $save = mysql_query("UPDATE `diveban_admins` SET `name`='".mysql_escape_string($_POST['name'])."', `nick`='".mysql_escape_string($_POST['nick'])."', `icq`='".mysql_escape_string($_POST['icq'])."', `skype`='".mysql_escape_string($_POST['skype'])."', `vk`='".mysql_escape_string($_POST['vk'])."', `steam`='".mysql_escape_string($_POST['steam'])."', `steamid`='".mysql_escape_string($_POST['steamid'])."', `passwd`='".mysql_escape_string($_POST['passwd'])."', `flags`='".mysql_escape_string($_POST['flags'])."', `access`='".mysql_escape_string($_POST['access'])."', `timedo`='".mysql_escape_string($_POST['timedo'])."', `timelast`='".mysql_escape_string($_POST['timelast'])."', `hide`='".mysql_escape_string($_POST['hide_adm'])."' WHERE id='".mysql_escape_string($_POST['id'])."'") or die(mysql_error());
die("<script>$(document).ready(function(){ $('#box').bounceBox(); $('#box').bounceBoxToggle(); $('#box').click(function(){ $('#box').bounceBoxHide(); }); });</script>
<div id='box'>
    ".($error ? '<b>Вы не ввели '.implode(', ',$error).' администратора</b>' : '<b>Сохранено</b>').".<br>
    <div style='text-align:right;'><a href='#'>Закрыть</a></div>
</div>");
} else if(isset($_GET['add'])) {
$qt = '';

$query = mysql_query('SELECT * FROM `superban_servers` ');
while ($server = mysql_fetch_assoc($query))
{
//echo $server['ip'];
$qt .= '<option value="'.$server['ip'].':'.$server['port'].'">'.$server['mode'].' ['.$server['ip'].':'.$server['port'].']</option>';
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
            data: "name="+name+"&nick="+nick+"&icq="+icq+"&skype="+skype+"&vk="+vk+"&steam="+steam+"&steamid="+steamid+"&passwd="+passwd+"&flags="+flags+"&access="+access+"&timedo="+timedo+"&timelast="+timelast+"&hide_adm="+hide_adm+"&add_new=1",
            success: function(html) {
                $("#box_result").empty();
                $("#box_result").append(html);
            }
        });
    }
</script>
<div class="well well-small">
    <div class="tabbable">
        <ul class="nav nav-tabs">
            <li><a href="#tab1" data-toggle="tab"><b>Админцентр</b></a></li>
            <li class="active"><a href="#tab2" data-toggle="tab"><b>Сервер</b></a></li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane" id="tab1">
                <a href="{url}admin.php" class="btn btn-small btn-inverse">Информация о системе</a>
                <a href="{url}admin.php?do=settings" class="btn btn-small btn-inverse">Глобальные настройки</a>
                <a href="{url}admin.php?do=users" class="btn btn-small btn-inverse">Управления пользователями</a>
                <br></br>
            </div>
            <div class="tab-pane active" id="tab2">
                <a href="{url}admin.php?do=bans" class="btn btn-small btn-inverse">Управления банами</a>
                <a href="{url}admin.php?do=servers" class="btn btn-small btn-inverse">Управления серверами</a>
                <a href="{url}admin.php?do=admins" class="btn btn-small btn-inverse disabled">Список администрации</a>
                <br></br>
            </div>
        </div>
    </div>
    <ul class="breadcrumb alert alert-info">
        <li><a href="{url}admin.php">Главная</a> <span class="divider"></span></li>
        <li><a href="{url}admin.php?do=admins">Управления списком администрации</a> <span class="divider"></span></li>
        <li class="active">Добавление админа</li>
    </ul>
    <div class="well alert-info" style="color: black;">
        <table width="100%">
            <tr> <td><b><span style="color:red;">*</span>Имя:</b> </td> <td><input type="text" name="name" id="name" style="width:300px"></td> </tr>
            <tr> <td><b><span style="color:red;">*</span>Ник:</b> </td> <td><input type="text" name="nick" id="nick" style="width:300px"></td> </tr>
            <tr> <td><b>Должность на сервере:</b> </td> <td><input type="text" name="icq" id="icq" style="width:300px"></td> </tr>
            <tr> <td><b>Skype(Логин):</b> </td> <td><input type="text" name="skype" id="skype" style="width:300px"></td> </tr>
            <tr> <td><b>VK(Ссылка на профиль):</b> </td> <td><input type="text" name="vk" id="vk" style="width:300px"></td> </tr>
            <tr> <td><b>Steam(Ссылка на профиль):</b> </td> <td><input type="text" name="steam" id="steam" style="width:300px"></td> </tr>
            <tr> <td><b>SteamID:</b> </td> <td><input type="text" name="steamid" id="steamid" style="width:300px"></td> </tr>
            <tr> <td><b>Password:</b> </td> <td><input type="text" name="passwd" id="passwd" style="width:300px"></td> </tr>
            <tr> <td><b>Flags(Флаги доступа):</b> </td> <td><input type="text" name="flags" id="flags" placeholder="Например:abcdefghijklmnopqrstu" style="width:300px"></td> </tr>
            <tr> <td><b><span style="color:red;">*</span>Сервер(а):</b> </td>
                <td>
                    <select name="access" id="access" multiple style="width:315px">
                        '.$qt.'
                    </select>
                </td> </tr>
            <tr> <td><b><span style="color:red;">*</span>Дата покупки:</b> </td>  <td><input type="date" name="timedo" id="timedo" value="'.date("Y-m-d").'" style="width:300px"></td> </tr>
            <tr> <td><b><span style="color:red;">*</span>Дата окончания:</b> </td>  <td><input type="date" name="timelast" id="timelast" value="'.date("Y-m-d").'" style="width:300px"></td> </tr>
            <tr> <td></td><td><label class="checkbox"><input type="checkbox" name="timelast_c" id="timelast_c" value="0"> <b>Неограниченный срок</b></label></td></tr>
            <tr> <td></td><td><label class="checkbox"><input type="checkbox" name="hide_adm" id="hide_adm" value="0"> <b>Скрывать Админа</b></label></td></tr>
        </table>
        <br />
        <input type="button" class="btn btn-inverse" value="Добавить" onclick="send()">
        <br />
    </div>
</div>';
} else if(isset($_POST['add_new']) and isset($_POST['name']) and isset($_POST['nick'])) {
if($_POST['name'] == '') $error[] =  'имя';
if($_POST['nick'] == '') $error[] =  'ник';
if(!isset($error)) $add = mysql_query("INSERT INTO `diveban_admins` (`id`, `name`, `nick`, `icq`, `skype`, `vk`, `steam`, `steamid`, `passwd`, `flags`, `access`, `timedo`, `timelast`, `hide`) VALUES (NULL,'".mysql_escape_string($_POST['name'])."', '".mysql_escape_string($_POST['nick'])."', '".mysql_escape_string($_POST['icq'])."', '".mysql_escape_string($_POST['skype'])."', '".mysql_escape_string($_POST['vk'])."', '".mysql_escape_string($_POST['steam'])."', '".mysql_escape_string($_POST['steamid'])."', '".mysql_escape_string($_POST['passwd'])."', '".mysql_escape_string($_POST['flags'])."', '".mysql_escape_string($_POST['access'])."', '".mysql_escape_string($_POST['timedo'])."', '".mysql_escape_string($_POST['timelast'])."', '".mysql_escape_string($_POST['hide_adm'])."')") or die(mysql_error());
die("<script>$(document).ready(function(){ $('#box').bounceBox(); $('#box').bounceBoxToggle(); $('#box').click(function(){ $('#box').bounceBoxHide(); }); });</script>
<div id='box'>
    ".($error ? '<b>Вы не ввели '.implode(', ',$error).' администратора</b>' : '<b>Администратор добавлен</b>').".<br>
    <div style='text-align:right;'><a href='#'>Закрыть</a></div>
</div>");
} else if(isset($_GET['delete'])) {
$delete = mysql_query ("DELETE FROM `diveban_admins` WHERE id='".abs((int)$_GET['delete'])."'") or die(mysql_error());
header('Location: admin.php?do=admins');
} else {
$users = mysql_query('SELECT * FROM `diveban_admins` ORDER BY `timelast`');
$content = '
<div class="well well-small">
    <div class="tabbable">
        <ul class="nav nav-tabs">
            <li><a href="#tab1" data-toggle="tab"><b>Админцентр</b></a></li>
            <li class="active"><a href="#tab2" data-toggle="tab"><b>Сервер</b></a></li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane" id="tab1">
                <a href="{url}admin.php" class="btn btn-small btn-inverse">Информация о системе</a>
                <a href="{url}admin.php?do=settings" class="btn btn-small btn-inverse">Глобальные настройки</a>
                <a href="{url}admin.php?do=users" class="btn btn-small btn-inverse">Управления пользователями</a>
                <br></br>
            </div>
            <div class="tab-pane active" id="tab2">
                <a href="{url}admin.php?do=bans" class="btn btn-small btn-inverse">Управления банами</a>
                <a href="{url}admin.php?do=servers" class="btn btn-small btn-inverse">Управления серверами</a>
                <a href="{url}admin.php?do=admins" class="btn btn-small btn-inverse disabled">Список администрации</a>
                <br></br>
            </div>
        </div>
    </div>
    <ul class="breadcrumb alert alert-info">
        <li><a href="{url}admin.php">Главная</a> <span class="divider"></span></li>
        <li class="active">Управления списком администрации</a></li>
    </ul>';
    if(mysql_num_rows($users) > 0){
    $content .= '
    <table class="table table-bordered table-hover">
        <thead class="alert-info" style="color: black;">
        <th><i class="icon-hand-right"></i> Имя</th>
        <th><i class="icon-user"></i> Ник</th>
        <th><center><i class="icon-flag"></i> Должность</center></th>
        <th><center><i class="icon-calendar"></i> Дата покупки</center></th>
        <th><center><i class="icon-calendar"></i> Срок</center></th>
        <th><center><i class="icon-wrench"></i> Функции</center></th>
        </thead>
        <tbody>';
        while ($data = mysql_fetch_assoc($users))
        {
        $fons = 'style="background-color: #f5f5f5;"';
        $label = 'label-success';
        $havedate = 'Осталось(ся) '.ceil((strtotime(''.$data['timelast'].'') - time()) / 86400).' дн.';
        if(($data['timelast'] < date("Y-m-d")) OR ($data['timelast'] == date("Y-m-d"))){
        $havedate = "Время истекло !";
        $fons = 'class="error"';
        $label = 'label-important';
        }
        if($data['timelast'] == 0){
        $havedate = "Неограниченный срок !";
        $fons = 'style="background-color: #f5f5f5;"';
        $label = 'label-success';
        }
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
        <tr '.$fons.'>
        <td><span class="label label-important" rel="tooltip" data-placement="top" data-original-title="Имя">'.$data['name'].'</span></td>
        <td><span class="label label-info" rel="tooltip" data-placement="top" data-original-title="Ник">'.$data['nick'].'</span></td>
        <td><center><span class="label label-success" rel="tooltip" data-placement="top" data-original-title="Должность">'.$data['icq'].'</span></center></td>
        <td><center><span class="label" rel="tooltip" data-placement="top" data-original-title="Дата покупки"><font style="color: white;">'.date('d.m.Y [H:i]', strtotime($data['timedo'])).'</font></span></center></td>
        <td><center><span class="label '.$label.'" rel="tooltip" data-placement="top" data-original-title="Осталось дней"><font style="color: white;">'.$havedate.'</font></span></center></td>
        <td>
            <center>
                <div class="btn-group">
                    <a data-toggle="dropdown" href="#" class="btn btn-mini dropdown-toggle btn-inverse"><i class="icon-wrench icon-white"></i> <b>Функции</b> </a>
                    <ul style="left: 15px;" class="dropdown-menu">
                        <li><a href="{url}admin.php?do=admins&edit='.$data['id'].'"><i class="icon-pencil"></i> Редакт.</a></li>
                        <li><a onclick="del'.$data['id'].'()" style="cursor: pointer;"><i class="icon-trash"></i> Удалить</a></li>
                    </ul>
                </div>
            </center>
        </td>
        </tr>';
        }
        $content .= '</tbody></table>';
    } else {
    $content .= '<div class="alert alert-info">Список администрации пуст.</div>';
    }

    $content .= '
    <br />
    <center><a href="{url}admin.php?do=admins&add" title="Добавить Админа/Vip(а)"><button class="btn btn-small btn-inverse" ><i class="icon-plus icon-white"></i> <b>Добавить Админа/Vip(а)</b></button></a></center>
</div>';
}
} else {
$content = '
<div class="alert alert-error">
    <table width="90%">
        <tr>
            <td><img src="{url}style/img/oshibka.png" width="150" height="150"/></td>
            <td><center><h4 class="alert-heading">Ошибка!</h4> <p><b>У вас не достаточно прав для редактирования списка, это могут делать только пользователи относящиеся к группе: "Гл. Администратор".<b></p></center></td>
        </tr>
    </table>
</div>';
}