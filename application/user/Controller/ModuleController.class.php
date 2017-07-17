<?php 
/**
* 
*/
class ModuleController extends Controller
{
	
	function __construct()
	{
		parent::__construct();

		// $this->_isLogin();
	}

	

	// protected function _isLogin() {
	// 	$noCheck = array(
	// 		'User' => array('login'),
	// 		);
	// 	if (isset($noCheck[CONTROLLER]) && in_array(ACTION, $noCheck[CONTROLLER])) {
	// 		return;
	// 	}

		// if (!isset($_SESSION['user'])) {
		// 	$result['code'] = 0;
		// 	$result['status'] = '请重新登录！！';
		// 	echo json_encode($result);
		// 	die;
		// }
	// }


}
 ?>