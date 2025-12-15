<?php
// cart/checkout_debug.php - 详细调试版本
require_once __DIR__ . '/../includes/init.php';

error_log("=== CHECKOUT DEBUG START ===");

if (!is_logged_in()) {
    error_log("User not logged in");
    $_SESSION['error'] = 'Please login to checkout.';
    redirect(AUTH_URL . '/login.php');
}

// 检查购物车是否为空
if (empty($_SESSION['cart'])) {
    error_log("Cart is empty");
    $_SESSION['error'] = 'Your cart is empty.';
    redirect(CART_URL . '/');
}

error_log("Cart contents: " . print_r($_SESSION['cart'], true));

// 处理订单提交
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log("Processing checkout POST request");
    
    // CSRF 保护
    if (!validate_csrf($_POST['csrf_token'] ?? '')) {
        error_log("CSRF token invalid");
        $error = 'Invalid security token.';
    } else {
        $address = validate_string($_POST['address'] ?? '', 500);
        if (empty($address)) {
            error_log("Address is empty");
            $error = 'Shipping address is required.';
        } else {
            try {
                error_log("Starting order process for user: " . $_SESSION['user_id']);
                
                // 开始事务
                $pdo->beginTransaction();
                
                // 1. 计算总价和检查库存
                $total = 0;
                $cart_items = [];
                $product_ids = array_keys($_SESSION['cart']);
                $placeholders = str_repeat('?,', count($product_ids) - 1) . '?';
                
                $stmt = $pdo->prepare("SELECT id, title, price, stock_quantity FROM products WHERE id IN ($placeholders)");
                $stmt->execute($product_ids);
                $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                foreach ($products as $product) {
                    $qty = $_SESSION['cart'][$product['id']];
                    if ($qty > $product['stock_quantity']) {
                        throw new Exception("Insufficient stock for product ID: " . $product['id']);
                    }
                    $item_total = $product['price'] * $qty;
                    $total += $item_total;
                    $cart_items[] = array_merge($product, ['quantity' => $qty]);
                }
                
                error_log("Total amount: $" . $total);
                
                // 2. 创建订单主记录
                $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount, shipping_address, status) VALUES (?, ?, ?, 'processing')");
                $stmt->execute([$_SESSION['user_id'], $total, $address]);
                $order_id = $pdo->lastInsertId();
                
                error_log("Order created with ID: " . $order_id);
                
                // 3. 插入订单项
                foreach ($cart_items as $item) {
                    $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$order_id, $item['id'], $item['quantity'], $item['price']]);
                    error_log("Order item added: Product " . $item['id'] . ", Qty: " . $item['quantity']);
                    
                    // 扣减库存
                    $stmt = $pdo->prepare("UPDATE products SET stock_quantity = stock_quantity - ? WHERE id = ?");
                    $stmt->execute([$item['quantity'], $item['id']]);
                    error_log("Stock updated: Product " . $item['id'] . " -" . $item['quantity']);
                }
                
                // 4. 更新订单状态为完成
                $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount, shipping_address, status) VALUES (?, ?, ?, 'processing')");
$stmt->execute([$_SESSION['user_id'], $total, $address]);
                
                // 5. 清空购物车
                unset($_SESSION['cart']);
                
                $pdo->commit();
                
                error_log("Order completed successfully: #" . $order_id);
                $_SESSION['success'] = 'Order placed successfully! Order ID: #' . $order_id;
                redirect(USER_URL . '/orders.php');
                
            } catch (Exception $e) {
                $pdo->rollback();
                error_log("CHECKOUT ERROR: " . $e->getMessage());
                error_log("Error trace: " . $e->getTraceAsString());
                $error = 'Failed to place order: ' . $e->getMessage();
            }
        }
    }
}

// 如果执行到这里，说明有错误或者正在显示表单
error_log("Checkout page displayed (no redirect happened)");

// 获取购物车商品用于显示
$cart_items = [];
$total = 0;
if (!empty($_SESSION['cart'])) {
    $product_ids = array_keys($_SESSION['cart']);
    $placeholders = str_repeat('?,', count($product_ids) - 1) . '?';
    
    try {
        $stmt = executeQuery("SELECT id, title, price, stock_quantity FROM products WHERE id IN ($placeholders)", $product_ids);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($products as $product) {
            $qty = $_SESSION['cart'][$product['id']];
            $item_total = $product['price'] * $qty;
            $total += $item_total;
            $cart_items[] = array_merge($product, ['quantity' => $qty]);
        }
    } catch (Exception $e) {
        error_log("Cart display error: " . $e->getMessage());
    }
}
?>

<?php include __DIR__ . '/../includes/header.php'; ?>

<h2>Checkout - Debug Version</h2>

<?php if (isset($error)): ?>
    <div class="message error">
        <strong>Error:</strong> <?= esc($error) ?>
        <br><small>Check the error logs for details.</small>
    </div>
<?php endif; ?>

<?php if (!empty($cart_items)): ?>
    <div class="order-summary">
        <h3>Order Summary</h3>
        <ul>
            <?php foreach ($cart_items as $item): ?>
                <li><?= esc($item['title']) ?> - <?= $item['quantity'] ?> x $<?= number_format($item['price'], 2) ?></li>
            <?php endforeach; ?>
        </ul>
        <p><strong>Total: $<?= number_format($total, 2) ?></strong></p>
    </div>

    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
        <div class="form-group">
            <label for="address">Shipping Address</label>
            <textarea name="address" id="address" rows="4" required 
                placeholder="Enter your full shipping address...">123 Test Street, Test City</textarea>
        </div>

        <button type="submit" class="btn btn-primary">Place Order (Debug)</button>
        <a href="<?= CART_URL ?>/" class="btn">← Back to Cart</a>
    </form>
<?php else: ?>
    <p>Your cart is empty.</p>
    <a href="<?= PRODUCTS_URL ?>/" class="btn">Continue Shopping</a>
<?php endif; ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>