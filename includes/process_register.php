<?php
include '../config/database.php';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    try {
        // 检查用户是否已存在
        $check_stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $check_stmt->execute([$username, $email]);
        
        if($check_stmt->rowCount() > 0) {
            $_SESSION['error'] = "Username or email already exists!";
            header("Location: /ecommerce-project/auth/register.php");
            exit();
        }
        
        // 创建新用户
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$username, $email, $hashed_password]);
        
        $_SESSION['success'] = "Registration successful! Please login.";
        header("Location: /ecommerce-project/auth/login.php");
        exit();
        
    } catch(PDOException $e) {
        $_SESSION['error'] = "Registration failed: " . $e->getMessage();
        header("Location: /ecommerce-project/auth/register.php");
        exit();
    }
}
?>