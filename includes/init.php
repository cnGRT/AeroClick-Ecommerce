<?php
// includes/init.php

// 设置错误处理
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

// 定义根路径
define('ROOT_PATH', realpath(dirname(__FILE__) . '/..'));

// 包含配置文件
$config_path = ROOT_PATH . '/config/paths.php';
if (!file_exists($config_path)) {
    die('Configuration error: paths.php not found at ' . $config_path);
}
require_once $config_path;

$db_config_path = ROOT_PATH . '/config/database.php';
if (!file_exists($db_config_path)) {
    die('Configuration error: database.php not found at ' . $db_config_path);
}
require_once $db_config_path;

// 包含函数文件
$functions_path = ROOT_PATH . '/includes/functions.php';
if (!file_exists($functions_path)) {
    die('Configuration error: functions.php not found at ' . $functions_path);
}
require_once $functions_path;

// 启动会话
if (session_status() === PHP_SESSION_NONE) {
    session_start();
    
    // 初始化 CSRF 令牌（如果不存在）
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
}

// 安全检查
if (!defined('BASE_URL')) {
    die('Configuration error: Paths not properly initialized.');
}
?>