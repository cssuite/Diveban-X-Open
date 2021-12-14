<?PHP
error_reporting(E_ALL);
require_once "config.php";

$addr = isset($_SERVER["HTTP_X_FORWARDED_FOR"]) ? $_SERVER["HTTP_X_FORWARDED_FOR"] : $_SERVER["REMOTE_ADDR"]; 

$user = Divebans::getInstance()->getInfoByIPCookie($addr);
Divebans::getInstance()->setUserCookie( Divebans::getCookieName(), $user );


?>
<!DOCTYPE>
<html lang="ru">
<head>
	<title>You are banned!</title>
    <meta charset="UTF-8">

	<style type="text/css">
		body {
			padding-top: 30px;
			background-image: url(bg.png);
		}
		
		.con
		{
			width: 600px;
			margin: 0px auto;
			border: 1px solid #DD0000;
			border-radius: 10px;
		}
		
		.time {
			font-weight: bold;
			color: #FF5A00;
		}

		.bold {
			font-weight: bold;
		}

		table {
			border-collapse: collapse;
			width: 100%;
			max-width: 100%;
			margin: 10px;
		}

		td {
			border-bottom: 0;
			padding: 0;
		}

		.header {
			border-bottom: 1px solid #DDDDDD;
			text-align: center;
			color: orange;
			font-weight: bold;
		}

		.tl, .tr, .bl, .br {
			height: 10px;
			width: 10px;
			overflow: hidden;
			padding: 0;
		}
	</style>
</head>
<body>
	<div class="con">
		<div class="header">
			<h1>Вы забанены!</h1>
		</div>
		<div>
			<table>
				<tr>
				<td colspan="2" style="text-align:center;font-weight:bold; color:orange"> Diveban <span style="color:red">X</span></td>
				</tr>
				<tr>
					<td class="bold">Ник:</td>
					<td><?php echo $_GET['NICK']; ?></td>
				</tr>
				<tr>
					<td class="bold">Причина:</td>
					<td><?php echo $_GET['REASON']; ?></td>
				</tr>
				<tr>
					<td class="bold">Продолжительность:</td>
					<td class="time"><?php echo "".$_GET['TIME']." Минут"; ?></td>
				</tr>
				<tr>
					<td colspan="2">&nbsp;</td>
				</tr>
				<tr>
					<td class="bold">Окончание:</td>
					<td><?php echo date("d.m.Y H:i", $_GET['UNBAN']); ?></td>
				</tr>
				<tr>
					<td class="bold">Админом:</td>
					<td><?php echo $_GET['ADMIN']; ?></td>
				</tr>
				<tr>
					<td class="bold">Разбан:</td>
					<td><?php echo $_GET['URL']; ?></td>
				</tr>
			</table>
		</div>
	</div>
</body>
</html>