<?php
// products/view.php
require_once __DIR__ . '/../includes/init.php';

$product_id = validate_int($_GET['id'] ?? 0, 1);
if (!$product_id) {
    $_SESSION['error'] = 'Product not found.';
    redirect(PRODUCTS_URL . '/');
}

try {
    // 获取产品信息
    $stmt = executeQuery("
        SELECT p.*, c.name AS category_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        WHERE p.id = ?
    ", [$product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$product) {
        $_SESSION['error'] = 'Product not found.';
        redirect(PRODUCTS_URL . '/');
    }
    
    // 检查用户是否可以评论（是否购买过）
    $can_review = false;
    if (is_logged_in()) {
        $stmt = executeQuery("
            SELECT 1 FROM order_items oi
            JOIN orders o ON oi.order_id = o.id
            WHERE o.user_id = ? AND oi.product_id = ? AND o.status = 'completed'
            LIMIT 1
        ", [$_SESSION['user_id'], $product_id]);
        $can_review = (bool)$stmt->fetch();
    }
    
} catch (Exception $e) {
    error_log("Product view error: " . $e->getMessage());
    $_SESSION['error'] = 'Unable to load product details.';
    redirect(PRODUCTS_URL . '/');
}

$success = $_SESSION['success'] ?? null;
$error = $_SESSION['error'] ?? null;
unset($_SESSION['success'], $_SESSION['error']);
?>

<?php include __DIR__ . '/../includes/header.php'; ?>

<?php if ($success): ?>
    <div class="message success"><?= esc($success) ?></div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="message error"><?= esc($error) ?></div>
<?php endif; ?>

<div class="product-detail">
    <div class="product-images">
        <?php if (!empty($product['image_url'])): ?>
            <img src="<?= ASSETS_URL ?>/images/products/<?= esc($product['image_url']) ?>" 
                 alt="<?= esc($product['title']) ?>">
        <?php else: ?>
            <div class="product-image-placeholder large">
                <span><?= esc($product['title']) ?></span>
            </div>
        <?php endif; ?>
    </div>
    
    <div class="product-info">
        <h1><?= esc($product['title']) ?></h1>
        <p class="price">$<?= number_format($product['price'], 2) ?></p>
        <p class="stock <?= $product['stock_quantity'] > 0 ? 'in-stock' : 'out-of-stock' ?>">
            <?= $product['stock_quantity'] > 0 ? 'In Stock' : 'Out of Stock' ?>
        </p>
        
        <div class="product-description">
            <p><?= nl2br(esc($product['description'] ?? 'No description available.')) ?></p>
        </div>
        
        <table class="specs-table">
            <tr><td>Brand:</td><td><?= esc($product['brand']) ?></td></tr>
            <tr><td>Category:</td><td><?= esc($product['category_name'] ?? 'Uncategorized') ?></td></tr>
            <tr><td>DPI:</td><td><?= esc($product['dpi']) ?></td></tr>
            <tr><td>Sensor:</td><td><?= esc($product['sensor_type']) ?></td></tr>
            <tr><td>Connectivity:</td><td><?= esc($product['connectivity']) ?></td></tr>
            <tr><td>Weight:</td><td><?= esc($product['weight_grams']) ?>g</td></tr>
            <tr><td>Hand Orientation:</td><td><?= esc($product['hand_orientation']) ?></td></tr>
            <tr><td>Buttons:</td><td><?= esc($product['button_count']) ?></td></tr>
            <tr><td>RGB Lighting:</td><td><?= $product['rgb_lighting'] ? 'Yes' : 'No' ?></td></tr>
        </table>
        
        <?php if ($product['stock_quantity'] > 0): ?>
            <form action="<?= CART_URL ?>/add_to_cart.php" method="POST" class="add-to-cart-form">
                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                <div class="quantity-selector">
                    <label for="quantity">Quantity:</label>
                    <input type="number" id="quantity" name="quantity" value="1" min="1" max="<?= $product['stock_quantity'] ?>">
                </div>
                <button type="submit" class="btn btn-primary">Add to Cart</button>
            </form>
        <?php else: ?>
            <p class="out-of-stock-msg">This product is currently out of stock.</p>
        <?php endif; ?>
        
        <div class="product-actions">
            <a href="<?= PRODUCTS_URL ?>/compare.php?id=<?= $product['id'] ?>" class="btn">Add to Compare</a>
            <a href="<?= PRODUCTS_URL ?>/" class="btn">Back to Products</a>
        </div>
    </div>
</div>

<!-- 包含评论部分 -->
<?php include __DIR__ . '/../includes/reviews_section.php'; ?>
<!-- Giscus 评论区 -->
<div id="gitalk-container"></div>

<script src="https://giscus.app/client.js"
        data-repo="cnGRT/Comments"
        data-repo-id="R_kgDOQLJRNg"
        data-category="General"
        data-category-id="DIC_kwDOQLJRNs4CxMm8"
        data-mapping="url"
        data-strict="0"
        data-reactions-enabled="1"
        data-emit-metadata="0"
        data-input-position="bottom"
        data-theme="preferred_color_scheme"
        data-lang="en"
        crossorigin="anonymous"
        async>
</script>
<?php include __DIR__ . '/../includes/footer.php'; ?>