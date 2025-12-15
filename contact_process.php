<?php
// contact_process.php

require_once __DIR__ . '/includes/init.php';

// 开启错误报告（开发环境）
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 检查是否为POST请求
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = 'Invalid request method.';
    header('Location: contact.php');
    exit;
}

// 验证CSRF令牌
if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])) {
    $_SESSION['error'] = 'Invalid CSRF token. Please try again.';
    $_SESSION['form_data'] = $_POST;
    header('Location: contact.php');
    exit;
}

// 获取并验证表单数据
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$message = trim($_POST['message'] ?? '');

$errors = [];

// 验证必填字段
if (empty($name)) {
    $errors[] = 'Name is required';
}

if (empty($email)) {
    $errors[] = 'Email is required';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Invalid email format';
}

if (empty($message)) {
    $errors[] = 'Message is required';
}

// 验证文件上传
$maxFileSize = 5 * 1024 * 1024; // 5MB
$maxFiles = 5;
$allowedTypes = [
    'application/pdf',
    'application/msword',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'image/jpeg',
    'image/jpg',
    'image/png',
    'application/zip'
];

$uploadedFiles = [];

if (isset($_FILES['cv_files']) && $_FILES['cv_files']['error'][0] !== 4) { // 4 = no file uploaded
    $fileCount = count($_FILES['cv_files']['name']);
    
    if ($fileCount > $maxFiles) {
        $errors[] = "Maximum $maxFiles files allowed.";
    }
    
    for ($i = 0; $i < $fileCount; $i++) {
        if ($_FILES['cv_files']['error'][$i] === UPLOAD_ERR_OK) {
            // 验证文件大小
            if ($_FILES['cv_files']['size'][$i] > $maxFileSize) {
                $errors[] = "File '{$_FILES['cv_files']['name'][$i]}' exceeds maximum size of 5MB.";
                continue;
            }
            
            // 验证文件类型
            $fileType = mime_content_type($_FILES['cv_files']['tmp_name'][$i]);
            if (!in_array($fileType, $allowedTypes)) {
                $errors[] = "File '{$_FILES['cv_files']['name'][$i]}' type is not allowed.";
                continue;
            }
            
            // 获取文件扩展名
            $originalName = $_FILES['cv_files']['name'][$i];
            $fileExt = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
            
            // 生成唯一文件名
            $uniqueName = uniqid() . '_' . date('Ymd_His') . '_' . $i . '.' . $fileExt;
            $uploadedFiles[] = [
                'tmp_name' => $_FILES['cv_files']['tmp_name'][$i],
                'original_name' => $originalName,
                'unique_name' => $uniqueName,
                'file_type' => $fileType,
                'file_size' => $_FILES['cv_files']['size'][$i]
            ];
        } elseif ($_FILES['cv_files']['error'][$i] !== UPLOAD_ERR_NO_FILE) {
            $errors[] = "Error uploading file '{$_FILES['cv_files']['name'][$i]}'. Error code: {$_FILES['cv_files']['error'][$i]}";
        }
    }
}

// 如果有错误，返回表单页面并显示错误
if (!empty($errors)) {
    $_SESSION['error'] = implode('<br>', $errors);
    $_SESSION['form_data'] = $_POST;
    header('Location: contact.php');
    exit;
}

// 连接到数据库并保存数据
try {
    $servername = "sql301.infinityfree.com";
    $username = "if0_38341067";
    $password = "Grtnb137";
    $dbname = "if0_38341067_wp221";
    
    // 创建数据库连接
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // 创建上传目录（如果不存在）
    $uploadDir = __DIR__ . '/uploads/cv/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    // 创建files目录（如果不存在）
    if (!file_exists($uploadDir . 'files/')) {
        mkdir($uploadDir . 'files/', 0777, true);
    }
    
    // 移动上传的文件到files目录
    $filePaths = [];
    foreach ($uploadedFiles as $file) {
        $destination = $uploadDir . 'files/' . $file['unique_name'];
        if (move_uploaded_file($file['tmp_name'], $destination)) {
            $filePaths[] = $file['unique_name'];
        } else {
            error_log("Failed to move uploaded file: " . $file['original_name']);
        }
    }
    
    // 序列化文件路径数组
    $filesJson = !empty($filePaths) ? json_encode($filePaths) : null;
    
    // 创建数据库表（如果不存在）
    $createTableSQL = "
        CREATE TABLE IF NOT EXISTS wpri_contact_submissions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(100) NOT NULL,
            message TEXT NOT NULL,
            files TEXT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_email (email),
            INDEX idx_created_at (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ";
    $pdo->exec($createTableSQL);
    
    // 准备并执行插入语句
    $stmt = $pdo->prepare("
        INSERT INTO wpri_contact_submissions (name, email, message, files, created_at) 
        VALUES (:name, :email, :message, :files, NOW())
    ");
    
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':message', $message);
    $stmt->bindParam(':files', $filesJson);
    
    if ($stmt->execute()) {
        $lastId = $pdo->lastInsertId();
        
        // 处理成功情况
        $successMsg = 'Thank you for your message!';
        if (!empty($uploadedFiles)) {
            $fileCount = count($uploadedFiles);
            $successMsg .= " We have received your message and $fileCount file(s).";
        } else {
            $successMsg .= " We have received your message.";
        }
        
        $_SESSION['success'] = $successMsg;
        unset($_SESSION['form_data']); // 清除表单数据
        
        // 记录成功日志
        error_log("Contact form submitted successfully. ID: $lastId, Name: $name, Email: $email");
        
    } else {
        $errorInfo = $stmt->errorInfo();
        error_log("Database insert failed: " . print_r($errorInfo, true));
        $_SESSION['error'] = 'Sorry, there was an error saving your message. Please try again.';
        $_SESSION['form_data'] = $_POST;
    }
    
} catch (PDOException $e) {
    // 数据库错误处理
    error_log("Database Error: " . $e->getMessage());
    error_log("Error Code: " . $e->getCode());
    error_log("File: " . $e->getFile() . " Line: " . $e->getLine());
    
    $_SESSION['error'] = 'Sorry, there was an error processing your request. Please try again.';
    $_SESSION['form_data'] = $_POST;
}

// 重定向回联系页面
header('Location: contact.php');
exit;
?>