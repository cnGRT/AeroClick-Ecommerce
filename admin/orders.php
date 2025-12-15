<?php
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/admin_auth.php';
require_admin_auth();
log_admin_action('view_orders'); // 或 view_users

if (!is_logged_in() || !is_admin()) {
    redirect(BASE_URL . '/');
}

try {
    // 获取所有订单（含用户邮箱）
    $stmt = executeQuery("
        SELECT o.id, o.total_amount, o.status, o.created_at,
               u.email as user_email,
               COUNT(oi.id) as item_count
        FROM orders o
        JOIN users u ON o.user_id = u.id
        LEFT JOIN order_items oi ON o.id = oi.order_id
        GROUP BY o.id
        ORDER BY o.created_at DESC
    ");
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log("Admin orders fetch error: " . $e->getMessage());
    $orders = [];
}
?>

<?php include __DIR__ . '/../includes/header.php'; ?>
<link rel="stylesheet" href="<?= ASSETS_URL ?>/css/admin.css">

<div class="admin-container">
    <h1>All Orders</h1>

    <?php if (empty($orders)): ?>
        <p>No orders yet.</p>
    <?php else: ?>
        <table class="inventory-table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>User</th>
                    <th>Date</th>
                    <th>Items</th>
                    <th>Total</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td>#<?= $order['id'] ?></td>
                        <td><?= esc($order['user_email']) ?></td>
                        <td><?= date('Y-m-d', strtotime($order['created_at'])) ?></td>
                        <td><?= $order['item_count'] ?></td>
                        <td>$<?= number_format($order['total_amount'], 2) ?></td>
                        <td><?= ucfirst(esc($order['status'])) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>