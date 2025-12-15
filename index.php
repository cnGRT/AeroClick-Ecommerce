<?php
require_once __DIR__ . '/includes/init.php';

// 获取热销产品
$featured_products = [];
try {
    $stmt = executeQuery("
        SELECT p.id, p.title, p.price, p.image_url
        FROM products p
        LEFT JOIN order_items oi ON p.id = oi.product_id
        GROUP BY p.id
        ORDER BY COALESCE(SUM(oi.quantity), 0) DESC
        LIMIT 4
    ");
    $featured_products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log("Featured products error: " . $e->getMessage());
    $featured_products = [];
}
?>

<?php include __DIR__ . '/includes/header.php'; ?>

<section class="hero">
    <h1>Precision. Performance. Control.</h1>
    <p>Find your perfect gaming mouse with AeroClick.</p>
    <a href="<?= PRODUCTS_URL ?>/" class="btn">Shop Now</a>
</section>

<h2>Featured Gaming Mice</h2>
<div class="products-grid">
    <?php foreach ($featured_products as $product): ?>
        <div class="product-card">
            <div class="product-image">
                <?php if (!empty($product['image_url'])): ?>
                    <img src="<?= ASSETS_URL ?>/images/products/<?= esc($product['image_url']) ?>" 
                         alt="<?= esc($product['title']) ?>">
                <?php else: ?>
                    <!-- 如果没有图片，显示产品名称和占位背景 -->
                    <div class="product-image-placeholder">
                        <span><?= esc($product['title']) ?></span>
                    </div>
                <?php endif; ?>
            </div>
            <div class="product-info">
                <h3 class="product-title"><?= esc($product['title']) ?></h3>
                <p class="product-price">$<?= number_format($product['price'], 2) ?></p>
                <a href="<?= PRODUCTS_URL ?>/view.php?id=<?= $product['id'] ?>">View Details</a>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>