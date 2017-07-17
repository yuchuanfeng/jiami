<?php  
/**
* 
*/
class Model
{
	protected $_dao;
	public function __construct() {
		// 初始化DAO
		$this->_initDAO();
	}

	private function _initDAO() {
		// 获取数据库服务器配置信息
		$config = array(
			'host' 	=> $GLOBALS['commonConfig']['db_host'],
			'port'	=> $GLOBALS['commonConfig']['db_port'],
			'username'	=> $GLOBALS['commonConfig']['db_user'],
			'password'	=> $GLOBALS['commonConfig']['db_password'],
			'dbname'		=> $GLOBALS['commonConfig']['db_dbname'],
			);
		// 当前模型对象的_dao属性上，模型方法中就可以通过$this->_dao的方式
		// 获取当前的DAO对象
		// $this->_dao = mysql::getInstance($config);
		$this->_dao = PDODB::getInstance($config);
	}

	public function checkToken($token='11') {
		$redis = MyRedis::getInstance();
		$getResult = $redis->get($token);

		// $commonToken = 'a501b9cf4f7a010521c50d1f79cd762d';
		// if ($token == $commonToken) {
		// 	return true;
		// }else
		if ($getResult == false) {
			$result = array();
			$result['code'] = 0;
			$result['status'] = 'token失败，请重新登录！';
			// $result['token'] = $token;
			// $result['id'] = session_id();
			echo json_encode($result);
			die;
		}
		else{
			return true;
		}
	}

	public function currentUserInfo($token) {
		$token = $this->_dao->escapeStringNoQMarks($token);
		$redis = MyRedis::getInstance();
		$userId = $redis->get($token);
		$userInfo = $redis->get($userId.'info');
		 // json_decode( json_encode( $obj ), true )
		$userInfo = json_decode($userInfo, true);
		return $userInfo;
	}

	public function currentUserId($token) {
		$token = $this->_dao->escapeStringNoQMarks($token);
		$redis = MyRedis::getInstance();
		$userId = $redis->get($token);
		return $userId;
	}

	public function userInfoWithUserId($userId='0') {
		$userId = $this->_dao->escapeString($userId);
		$sql = "SELECT * FROM user WHERE userId=$userId";
		return $this->_dao->fetchRow($sql);
	}

	public function isAdmin($token) {

		$userInfo = $this->currentUserInfo($token);
		// error_log(json_encode($userInfo));
		return $userInfo['isAdmin'];
	}
}
?>