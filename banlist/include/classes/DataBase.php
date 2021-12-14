<?php
/*
* DataBase.php
*
* @version Superban CMS 1.6
* @author Oleksandr Kornienko
* @copyright (C)2013 Superban.net. Все права защищены.
* @contacts Skype: magoga25 ICQ: 624338780
*/

if (!defined("GUARD")) { header('Content-type: text/html; charset=utf-8'); die('Доступ запрещен!'); } // Защита файла от прямого вызова.

class DataBase 
{
    /** @var mysqli */
    public $mysqli;

    public static $instance = null;

    public function __construct($host = '', $user = '', $password = '', $db = '', $charset = 'utf8') {

        if ($host == '') {
            $host = Configuration::$db['db_serv'];
            $user = Configuration::$db['db_user'];
            $password = Configuration::$db['db_pass'];
            $db = Configuration::$db['db_name'];
        }

        $this->mysqli = mysqli_connect($host, $user, $password, $db);

        if ($this->mysqli === false) {
            die(mysqli_connect_error());
        }

        $this->mysqli->set_charset($charset);
        return true;
    }

    public static function getInstance() : self {
        if (static::$instance === null) {
            static::$instance = new DataBase();
        }

        return static::$instance;
    }

    public function query($query) {
        return $this->mysqli->query($query);
    }

    /** Return table name with prefix
     * @param string $table
     * @return string
     */
    public function prepareTableName(string $table) : string {
        return $table;
    }

    /** Prepare array for query
     * @param array $array
     * @param bool $insert
     * @param string $condition
     * @return string
     */
    public function prepareArray(array $array, bool $insert = false, string $condition = 'AND') : string {
        foreach( $array as $k=>$v) {
            if ($insert) {
                if ($condition == 'AND') $condition = "'";
                $value = $this->mysqli->escape_string($v);
                $array[$k] = $condition.$value.$condition;
                continue;
            }

            $operator = is_array($v) && in_array( $v[0],  [ '=', '>', '<']);

            if ($operator)      $array[$k] =  " `$k` $operator '".$this->mysqli->escape_string($v[1])."' ";
            else                $array[$k] =  " `$k` = '".$this->mysqli->escape_string($v)."' ";
        }

        $prepare = $insert ? implode( ",", $array) : implode($condition, $array);
        return $prepare ?: "1";
    }

    public function prepareLimit($limit) {

        if ( is_array($limit)) {
            return 'LIMIT '.$limit[0].','.$limit[1];
        }

        return $limit ? 'LIMIT '.$limit : '';
    }

    public function delete(string $table, array $where) {
        $table = $this->prepareTableName($table);
        $where = $this->prepareArray($where);

        return $this->query("DELETE FROM `$table` WHERE $where");
    }

    public function insertRow( string $table, array $insert) {
        $table = $this->prepareTableName($table);
        $keys = $this->prepareArray(array_keys($insert), true, '`');
        $insert = $this->prepareArray($insert, true);

        return $this->query("INSERT INTO `$table` ($keys) VALUES ($insert)");
    }

    public function updateRow( string $table, array $set, array $where = []) {
        $table = $this->prepareTableName($table);
        $set = $this->prepareArray($set, false, ',');
        $where = $this->prepareArray($where);

        return $this->query("UPDATE `$table` SET $set WHERE ( $where )");
    }

    /** Fetch all rows from table with conditions
     * @param string $table
     * @param array $where
     * @param array $order
     * @param int $limit
     * @param bool $or
     * @return array
     */
    public function fetchAll(string $table, array $where = [], array $order = [], $limit = 0, bool $or = false) {
        $table = $this->prepareTableName($table);
        $where = $this->prepareArray($where, false, $or ? 'OR' : 'AND');

        if ( $order && $order['name'] ) {
            $order['type'] = $order['type'] ?: 'ASC';
            $order = "ORDER BY $order[name] $order[type]";
        } else {
            $order = '';
        }

        $limit = $this->prepareLimit($limit);

        $result = $this->query("SELECT * FROM `$table` WHERE ( $where ) $order $limit");
        if (!$result) return array();

        return $this->fetchAssoc($result);
    }

    /**
     * @param $table
     * @param array $where
     * @return mixed
     */
    public function countRow($table, array $where = array())
    {
        $table = $this->prepareTableName($table);
        $where = $this->prepareArray($where);

        $query = $this->query("SELECT COUNT(*) FROM `$table` WHERE $where");
        $count = \mysqli_fetch_array($query);
        return $count[0];
    }

    /**
     * @param string $table
     * @param array $where
     * @param array $order
     * @param int $limit
     * @param $
     * @param bool $or
     * @return array|mixed
     */
    public function fetchOne(string $table, array $where = [], array $order = [], $limit = 0, bool $or = false) {
        $data = $this->fetchAll($table, $where, $order, $limit, $or);
        return isset($data[0]) && $data[0] ? $data[0] : array();
    }

    public function fetchAssoc($result) {
        $row = array();
        while ( $data = \mysqli_fetch_assoc($result) ) $row[] = $data;
        return $row;
    }

    public function escape($var) {
        return $this->mysqli->escape_string($var);
    }
}
?>