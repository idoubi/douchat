<?php

/**
 * 应用入口文件
 */
if (version_compare(PHP_VERSION,'5.4.0','<')) {				// 检测环境
	die('require PHP > 5.4.0 !');
}

define('THINK_PATH', './ThinkPHP/');						// 定义thinkphp框架路径
define('APP_PATH', './App/');								// 定义应用目录
define('RUNTIME_PATH', './Runtime/');						// 定义缓存目录
define('ADDON_PATH', './Addons/'); 							// 定义插件目录
define('UPLOAD_PATH', './Uploads/');						// 上传目录
define('SITE_PATH', dirname(__FILE__));						// 定义网站物理路径
define('SITE_URL', str_replace('index.php', '', 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME']));
define('APP_DEBUG', true);									// 开启调试模式
define('NOW_TIME', time());									// 定义脚本执行时间

if (!is_file(SITE_PATH.'/Data/install.lock')) {				// 如果框架未安装，则跳转到安装页面
	$_GET['m'] = 'install';
}

$_G = array();				// 声明全局变量

require THINK_PATH . 'ThinkPHP.php';							// 引入ThinkPHP入口文件


?>