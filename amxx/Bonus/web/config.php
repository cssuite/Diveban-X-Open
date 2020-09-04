<?php


class Divebans 
{
	/*Settings start */
    private $table = 'divebans';

    /* Укажите данные для подключения к базе данных */
    private $db_host = '';
    private $db_user = '';
    private $db_pass = '';
	private $db_db = '';
	
	static $instance = null;

	private $mysqli;

	static function getInstance() {
		if ( self::$instance === null )
			self::$instance = new self();

		return self::$instance;
	}

	static function getCookieName() {
		return 'DivebanX';
	}

	public function __construct()
	{
		$this->mysqli = new mysqli($this->db_host, $this->db_user, $this->db_pass);
		mysqli_set_charset($this->mysqli, 'utf8');
		return (@$this->mysqli and $this->mysqli->select_db($this->db_db));
	}

	public function getInfoByIPCookie( $ipAddr ) {
		$query = $this->mysqli->query("SELECT * FROM `".$this->table."` WHERE `ipcookie`='$ipAddr' ORDER BY `banid` DESC LIMIT 1" );
		return mysqli_fetch_assoc($query);
	}

	public function setUserCookie( $cookieName, $cookie, $time = 315360000) {
	
		if ( is_array($cookie) )
			$cookie = serialize($cookie);

		return setcookie($cookieName, $cookie, time()+ $time, '/');
	}

	public function getUserCookie( $cookie ) {

		if ( !isset($_COOKIE[$cookie]) )
			return false;

		return unserialize($_COOKIE[$cookie], ["allowed_classes" => false]);
	}

	public function updateByCookie( $ipCookie, $id) {
		return $this->mysqli->query("UPDATE `".$this->table."` SET `ipcookie` = '$ipCookie' WHERE `banid` = ".intval($id));
	}
}

?>
