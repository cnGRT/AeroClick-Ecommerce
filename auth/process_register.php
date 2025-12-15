<?php
session_start();
echo "<pre>";

// 基本配置
$host = 'localhost';
$dbname = 'aeroclick';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ Database connected successfully\n";
    
    // 获取POST数据
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? ''; // 新增手机号字段
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    echo "📝 Form data received:\n";
    echo "Username: $username\n";
    echo "Email: $email\n";
    echo "Phone: $phone\n";
    echo "Password: " . strlen($password) . " characters\n";
    
    // 验证必填字段
    if (empty($username) || empty($email) || empty($phone) || empty($password) || empty($confirm_password)) {
        die("❌ All fields are required");
    }
    
    // 验证手机号格式 (10-15位数字)
    if (!preg_match('/^[0-9]{10,15}$/', $phone)) {
        die("❌ Please enter a valid phone number (10-15 digits)");
    }
    
    // 验证密码匹配
    if ($password !== $confirm_password) {
        die("❌ Passwords do not match");
    }
    
    // 验证密码长度
    if (strlen($password) < 6) {
        die("❌ Password must be at least 6 characters long");
    }
    
    // 检查用户名是否已存在
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->rowCount() > 0) {
        die("❌ Username already exists");
    }
    
    // 检查邮箱是否已存在
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->rowCount() > 0) {
        die("❌ Email already exists");
    }
    
    // 检查手机号是否已存在
    $stmt = $pdo->prepare("SELECT id FROM users WHERE phone = ?");
    $stmt->execute([$phone]);
    if ($stmt->rowCount() > 0) {
        die("❌ Phone number already exists");
    }
    
    // 创建用户
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (username, email, phone, password, role, created_at) VALUES (?, ?, ?, ?, 'user', NOW())");
    $stmt->execute([$username, $email, $phone, $hashed_password]);
    
    $user_id = $pdo->lastInsertId();
    echo "✅ User registered successfully!\n";
    echo "User ID: $user_id\n";
    echo "Username: $username\n";
    echo "Email: $email\n";
    echo "Phone: $phone\n";
    
    // 自动登录
    $_SESSION['user_id'] = $user_id;
    $_SESSION['username'] = $username;
    $_SESSION['role'] = 'user';
    
    echo "🔐 Auto-login successful\n";
    
    // 重定向到首页
    header("Location: ../index.php?register=success");
    exit;
    
} catch (PDOException $e) {
    echo "❌ Database Error: " . $e->getMessage() . "\n";
    echo "Error Code: " . $e->getCode() . "\n";
}
?>