<?php
// includes/functions.php

// 注意：不再在这里启动会话，由 init.php 统一处理
// 确保会话已启动
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
/**
 * 安全的数据库查询执行器
 */
function executeQuery($sql, $params = []) {
    global $pdo;
    
    if (!$pdo) {
        throw new Exception('Database connection unavailable');
    }
    
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    } catch (PDOException $e) {
        error_log("SQL Error: " . $e->getMessage() . " [Query: $sql]");
        throw new Exception('Database query failed');
    }
}

/**
 * 检查用户是否登录
 */
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

/**
 * 增强的管理员检查
 */
function is_admin() {
    if (!is_logged_in()) {
        return false;
    }
    
    global $pdo;
    if (!$pdo) {
        return false;
    }
    
    try {
        $stmt = $pdo->prepare("SELECT role, status FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return ($user && $user['role'] === 'admin' && $user['status'] === 'active');
    } catch (PDOException $e) {
        error_log("Admin check error: " . $e->getMessage());
        return false;
    }
}

/**
 * 安全的获取用户信息
 */
function get_logged_in_user() {
    if (!is_logged_in()) {
        return null;
    }
    
    global $pdo;
    if (!$pdo) {
        return null;
    }
    
    try {
        $stmt = $pdo->prepare("SELECT id, username, email, role, status FROM users WHERE id = ? AND status = 'active'");
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("User fetch error: " . $e->getMessage());
        return null;
    }
}

/**
 * 重定向函数
 */
function redirect($url) {
    // 确保使用绝对路径
    if (strpos($url, '/') !== 0) {
        $url = BASE_URL . '/' . ltrim($url, '/');
    }
    header("Location: " . $url);
    exit();
}

/**
 * HTML 转义
 */
function esc($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * URL 参数转义
 */
function esc_url($url) {
    return htmlspecialchars($url ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * 属性转义（用于 HTML 属性）
 */
function esc_attr($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * JavaScript 转义
 */
function esc_js($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * 验证和清理整数
 */
function validate_int($value, $min = null, $max = null) {
    $value = filter_var($value, FILTER_VALIDATE_INT);
    if ($value === false) {
        return null;
    }
    if ($min !== null && $value < $min) {
        return null;
    }
    if ($max !== null && $value > $max) {
        return null;
    }
    return $value;
}

/**
 * 验证和清理字符串
 */
function validate_string($value, $max_length = 255) {
    $value = trim($value ?? '');
    if (mb_strlen($value) > $max_length) {
        $value = mb_substr($value, 0, $max_length);
    }
    return $value;
}

/**
 * 生成 CSRF 令牌
 */
function csrf_token() {
    // 确保会话已启动
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // 如果不存在token，生成一个
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * 验证 CSRF 令牌
 */
function validate_csrf($token) {
    // 确保会话已启动
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
?>
<?php
// 在 includes/functions.php 中添加以下函数

/**
 * 安全的图片路径处理
 */
function safe_image_path($image_url, $default = 'default-product.png') {
    // 只允许字母、数字、连字符、下划线和点
    $safe_filename = preg_replace('/[^a-zA-Z0-9._-]/', '', basename($image_url ?? ''));
    
    // 如果文件名为空或无效，使用默认图片
    if (empty($safe_filename) || !preg_match('/^[a-zA-Z0-9._-]+\.[a-zA-Z0-9]{2,4}$/', $safe_filename)) {
        $safe_filename = $default;
    }
    
    return $safe_filename;
}

/**
 * 验证图片文件类型
 */
function is_valid_image_type($filename) {
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    return in_array($extension, $allowed_extensions);
}

/**
 * 安全的图片URL生成
 */
function get_product_image_url($image_url, $default = 'default-product.png') {
    $safe_filename = safe_image_path($image_url, $default);
    
    // 确保是有效的图片类型
    if (!is_valid_image_type($safe_filename)) {
        $safe_filename = $default;
    }
    
    return ASSETS_URL . '/images/products/' . $safe_filename;
}
?>