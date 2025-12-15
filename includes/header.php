<?php
// includes/header.php

// 直接包含必要的文件，避免路径问题
$base_dir = __DIR__;
require_once $base_dir . '/functions.php';

// 启动会话（如果还没启动）
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 获取当前用户信息
$current_user = null;
if (isset($_SESSION['user_id'])) {
    $current_user = [
        'id' => $_SESSION['user_id'],
        'username' => $_SESSION['username'] ?? 'User',
        'role' => $_SESSION['role'] ?? 'user'
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AeroClick - Precision Gaming Mice</title>
    <!-- Bootstrap CSS -->
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/style.css">
    
</head>
<body>
    <header class="navbar">
        <div class="nav-container">
            <div class="nav-brand">
    <a href="<?= BASE_URL ?>/" class="brand-link">
        <img src="<?= ASSETS_URL ?>/images/logo.png" alt="AeroClick Logo" class="brand-logo">
        <span class="brand-text">AeroClick</span>
    </a>
</div>
<nav class="nav-menu">
    <ul class="nav-links">
        <li><a href="<?= PRODUCTS_URL ?>/">Products</a></li>
        <li><a href="<?= BASE_URL ?>/about.php">About</a></li>
        <li><a href="<?= BASE_URL ?>/contact.php">Contact</a></li>
        <li><a href="https://grant.fwh.is/wordpress/contact-list" target="_blank">Contact-List</a></li>
        <li><a href="https://grant.fwh.is/wordpress/email/" target="_blank">Subscribe</a></li>
        <li><a href="https://grant.fwh.is/wordpress/Recruitment/" target="_blank">Recruitment</a></li>
        <li><a href="<?= BASE_URL ?>/forum.php">Forum</a></li>
    

        
        <?php if (isset($_SESSION['user_id']) && $current_user): ?>
            <!-- 登录状态显示 -->
            <li><a href="<?= USER_URL ?>/profile.php">
                <?= htmlspecialchars($current_user['username']) ?>
            </a></li>
            <li><a href="<?= CART_URL ?>/">Cart</a></li>
            <li><a href="<?= AUTH_URL ?>/logout.php" class="logout-btn">Logout</a></li>
            <?php if (($current_user['role'] ?? '') === 'admin'): ?>
                <li><a href="<?= ADMIN_URL ?>/" class="admin-btn">Admin</a></li>
            <?php endif; ?>
        <?php else: ?>
            <!-- 未登录状态显示 -->
            <li><a href="<?= AUTH_URL ?>/login.php">Login</a></li>
            
        <?php endif; ?>
    </ul>
</nav>
        </div>
    </header>

    <main class="main-content">
        <div class="content-container">