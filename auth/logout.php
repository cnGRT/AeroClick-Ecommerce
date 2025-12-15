<?php
require_once __DIR__ . '/../includes/init.php';

// 记录登出日志
if (is_logged_in()) {
    error_log("User logout: " . ($_SESSION['username'] ?? 'unknown'));
}

// 清除所有会话数据
$_SESSION = [];

// 删除会话 cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 销毁会话
session_destroy();

// 设置成功消息
session_start(); // 重新开始会话来存储消息
$_SESSION['success'] = "You have been logged out successfully.";

// 重定向到首页
redirect(BASE_URL . '/');
?>