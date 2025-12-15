<?php
require_once __DIR__ . '/includes/init.php';

// 模拟登录成功
$_SESSION['user_id'] = 1;
$_SESSION['username'] = 'admin';
$_SESSION['role'] = 'admin';

echo "Session set: " . print_r($_SESSION, true) . "<br>";
echo "Redirecting to home...<br>";

redirect(BASE_URL . '/');
?>