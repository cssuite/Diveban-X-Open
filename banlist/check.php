<?PHP
error_reporting(E_ALL);
require_once "config.php";

$addr = isset($_SERVER["HTTP_X_FORWARDED_FOR"]) ? $_SERVER["HTTP_X_FORWARDED_FOR"] : $_SERVER["REMOTE_ADDR"];

if ( isset($_COOKIE[Divebans::getCookieName()]) ) {

    $user = Divebans::getInstance()->getInfoByIPCookie($addr);
    $cookieUser = Divebans::getInstance()->getUserCookie(Divebans::getCookieName());

    if ( !$user || $user['banid'] != $cookieUser['banid'] )  {
        //var_dump($cookieUser);
        // Динамический Айпи
        Divebans::getInstance()->updateByCookie( $cookieUser['ipcookie'], $cookieUser['banid']);
    }

}
?>
<!DOCTYPE>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Cstrike MOTD</title>

</head>
<body style="backround-color:white">
   Motd
</body>
</html>