<?php
// config/database.php

// 错误报告设置
error_reporting(E_ALL);
ini_set('display_errors', 0); // 生产环境设为 0
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/php_errors.log');

// 确保日志目录存在
$log_dir = __DIR__ . '/../logs';
if (!is_dir($log_dir)) {
    mkdir($log_dir, 0755, true);
}

$host = 'sql301.infinityfree.com';
$dbname = 'if0_38341067_aeroclick';
$username = 'if0_38341067';
$password = 'Grtnb137';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    
    // 测试连接
    $pdo->query("SELECT 1");
    
} catch (PDOException $e) {
    // 安全地记录错误，不暴露详细信息
    error_log("Database connection failed: " . $e->getMessage());
    
    // 创建安全的错误页面或返回 null
    $pdo = null;
    
    // 如果是管理员且是开发环境，显示详细错误
    if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
        // 检查是否是本地环境
        $is_local = in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1']) || 
                   (isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], 'localhost') !== false);
        
        if ($is_local) {
            die("Database connection error (Development): " . $e->getMessage());
        }
    }
    
    // 生产环境显示通用错误
    die("System temporarily unavailable. Please try again later.");
}
?>