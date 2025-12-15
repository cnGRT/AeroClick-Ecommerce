<?php
// cart/index.php
require_once __DIR__ . '/../includes/init.php';

error_log("=== CART PAGE LOAD ===");
error_log("Session cart: " . print_r($_SESSION['cart'] ?? 'empty', true));

if (!is_logged_in()) {
    redirect(AUTH_URL . '/login.php');
}

$cart_items = [];
$total = 0;

if (!empty($_SESSION['cart'])) {
    $product_ids = array_keys($_SESSION['cart']);
    $placeholders = str_repeat('?,', count($product_ids) - 1) . '?';
    
    try {
        $stmt = executeQuery("SELECT id, title, price, stock_quantity, image_url FROM products WHERE id IN ($placeholders)", $product_ids);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($products as $product) {
            $qty = $_SESSION['cart'][$product['id']];
            $item_total = $product['price'] * $qty;
            $total += $item_total;
            $cart_items[] = array_merge($product, ['quantity' => $qty, 'item_total' => $item_total]);
        }
        
        error_log("Cart items loaded: " . count($cart_items) . " items");
    } catch (Exception $e) {
        error_log("Cart items fetch error: " . $e->getMessage());
        $_SESSION['error'] = 'Unable to load cart items.';
    }
} else {
    error_log("Cart is empty");
}

$success = $_SESSION['success'] ?? null;
$error = $_SESSION['error'] ?? null;
unset($_SESSION['success'], $_SESSION['error']);

error_log("Cart page ready - Items: " . count($cart_items) . ", Total: $" . $total);
?>

<?php include __DIR__ . '/../includes/header.php'; ?>

<h2>Shopping Cart</h2>

<?php if ($success): ?>
    <div class="message success"><?= esc($success) ?></div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="message error"><?= esc($error) ?></div>
<?php endif; ?>

<?php if (empty($cart_items)): ?>
    <div class="empty-cart">
        <p>Your cart is empty.</p>
        <a href="<?= PRODUCTS_URL ?>/" class="btn">Continue Shopping</a>
    </div>
<?php else: ?>
    <table class="cart-table">
        <thead>
            <tr>
                <th>Product</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Total</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($cart_items as $item): ?>
                <tr>
                    <td>
                        <?php if (!empty($item['image_url'])): ?>
                            <img src="<?= ASSETS_URL ?>/images/products/<?= esc($item['image_url']) ?>" 
                                 alt="<?= esc($item['title']) ?>" width="50">
                        <?php else: ?>
                            <div class="product-image-placeholder small">
                                <span>Product</span>
                            </div>
                        <?php endif; ?>
                        <?= esc($item['title']) ?>
                    </td>
                    <td>$<?= number_format($item['price'], 2) ?></td>
                    <td>
                        <form method="POST" action="update_cart.php" style="display:inline;">
                            <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
                            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                            <input type="number" name="quantity" value="<?= $item['quantity'] ?>" min="1" max="<?= $item['stock_quantity'] ?>" style="width: 60px;">
                            <button type="submit" class="btn-small">Update</button>
                        </form>
                    </td>
                    <td>$<?= number_format($item['item_total'], 2) ?></td>
                    <td>
                        <form method="POST" action="remove_item.php" style="display:inline;">
                            <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
                            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                            <button type="submit" class="btn-small btn-danger">Remove</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="cart-total">
        <h3>Total: $<?= number_format($total, 2) ?></h3>
    </div>

    <div class="cart-actions">
        <a href="<?= PRODUCTS_URL ?>/" class="btn">Continue Shopping</a>
        <a href="<?= CART_URL ?>/checkout.php" class="btn btn-primary">Proceed to Checkout</a>
    </div>
<?php endif; ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>