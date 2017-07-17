<?php  
/**
* 
*/
class mysql
{
	// 数据库服务器配置信息
	private $_config = array();
	// 数据库服务器的连接
	private $_link = null;

	// 存储当前实例化完成对象
	private static $_instance;

	public static function getInstance($config) {
		// 判断是否已经实例化
		if (! self::$_instance instanceof self) {
			// 没有实例化过，实例化，并存储
			self::$_instance = new self($config);
		}
		// 返回
		return self::$_instance;
	}

	private function __construct($config = array()) {
		// 初始化数据库服务器信息
		$this->_initConfig($config);
		// 连接数据库服务器
		$this->_connect();
		// 选择默认的数据库
		$this->_initDefaultDB();
	}

	private function _initConfig($config) {
		// 判断用户给定的配置项，是否存在，如果不存在，使用系统默认的配置项
		// 将当前服务器配置信息，存储与对象的某个属性中！_config;
		// 主机，端口，用户，密码，字符集，默认数据库
		$this->_config['host'] = isset($config['host']) ? $config['host'] : 'localhost';
		$this->_config['port'] = isset($config['port']) ? $config['port'] : '3306';
		$this->_config['username'] = isset($config['username']) ? $config['username'] : 'root';
		$this->_config['password'] = isset($config['password']) ? $config['password'] : '';
		$this->_config['dbname'] = isset($config['dbname']) ? $config['dbname'] : '';
	}

	private function _connect() {
		$link = mysql_connect($this->_config['host'] . ':' . $this->_config['port'], $this->_config['username'], $this->_config['password']);
		// 判断连接结果
		if (!$link) {
			// 连接失败
			trigger_error('数据库服务器连接失败，请确保服务器配置正确', E_USER_ERROR);
		}
		// 存储到属性上
		$this->_link = $link;
	}

	private function _initDefaultDB() {
		// 判断用户是否指定了默认数据库
		if ($this->_config['dbname'] === '') {
			// 用户没有指定，不需要选择默认数据库
			return;
		}
		$sql = "USE `{$this->_config['dbname']}`";
		return $this->query($sql);
	}

	private function __clone() {

	}

	public function query($sql) {
		$result = mysql_query($sql, $this->_link);
		// 判断执行结果
		if ($result == false) {
			//说明执行失败，将失败的原因，作为用户错误信息报告
			$error_info = 'SQL执行失败:<br>';
			$error_info .= '错误的SQL为：' . $sql . '<br>';
			$error_info .= '错误码为：' . mysql_errno($this->_link) . '<br>';
			$error_info .= '错误消息为：' . mysql_error($this->_link) . '<br>';

			// 触发错误
			trigger_error($error_info, E_USER_ERROR);
			return false;
		}
		// 返回执行结果即可
		return $result;
	}

	public function fetchAll($sql) {
		// 执行
		$result = $this->query($sql);
		// 遍历结果集，获取全部记录
		$rows = array();
		while($row = mysql_fetch_assoc($result)) {
			$rows[] = $row;
		}
		// 释放该结果集
		mysql_free_result($result);
		// 返回结果
		return $rows;
	}

	public function fetchRow($sql) {
		// 执行
		$result = $this->query($sql);
		// fetch一次，获取一条记录
		// 如果结果集中，没有记录，则fetch的结果为false。如果有记录，则返回数组！
		$row = mysql_fetch_assoc($result);
		// 释放结果集资源
		mysql_free_result($result);
		// 返回记录
		return $row;
	}

	public function fetchOne($sql) {
		// 执行
		$result = $this->query($sql);
		// 获取第一条记录
		$row =mysql_fetch_row ($result);
		// 是否存在
		if ($row) {
			// 则结果为该记录的第一个字段
			$data = $row[0];
		} else {
			$data = false;
		}
		// 释放结果集
		mysql_free_result($result);
		// 返回数据
		return $data;
	}
}
?>