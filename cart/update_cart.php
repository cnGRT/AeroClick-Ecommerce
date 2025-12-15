<?php
require_once __DIR__ . '/../includes/init.php';

if (!is_logged_in()) {
    redirect(AUTH_URL . '/login.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    // CSRF 保护
    if (!validate_csrf($_POST['csrf_token'] ?? '')) {
        $_SESSION['error'] = 'Invalid security token.';
        redirect(CART_URL . '/');
    }
    
    $product_id = validate_int($_POST['product_id'], 1);
    $quantity = max(1, validate_int($_POST['quantity'] ?? 1, 1));
    
    if ($product_id && isset($_SESSION['cart'][$product_id])) {
        // 检查库存
        try {
            $stmt = executeQuery("SELECT stock_quantity FROM products WHERE id = ?", [$product_id]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($product && $quantity <= $product['stock_quantity']) {
                $_SESSION['cart'][$product_id] = $quantity;
                $_SESSION['success'] = 'Cart updated successfully.';
            } else {
                $_SESSION['error'] = 'Requested quantity exceeds available stock.';
            }
        } catch (Exception $e) {
            error_log("Cart update error: " . $e->getMessage());
            $_SESSION['error'] = 'Failed to update cart.';
        }
    }
}

redirect(CART_URL . '/');
?>