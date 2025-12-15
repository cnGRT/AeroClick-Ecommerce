<?php
// cart/add_to_cart.php - 简化版本
session_start();
require_once __DIR__ . '/../config/paths.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

// 记录开始
error_log("=== ADD TO CART STARTED ===");

// 基本检查
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    error_log("Not a POST request");
    header("Location: " . PRODUCTS_URL . "/");
    exit();
}

// 检查登录状态
if (!isset($_SESSION['user_id'])) {
    error_log("User not logged in");
    $_SESSION['error'] = 'Please login first.';
    header("Location: " . AUTH_URL . "/login.php");
    exit();
}

// 获取数据
$product_id = intval($_POST['product_id'] ?? 0);
$quantity = intval($_POST['quantity'] ?? 1);

error_log("Product ID: $product_id, Quantity: $quantity");

if ($product_id <= 0) {
    error_log("Invalid product ID");
    $_SESSION['error'] = 'Invalid product.';
    header("Location: " . PRODUCTS_URL . "/");
    exit();
}

// 初始化购物车
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
    error_log("Cart initialized");
}

// 简单添加逻辑（跳过库存检查先测试）
if (isset($_SESSION['cart'][$product_id])) {
    $_SESSION['cart'][$product_id] += $quantity;
} else {
    $_SESSION['cart'][$product_id] = $quantity;
}

error_log("Cart after add: " . print_r($_SESSION['cart'], true));

// 设置成功消息
$_SESSION['success'] = 'Product added to cart!';

// 重定向到购物车
error_log("Redirecting to: " . CART_URL . "/");
header("Location: " . CART_URL . "/");
exit();
?>