<?php 
/**
* 
*/
class UserModel extends Model
{
	public function checkLogin($userName='', $pwd='') {
		$userName = $this->_dao->escapeString($userName);
		$pwd = $this->_dao->escapeString(md5($pwd));
		$sql = "SELECT * FROM user WHERE userName=$userName AND userPass=$pwd";
		$values = $this->_dao->fetchRow($sql);
		return $values;
	}

	public function addUser($userName='', $pwd='', $level=0) {
		$userName = $this->_dao->escapeString($userName);
		$pwd = $this->_dao->escapeString(md5($pwd));
		$level = $this->_dao->escapeString($level);
		$userId = $this->_generateuserId();
		$time = date('Y-m-d H:i:s'); // 当前时间
		// $time = $this->_dao->escapeString($time);
		$sql = "INSERT INTO user VALUES($userId, $userName, $pwd, '$time', false, $level)";
		return $this->_dao->query($sql);
	}

	public function removeUser($userId='') {
		$userId = $this->_dao->escapeString($userId);
		$sql = "delete from user where userId=$userId";
		return $this->_dao->query($sql);
	}


	public function userInfoUserName($userName='') {
		$userName = $this->_dao->escapeString($userName);
		$sql = "SELECT * FROM user WHERE userName=$userName";
		return $this->_dao->fetchRow($sql);
	}



	public function logout($token) {
		error_log("logout======");
		// session_regenerate_id();
		// $values = session_destroy();
		$token = $this->_dao->escapeStringNoQMarks($token);
		$redis = MyRedis::getInstance();
		$result = $redis->remove($token);
		return $result;
	}

	public function generateToken($userId) {
		$userId = $this->_dao->escapeStringNoQMarks($userId);
		$token =  uniqid($userId, true);
		$token = md5($token);
		$redis = MyRedis::getInstance();
		$result = $redis->get($userId);
		if ($result) {
			$redis->remove($result);
		}
		$result = $redis->set($token, $userId, TIME_OUT);
		$result = $redis->set($userId, $token, TIME_OUT);
		error_log('login_token:'. $token);
		return $token;
	}

	public function saveUserInfoToRedis($userId) {
		$userId = $this->_dao->escapeStringNoQMarks($userId);
		$redis = MyRedis::getInstance();
		$userInfo = $this->userInfoWithUserId($userId);
		$result = $redis->set($userId.'info', json_encode($userInfo), TIME_OUT);
		return $result;
	}

	

	private function _generateuserId() {
		$sql = "SELECT userId from user";
		$userIds = $this->_dao->fetchAll($sql);
		$userId = rand(1000, 9999);
		while (in_array(array('userId' => $userId), $userIds)) {
			$userId = rand(1000, 9999);
		}
		return $userId;
	}
}
 ?>