<?php 
/**
* 
*/
class PDODB
{
	private function __clone() {

	}

	private static $_instance;

	private $_host;
	private $_port;
	private $_user;
	private $_password;
	private $_dbname;

	private $_dsn;
	private $_options;
	private $_pdo;

	public static function getInstance($config=array()) {
		if (! static::$_instance instanceof static) {
			static::$_instance = new static($config);
		}
		return static::$_instance;
	}

	private function __construct($config=array())
	{
		$this->_initServer($config);
		$this->_newPDO();
	}

	private function _initServer($config) {
		$this->_host = isset($config['host']) ? $config['host'] : 'localhost';
		$this->_port = isset($config['port']) ? $config['port'] : '3306';
		$this->_user = isset($config['username']) ? $config['username'] : '';

		$this->_password = isset($config['password']) ? $config['password'] : '';
		$this->_dbname = isset($config['dbname']) ? $config['dbname'] : 'jiami';
	}

	private function _newPDO() {
		$this->_dsn = "mysql:host=$this->_host;port=$this->_port;dbname=$this->_dbname";
		// $this->_options = array(
		// 	PDO::MYSQL_ATTR_INIT_COMMAND => "";
		// 	);
		$this->_pdo = new PDO($this->_dsn, $this->_user, $this->_password, NULL);
	}

	public function fetchAll($sql=''){
		$result = $this->query($sql);
		$rows = $result->fetchAll(PDO::FETCH_ASSOC);
		$result->closeCursor();
		return $rows;
	}

	public function fetchRow($sql='') {
		$result = $this->query($sql);
		$row = $result->fetch(PDO::FETCH_ASSOC);
		$result->closeCursor();
		return $row;
	}

	public function fetchOne($sql='') {
		$result = $this->query($sql);
		$string = $result->fetchColumn();
		$result->closeCursor();
		return $string;
	}


	public function query($sql='') {
		if (strtolower(substr($sql, 0, 6)) == 'select' || strtolower(substr($sql, 0, 4)) == 'show' || strtolower(substr($sql, 0, 4)) == 'desc') {
			$result = $this->_pdo->query($sql);
		}else {
			$result = $this->_pdo->exec($sql);
		}

		if ($result == false) {
			$error = $this->_pdo->errorInfo();
			$error_info = 'SQL执行失败:<br>';
			$error_info .= '错误的SQL为：' . $sql . '<br>';
			$error_info .= '错误消息为：' . json_encode($error) . '<br>';
			trigger_error($error_info, E_USER_ERROR);
			// die;
			return $result;
		}else {
			return $result;
		}
	}

	public function update($sql='') {
		$pdoStatement = $this->_pdo->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
		$execRet = $pdoStatement->execute();
		return $execRet;
  }


	public function escapeString($str='') {
		$string = $this->_pdo->quote($str);
		// $string = substr($string, 1, -1);
		return $string;
	}

	public function escapeStringNoQMarks($str='') {
		$string = $this->_pdo->quote($str);
		$string = substr($string, 1, -1);
		return $string;
	}
}
?>