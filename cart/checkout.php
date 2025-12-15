<?php
// cart/checkout.php - 完全修复版本
require_once __DIR__ . '/../includes/init.php';

if (!is_logged_in()) {
    redirect(AUTH_URL . '/login.php');
}

// 检查购物车是否为空
if (empty($_SESSION['cart'])) {
    $_SESSION['error'] = 'Your cart is empty.';
    redirect(CART_URL . '/');
}

$success = $_SESSION['success'] ?? null;
$error = $_SESSION['error'] ?? null;
unset($_SESSION['success'], $_SESSION['error']);

// 获取购物车商品
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
            if ($qty > $product['stock_quantity']) {
                $_SESSION['error'] = 'Some items are out of stock. Please update your cart.';
                redirect(CART_URL . '/');
            }
            $item_total = $product['price'] * $qty;
            $total += $item_total;
            $cart_items[] = array_merge($product, ['quantity' => $qty]);
        }
    } catch (Exception $e) {
        error_log("Checkout cart items error: " . $e->getMessage());
        $_SESSION['error'] = 'Unable to process checkout.';
        redirect(CART_URL . '/');
    }
}

// 处理订单提交
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF 保护
    if (!validate_csrf($_POST['csrf_token'] ?? '')) {
        $_SESSION['error'] = 'Invalid security token.';
        redirect(CART_URL . '/checkout.php');
    }
    
    // 地址验证
    $address = trim($_POST['address'] ?? '');
    if (empty($address)) {
        $_SESSION['error'] = 'Shipping address is required.';
        redirect(CART_URL . '/checkout.php');
    }
    
    try {
        // 开始事务
        $pdo->beginTransaction();

        // 1. 重新检查库存
        foreach ($cart_items as $item) {
            $stmt = $pdo->prepare("SELECT stock_quantity FROM products WHERE id = ?");
            $stmt->execute([$item['id']]);
            $current_stock = $stmt->fetch(PDO::FETCH_COLUMN);
            
            if ($current_stock < $item['quantity']) {
                throw new Exception("Insufficient stock for product ID: " . $item['id']);
            }
        }

        // 2. 创建订单
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount, shipping_address, status) VALUES (?, ?, ?, 'processing')");
        $stmt->execute([$_SESSION['user_id'], $total, $address]);
        $order_id = $pdo->lastInsertId();

        // 3. 插入订单项 + 扣减库存
        foreach ($cart_items as $item) {
            // 插入 order_items
            $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
            $stmt->execute([$order_id, $item['id'], $item['quantity'], $item['price']]);

            // 扣减库存
            $stmt = $pdo->prepare("UPDATE products SET stock_quantity = stock_quantity - ? WHERE id = ?");
            $stmt->execute([$item['quantity'], $item['id']]);
        }

        // 4. 更新订单状态为完成
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount, shipping_address, status) VALUES (?, ?, ?, 'processing')");
        $stmt->execute([$_SESSION['user_id'], $total, $address]);

        // 5. 清空购物车
        unset($_SESSION['cart']);

        $pdo->commit();
        
        $_SESSION['success'] = 'Order placed successfully! Order ID: #' . $order_id;
        redirect(USER_URL . '/orders.php');

    } catch (Exception $e) {
        $pdo->rollback();
        error_log("Checkout error: " . $e->getMessage());
        
        // 具体的错误信息
        if (strpos($e->getMessage(), 'Insufficient stock') !== false) {
            $_SESSION['error'] = 'Some items are out of stock. Please update your cart.';
        } else if (strpos($e->getMessage(), 'shipping_address') !== false) {
            $_SESSION['error'] = 'Database configuration error. Please contact support.';
        } else {
            $_SESSION['error'] = 'Failed to place order. Please try again. Error: ' . $e->getMessage();
        }
        
        redirect(CART_URL . '/checkout.php');
    }
}
?>

<?php include __DIR__ . '/../includes/header.php'; ?>

<h2>Checkout</h2>

<?php if ($success): ?>
    <div class="message success"><?= esc($success) ?></div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="message error"><?= esc($error) ?></div>
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

    <form method="POST" id="checkout-form">
        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
        
        <div class="form-group">
            <label for="address">Shipping Address:</label>
            <textarea name="address" id="address" rows="4" required 
                placeholder="Enter your full shipping address..."><?= esc($_POST['address'] ?? '') ?></textarea>
        </div>

        <div class="form-group">
            <p><strong>Payment Method:</strong> Simulated (No real transaction)</p>
            <p>All orders are processed as <em>cash on delivery</em> for demonstration purposes.</p>
        </div>

        <button type="submit" class="btn btn-primary" id="submit-btn">Place Order</button>
        <a href="<?= CART_URL ?>/" class="btn">← Back to Cart</a>
    </form>

    <!-- 调试信息 -->
    <div style="margin-top: 30px; padding: 15px; background: #1a1a1a; border-radius: 5px; border: 1px solid #333;">
        <h4>Debug Information:</h4>
        <p><small>User ID: <?= $_SESSION['user_id'] ?? 'Not logged in' ?></small></p>
        <p><small>Cart Items: <?= count($cart_items) ?></small></p>
        <p><small>Total: $<?= number_format($total, 2) ?></small></p>
        <p><small><a href="<?= CART_URL ?>/checkout_debug.php" style="color: #00d9ff;">Open Debug Version</a></small></p>
    </div>
<?php endif; ?>

<script>
// 添加一些客户端验证
document.getElementById('checkout-form').addEventListener('submit', function(e) {
    const address = document.getElementById('address').value.trim();
    const submitBtn = document.getElementById('submit-btn');
    
    if (!address) {
        e.preventDefault();
        alert('Please enter a shipping address.');
        return;
    }
    
    // 防止重复提交
    submitBtn.disabled = true;
    submitBtn.textContent = 'Processing...';
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>