<?php  
// 项目通用配置文件
// 项目 任何平台公共配置，例如数据库服务配置

// 直接返回该配置数组
// return 语法，在require载入该文件时，该数组作为require的返回值来使用！
return array(
	'db_host' => 'localhost',// 数据库服务器主机
	'db_port' => '3306',// 数据库服务器的端口
	'db_user'	=> 'root', // 数据库服务器的用户
	'db_password' => '123', // 数据库服务器密码
	'db_dbname' => 'jiami', // 默认的数据库
	);
?>