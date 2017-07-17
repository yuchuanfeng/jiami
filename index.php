

<?php 

// session_start();
// echo  $_SESSION[session_name()], "<br>";
// $_SESSION['name'] = 'zhang';
// echo session_id(), '<br>';

// session_regenerate_id();
// echo session_id(), '<br>';
// session_start(); 

// echo session_id(), '<br>';

define('TIME_OUT', 86400); // 24 h

define('ROOT_PATH', __DIR__. '/');
define('FRAMEWORK_PATH', ROOT_PATH. 'framework/');
define('APPLICATION_PATH', ROOT_PATH. 'application/');
define('COMMON_PATH', APPLICATION_PATH. 'common/');
define('COMMON_CONFIG_PATH', COMMON_PATH. 'config/');
define('FILE_PATH', ROOT_PATH. 'files/');

$commonConfig = require_once COMMON_CONFIG_PATH. 'config.php';

$framework_class_list['Controller'] = FRAMEWORK_PATH. 'Controller'. '.class.php';
$framework_class_list['Model'] = FRAMEWORK_PATH. 'Model'. '.class.php';
$framework_class_list['mysql'] = FRAMEWORK_PATH. 'mysql'. '.class.php';
$framework_class_list['PDODB'] = FRAMEWORK_PATH. 'PDODB'. '.class.php';
$framework_class_list['MyRedis'] = FRAMEWORK_PATH. 'MyRedis'. '.class.php';
$framework_class_list['Factory'] = FRAMEWORK_PATH. 'Factory'. '.class.php';
$framework_class_list['upload'] = FRAMEWORK_PATH. 'tool/'.'upload'. '.class.php';

function userAutoload($class_name) {
	if (isset($GLOBALS['framework_class_list'][$class_name])) {
			require_once $GLOBALS['framework_class_list'][$class_name];
	}
	elseif (substr($class_name, -5) == 'Model') {
			require_once  MODEL_PATH. $class_name. '.class.php';
	}
	elseif (substr($class_name, -10) == 'Controller') {
			require_once CONTROLLER_PATH. $class_name. '.class.php';
		}	
 }
 spl_autoload_register('userAutoload');


 $default_module = 'user';
 define('MODULE', isset($_GET['module']) ? $_GET['module'] : $default_module);

 $default_class = 'User';
 define('CONTROLLER', isset($_GET['c']) ? $_GET['c'] : $default_class);

 $default_action = 'login';
 define('ACTION', isset($_GET['action']) ? $_GET['action'] : $default_action);


 define('MODULE_PATH', APPLICATION_PATH. MODULE. '/');
 define('CONTROLLER_PATH', MODULE_PATH . 'controller/'); //当前平台控制器路径
 define('MODEL_PATH', MODULE_PATH . 'model/'); // 当前平台模型路径

$controllerName = CONTROLLER. 'Controller';

$controller = new $controllerName();

$actionName = ACTION. 'Action';
$controller->$actionName();





?>