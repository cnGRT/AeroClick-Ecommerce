<?php
require_once __DIR__ . '/../includes/init.php';

if (!is_logged_in()) {
    redirect(AUTH_URL . '/login.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF 保护
    if (!validate_csrf($_POST['csrf_token'] ?? '')) {
        $_SESSION['error'] = 'Invalid security token.';
        redirect(CART_URL . '/');
    }
    
    $id = validate_int($_POST['product_id'] ?? 0, 1);
    if ($id && isset($_SESSION['cart'][$id])) {
        unset($_SESSION['cart'][$id]);
        $_SESSION['success'] = 'Item removed from cart.';
    }
}

redirect(CART_URL . '/');