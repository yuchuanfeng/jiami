<?php 
/**
* 
*/
class UserController extends ModuleController
{
	function loginAction() {
		// error_log(json_encode($_POST), 0);
		// 判断参数是否齐
		if (!isset($_POST['userName']) || !isset($_POST['pwd'])) {
			$result = array(
				'code' => 0,
				'status' => '缺少参数！'
				);
		}else {
			$userName = $_POST['userName'];
			$pwd = $_POST['pwd'];
			$userModel = Factory::M('User');
			$values = $userModel->checkLogin($userName, $pwd);
			if ($values['userId']) { // 判断数据库是否有此用户
				array_splice($values, 2, 1);
				$token = $userModel->generateToken($values['userId']);
				$userModel->saveUserInfoToRedis($values['userId']);
				$values['token'] = $token;
				$result = array(
					'code' => 1,
					'status' => 'OK',
					'userInfo' => $values
					);
			}else {
				$result = array(
					'code' => 0,
					'status' => 'Error:登录失败！'
					);	
			}	
		}
		echo json_encode($result);
	}

	function addUserAction() {
		$userModel = Factory::M('User');
		if (!isset($_POST['userName']) || !isset($_POST['pwd'])|| !isset($_POST['token'])) {
			$result = array(
				'code' => 0,
				'status' => '缺少参数！'
				);
		}else if (!$userModel->isAdmin($_POST['token'])) {
			$result = array(
				'code' => 0,
				'status' => '没有权限！'
				);
		}else if (!$userModel->checkToken($_POST['token'])) {
			$result = array(
				'code' => 0,
				'status' => 'token无效！'
				);
		}else{
			$userName = $_POST['userName'];
			$pwd = $_POST['pwd'];
			$level = isset( $_POST['limitLevel']) ?  $_POST['limitLevel'] : 0;
			$values = $userModel->addUser($userName, $pwd, $level);
			if ($values) {
				$values = $userModel->userInfoUserName($userName);
				array_splice($values, 2, 1);
				$result = array(
					'code' => 1,
					'status' => 'OK',
					'userInfo' => $values
					);
			}else {
				$result = array(
					'code' => 0,
					'status' => 'Error:添加用户失败！'
					);	
			}	
		}
		echo json_encode($result);
	}

	function removeUserAction() {
		$userModel = Factory::M('User');
		if (!isset($_POST['userId'])|| !isset($_POST['token'])) {
			$result = array(
				'code' => 0,
				'status' => '缺少参数！'
				);
		}else if (!$userModel->isAdmin($_POST['token'])) {
			$result = array(
				'code' => 0,
				'status' => '没有权限！'
				);
		}else if (!$userModel->checkToken($_POST['token'])) {
			$result = array(
				'code' => 0,
				'status' => 'token无效！'
				);
		}else{
			$userId = $_POST['userId'];
			$userInfo = $userModel->userInfoWithUserId($userId);
			if ($userInfo['isAdmin']) {
				$result = array(
				'code' => 0,
				'status' => '没有权限！'
				);
			}else if($userInfo){
				$values = $userModel->removeUser($userId);
				if ($values) {
					$result = array(
						'code' => 1,
						'status' => 'OK',
						);
				}else {
					$result = array(
						'code' => 0,
						'status' => 'Error:删除用户失败！'
						);	
				}
			}else {
				$result = array(
					'code' => 0,
					'status' => 'Error:删除用户失败！'
					);
			}
				
		}
		echo json_encode($result);
	}

	public function logoutAction() {
		$userModel = Factory::M('User');
		if (!isset($_POST['token'])) {
			$result = array(
				'code' => 0,
				'status' => '缺少参数！'
				);
		}else if (!$userModel->checkToken($_POST['token'])) {
			$result = array(
				'code' => 0,
				'status' => 'token无效！'
				);
		}else{
			$values = $userModel->logout($_POST['token']);
			if ($values) {
				$result = array(
					'code' => 1,
					'status' => 'OK',
					);
			}else {
				$result = array(
					'code' => 0,
					'status' => 'Error:登出失败！'
					);	
			}	
		}
		echo json_encode($result);
	}


}
?>