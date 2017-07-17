<?php  
/**
* 
*/
class MyRedis
{
	private static $_instance;
	private $_redis;

	public static function getInstance() {
		if (! static::$_instance instanceof static) {
			static::$_instance = new static();
		}
		return static::$_instance;
	}

	private function __clone() {

	}

	private function __construct()
	{
		$this->_redis = new Redis();
		$this->_redis->connect('127.0.0.1', '6379');
		$this->_redis->select(0);
	}

	public function set($key='', $value='', $timeout=0) {
		$result = $this->_redis->set($key, $value);
		$this->_redis->expire($key, $timeout);
		return $result;
	}

	public function get($key='') {
		$result = $this->_redis->get($key);
		return $result;
	}

	public function remove($key='') {
		// var_dump($key);
		// die;
		$result = $this->_redis->del($key);
		return $result;
	}
}
?>