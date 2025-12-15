<?php
// auth/process_login.php
require_once __DIR__ . '/../includes/init.php';

error_log("=== LOGIN PROCESS START ===");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    error_log("Not POST request");
    $_SESSION['error'] = "Invalid request method";
    header("Location: " . AUTH_URL . "/login.php");
    exit();
}

$username = $_POST['username'] ?? '';

error_log("Login attempt: $username");

if (empty($username)) {
    error_log("Empty username");
    $_SESSION['error'] = "Please enter email or phone number";
    header("Location: " . AUTH_URL . "/login.php");
    exit();
}

// 直接连接 WordPress 数据库
$db_host = 'sql301.infinityfree.com';
$db_username = 'if0_38341067';
$db_password = 'Grtnb137';
$db_name = 'if0_38341067_wp221';
$table_name = 'wpri_fc_subscribers';

try {
    $wp_pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_username, $db_password);
    $wp_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $wp_pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    error_log("WordPress database connected successfully");

    // 查找用户在 WordPress 订阅者表中
    $stmt = $wp_pdo->prepare("SELECT id, first_name, last_name, email, phone, status FROM $table_name WHERE email = ? OR phone = ?");
    $stmt->execute([$username, $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        error_log("User found: " . $user['email'] . " - Status: " . $user['status']);
        
        // 检查用户状态
        if ($user['status'] !== 'subscribed') {
            error_log("User not subscribed - status: " . $user['status']);
            $_SESSION['error'] = "Your account is not active. Please contact support.";
            header("Location: " . AUTH_URL . "/login.php");
            exit();
        }
        
        // 无密码验证 - 直接登录成功
        // 检查是否是管理员（只有 1459321941@qq.com 是管理员）
        $is_admin = ($user['email'] === '1459321941@qq.com');
        
        // 登录成功 - 设置会话变量
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['email'];
        $_SESSION['first_name'] = $user['first_name'];
        $_SESSION['last_name'] = $user['last_name'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['phone'] = $user['phone'];
        $_SESSION['role'] = $is_admin ? 'admin' : 'user';
        
        error_log("Login SUCCESS - User ID: " . $user['id'] . ", Role: " . $_SESSION['role']);
        
        if ($is_admin) {
            $_SESSION['success'] = "Welcome back, Admin " . ($user['first_name'] ?? $user['email']) . "!";
        } else {
            $_SESSION['success'] = "Welcome back, " . ($user['first_name'] ?? $user['email']) . "!";
        }
        
        // 重定向到首页
        header("Location: " . BASE_URL . "/");
        exit();
        
    } else {
        error_log("User not found: $username");
        $_SESSION['error'] = "No account found with this email or phone number";
    }
} catch (Exception $e) {
    error_log("Database error: " . $e->getMessage());
    $_SESSION['error'] = "System error. Please try again later. Error: " . $e->getMessage();
}

// 登录失败
header("Location: " . AUTH_URL . "/login.php");
exit();
?>