<?php
// includes/admin_auth.php - 管理员认证检查

/**
 * 严格的管理员权限检查
 */
function require_admin_auth() {
    if (!is_logged_in()) {
        $_SESSION['error'] = 'Please login to access admin area.';
        redirect(AUTH_URL . '/login.php');
    }
    
    if (!is_admin()) {
        $_SESSION['error'] = 'Access denied. Admin privileges required.';
        redirect(BASE_URL . '/');
    }
    
    // 额外安全检查：验证用户状态（兼容没有status字段的情况）
    global $pdo;
    try {
        // 检查表结构是否有status字段
        $stmt = $pdo->prepare("SHOW COLUMNS FROM users LIKE 'status'");
        $stmt->execute();
        $has_status = $stmt->fetch();
        
        if ($has_status) {
            $stmt = $pdo->prepare("SELECT status FROM users WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $user_status = $stmt->fetch(PDO::FETCH_COLUMN);
            
            if ($user_status !== 'active') {
                session_destroy();
                redirect(AUTH_URL . '/login.php');
            }
        }
    } catch (Exception $e) {
        error_log("Admin auth check error: " . $e->getMessage());
        // 不阻止访问，只记录错误
    }
}

/**
 * 记录管理员操作日志
 */
function log_admin_action($action, $details = '') {
    global $pdo;
    try {
        // 检查admin_logs表是否存在
        $stmt = $pdo->prepare("SHOW TABLES LIKE 'admin_logs'");
        $stmt->execute();
        $table_exists = $stmt->fetch();
        
        if ($table_exists) {
            $stmt = $pdo->prepare("INSERT INTO admin_logs (user_id, action, details, ip_address) VALUES (?, ?, ?, ?)");
            $stmt->execute([
                $_SESSION['user_id'],
                $action,
                $details,
                $_SERVER['REMOTE_ADDR'] ?? 'unknown'
            ]);
        }
    } catch (Exception $e) {
        error_log("Admin log error: " . $e->getMessage());
    }
}
?>