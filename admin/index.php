<?php
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/admin_auth.php';
// 使用严格的管理员认证
require_admin_auth();
// 记录访问日志
log_admin_action('view_dashboard');

if (!is_logged_in() || !is_admin()) {
    redirect(BASE_URL . '/');
}

try {
    // 获取统计
    $stats = executeQuery("SELECT COUNT(*) as total_orders, COALESCE(SUM(total_amount), 0) as total_revenue FROM orders")->fetch();
    $total_users = executeQuery("SELECT COUNT(*) as total_users FROM users")->fetch()['total_users'];

    // 获取热销产品
    $top_products = executeQuery("
        SELECT p.title, SUM(oi.quantity) as total_sold
        FROM order_items oi
        JOIN products p ON oi.product_id = p.id
        GROUP BY p.id
        ORDER BY total_sold DESC
        LIMIT 5
    ")->fetchAll();

} catch (Exception $e) {
    error_log("Admin dashboard error: " . $e->getMessage());
    $stats = ['total_orders' => 0, 'total_revenue' => 0];
    $total_users = 0;
    $top_products = [];
}
?>

<?php include __DIR__ . '/../includes/header.php'; ?>
<link rel="stylesheet" href="<?= ASSETS_URL ?>/css/admin.css">

<div class="admin-container">
    <h1>Admin Dashboard</h1>

    <div class="kpi-grid">
        <div class="kpi-card">
            <h3>Total Revenue</h3>
            <p class="kpi-value">$<?= number_format($stats['total_revenue'], 2) ?></p>
        </div>
        <div class="kpi-card">
            <h3>Total Orders</h3>
            <p class="kpi-value"><?= $stats['total_orders'] ?></p>
        </div>
        <div class="kpi-card">
            <h3>Registered Users</h3>
            <p class="kpi-value"><?= $total_users ?></p>
        </div>
    </div>

    <div class="top-products">
        <h2>Top Selling Products</h2>
        <?php if (!empty($top_products)): ?>
            <ul>
                <?php foreach ($top_products as $product): ?>
                    <li><?= esc($product['title']) ?> <span>(<?= $product['total_sold'] ?> sold)</span></li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No sales data available yet.</p>
        <?php endif; ?>
    </div>

    <div class="admin-links">
        <h3>Quick Actions</h3>
        <ul>
            <li><a href="<?= ADMIN_URL ?>/products.php">Manage Products & Inventory</a></li>
            <li><a href="<?= ADMIN_URL ?>/orders.php">View All Orders</a></li>
            <li><a href="<?= ADMIN_URL ?>/users.php">Manage Users</a></li>
        </ul>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>