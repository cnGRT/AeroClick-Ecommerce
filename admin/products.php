<?php
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/admin_auth.php';
require_admin_auth();

if (!is_logged_in() || !is_admin()) {
    redirect(BASE_URL . '/');
}

// 处理库存更新
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    // CSRF 保护
    if (!validate_csrf($_POST['csrf_token'] ?? '')) {
        $_SESSION['error'] = 'Invalid security token.';
        redirect(ADMIN_URL . '/products.php');
    }
    
    $product_id = validate_int($_POST['product_id'], 1);
    $new_stock = max(0, validate_int($_POST['stock_quantity'], 0));
    
    if ($product_id) {
        try {
            $stmt = executeQuery("UPDATE products SET stock_quantity = ? WHERE id = ?", [$new_stock, $product_id]);
            $_SESSION['success'] = 'Stock updated successfully.';
            
            // 记录操作日志
            log_admin_action('update_stock', "Product ID: $product_id, New Stock: $new_stock");
        } catch (Exception $e) {
            error_log("Stock update error: " . $e->getMessage());
            $_SESSION['error'] = 'Failed to update stock.';
        }
    }
    redirect(ADMIN_URL . '/products.php');
}

// 获取所有产品（含分类）
try {
    $stmt = executeQuery("
        SELECT p.*, c.name AS category_name
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.id
        ORDER BY p.created_at DESC
    ");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log("Admin products fetch error: " . $e->getMessage());
    $products = [];
}
?>

<?php include __DIR__ . '/../includes/header.php'; ?>
<link rel="stylesheet" href="<?= ASSETS_URL ?>/css/admin.css">

<div class="admin-container">
    <h1>Product Inventory Management</h1>

    <?php if (!empty($_SESSION['success'])): ?>
        <div class="message success"><?= esc($_SESSION['success']) ?></div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    
    <?php if (!empty($_SESSION['error'])): ?>
        <div class="message error"><?= esc($_SESSION['error']) ?></div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <table class="inventory-table">
        <thead>
            <tr>
                <th>Product</th>
                <th>Category</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $p): ?>
                <tr>
                    <td><?= esc($p['title']) ?></td>
                    <td><?= esc($p['category_name'] ?? 'Uncategorized') ?></td>
                    <td>$<?= number_format($p['price'], 2) ?></td>
                    <td class="<?= $p['stock_quantity'] <= 5 ? 'low-stock' : '' ?>">
                        <?= $p['stock_quantity'] ?>
                        <?php if ($p['stock_quantity'] <= 5): ?>
                            <span style="color: #ff5555;">⚠️ Low Stock</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                            <input type="number" name="stock_quantity" value="<?= $p['stock_quantity'] ?>" min="0" size="5">
                            <button type="submit">Update</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>