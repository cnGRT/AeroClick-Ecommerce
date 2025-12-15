<?php
require_once __DIR__ . '/../includes/init.php';

if (!is_logged_in()) {
    redirect(AUTH_URL . '/login.php');
}

try {
    $stmt = executeQuery("
        SELECT o.id, o.total_amount, o.status, o.created_at,
               COUNT(oi.id) as item_count
        FROM orders o
        LEFT JOIN order_items oi ON o.id = oi.order_id
        WHERE o.user_id = ?
        GROUP BY o.id
        ORDER BY o.created_at DESC
    ", [$_SESSION['user_id']]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log("User orders fetch error: " . $e->getMessage());
    $orders = [];
}
?>

<?php include __DIR__ . '/../includes/header.php'; ?>

<h2>My Orders</h2>

<?php if (empty($orders)): ?>
    <p>You have no orders yet.</p>
    <a href="<?= PRODUCTS_URL ?>/">Start Shopping</a>
<?php else: ?>
    <div class="order-list">
        <?php foreach ($orders as $order): ?>
            <div class="order-card">
                <h3>Order #<?= $order['id'] ?></h3>
                <p>Date: <?= date('Y-m-d H:i', strtotime($order['created_at'])) ?></p>
                <p>Status: <strong><?= !empty($order['status']) ? esc(ucfirst($order['status'])) : 'Processing' ?></strong></p>
                <p>Items: <?= $order['item_count'] ?> | Total: $<?= number_format($order['total_amount'], 2) ?></p>
                <!-- 可扩展：添加"View Details"链接 -->
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>