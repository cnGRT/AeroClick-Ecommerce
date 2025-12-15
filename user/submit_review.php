<?php
require_once __DIR__ . '/../includes/init.php';


if (!is_logged_in()) {
    redirect(AUTH_URL . '/login.php');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(BASE_URL . '/');
}

// CSRF 保护
if (!validate_csrf($_POST['csrf_token'] ?? '')) {
    $_SESSION['error'] = 'Invalid security token.';
    redirect(PRODUCTS_URL . '/');
}

$product_id = validate_int($_POST['product_id'] ?? 0, 1);
$rating = validate_int($_POST['rating'] ?? 0, 1, 5);
$comment = validate_string($_POST['comment'] ?? '', 1000);

// 验证输入
if (!$product_id || !$rating || strlen($comment) < 10) {
    $_SESSION['error'] = 'Invalid review data. Rating must be 1-5, and comment at least 10 characters.';
    redirect(PRODUCTS_URL . "/view.php?id=$product_id");
}

try {
    // 验证用户是否已购买该商品
    $stmt = executeQuery("
        SELECT 1 FROM order_items oi
        JOIN orders o ON oi.order_id = o.id
        WHERE o.user_id = ? AND oi.product_id = ?
    ", [$_SESSION['user_id'], $product_id]);
    
    if (!$stmt->fetch()) {
        $_SESSION['error'] = 'You must purchase this product before reviewing.';
        redirect(PRODUCTS_URL . "/view.php?id=$product_id");
    }

    // 防止重复评论
    $stmt = executeQuery("SELECT id FROM reviews WHERE user_id = ? AND product_id = ?", [$_SESSION['user_id'], $product_id]);
    if ($stmt->fetch()) {
        $_SESSION['error'] = 'You have already reviewed this product.';
        redirect(PRODUCTS_URL . "/view.php?id=$product_id");
    }

    // 插入评论（verified_purchase = true）
    $stmt = executeQuery("
        INSERT INTO reviews (user_id, product_id, rating, comment, verified_purchase)
        VALUES (?, ?, ?, ?, 1)
    ", [$_SESSION['user_id'], $product_id, $rating, $comment]);

    $_SESSION['success'] = 'Thank you for your review!';
    redirect(PRODUCTS_URL . "/view.php?id=$product_id");

} catch (Exception $e) {
    error_log("Review submission error: " . $e->getMessage());
    $_SESSION['error'] = 'Failed to submit review. Please try again.';
    redirect(PRODUCTS_URL . "/view.php?id=$product_id");
}

?>