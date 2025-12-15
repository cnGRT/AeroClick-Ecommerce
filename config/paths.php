<?php
// config/paths.php - 完全动态路径检测

// 方法1: 通过脚本路径自动检测
$script_dir = dirname($_SERVER['SCRIPT_NAME']);
$document_root = rtrim($_SERVER['DOCUMENT_ROOT'], '/');

// 计算项目基础路径
$project_path = str_replace($document_root, '', __DIR__);
$base_url = dirname($project_path);

// 清理路径
$base_url = str_replace('\\', '/', $base_url);
$base_url = rtrim($base_url, '/');

// 如果路径为空，使用根目录
if (empty($base_url) || $base_url === '/config') {
    $base_url = '';
}

// 方法2: 通过当前请求URI检测（备用）
if (empty($base_url)) {
    $request_uri = $_SERVER['REQUEST_URI'] ?? '';
    $script_name = $_SERVER['SCRIPT_NAME'] ?? '';
    
    // 从请求URI中提取项目路径
    if (preg_match('#^(/.+?)/#', $request_uri, $matches)) {
        $base_url = $matches[1];
    } elseif (preg_match('#^(/.+?)/#', $script_name, $matches)) {
        $base_url = $matches[1];
    }
}

// 最终回退
if (empty($base_url)) {
    $base_url = '';
}

// 定义常量
define('BASE_URL', $base_url);
define('BASE_PATH', realpath(__DIR__ . '/..'));

// 常用路径
define('ASSETS_URL', BASE_URL . '/assets');
define('UPLOADS_URL', BASE_URL . '/uploads');
define('ADMIN_URL', BASE_URL . '/admin');
define('PRODUCTS_URL', BASE_URL . '/products');
define('CART_URL', BASE_URL . '/cart');
define('AUTH_URL', BASE_URL . '/auth');
define('USER_URL', BASE_URL . '/user');

// 调试信息（开发时启用，生产时注释掉）
error_log("=== PATH CONFIGURATION ===");
error_log("BASE_URL: " . BASE_URL);
error_log("DOCUMENT_ROOT: " . $document_root);
error_log("SCRIPT_NAME: " . ($_SERVER['SCRIPT_NAME'] ?? ''));
error_log("REQUEST_URI: " . ($_SERVER['REQUEST_URI'] ?? ''));
?>