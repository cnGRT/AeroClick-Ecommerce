<?php
require_once __DIR__ . '/../includes/init.php';

// 初始化对比数组
if (!isset($_SESSION['compare_ids'])) {
    $_SESSION['compare_ids'] = [];
}

// 处理"添加到对比"
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $new_ids = array_filter(array_map('intval', (array)$_GET['id']));
    foreach ($new_ids as $id) {
        if (!in_array($id, $_SESSION['compare_ids']) && count($_SESSION['compare_ids']) < 3) {
            $_SESSION['compare_ids'][] = $id;
        }
    }
    redirect(PRODUCTS_URL . '/compare.php');
}

// 处理"从对比中移除"
if (isset($_GET['remove'])) {
    $remove_id = (int)$_GET['remove'];
    $_SESSION['compare_ids'] = array_filter($_SESSION['compare_ids'], fn($id) => $id != $remove_id);
    redirect(PRODUCTS_URL . '/compare.php');
}

// 清空对比
if (isset($_GET['clear'])) {
    $_SESSION['compare_ids'] = [];
    redirect(PRODUCTS_URL . '/');
}

// 获取对比产品数据
$products = [];
if (!empty($_SESSION['compare_ids'])) {
    // 修复：完全使用参数化查询，避免 SQL 注入
    $placeholders = str_repeat('?,', count($_SESSION['compare_ids']) - 1) . '?';
    
    // 构建 FIELD 排序的参数
    $field_params = $_SESSION['compare_ids'];
    $all_params = array_merge($_SESSION['compare_ids'], $field_params);
    
    $sql = "SELECT p.*, c.name AS category_name 
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.id IN ($placeholders)
            ORDER BY FIELD(p.id, $placeholders)";
    
    try {
        $stmt = executeQuery($sql, $all_params);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Compare products error: " . $e->getMessage());
        $products = [];
    }
}
?>

<?php include __DIR__ . '/../includes/header.php'; ?>

<h2>Product Comparison</h2>

<?php if (empty($products)): ?>
    
    <p>Your comparison list is empty.</p>
    <a href="<?= PRODUCTS_URL ?>/">Browse Products</a>
<?php else: ?>
    <p><?= count($products) ?>/3 products selected</p>
    
    <a href="<?= PRODUCTS_URL ?>/">← Continue Shopping</a> |
    <a href="?clear=1" style="color: red;">Clear All</a>

    <div class="comparison-table">
        <table>
            <thead>
                <tr>
                    <th>Feature</th>
                    <?php foreach ($products as $product): ?>
                        <th>
                            <?= esc($product['title']) ?>
                            <br>
                            <a href="?remove=<?= $product['id'] ?>" style="font-size:0.8em; color:red;">Remove</a>
                        </th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <tr><td>Price</td><?php foreach ($products as $p): ?><td>$<?= number_format($p['price'], 2) ?></td><?php endforeach; ?></tr>
                <tr><td>Brand</td><?php foreach ($products as $p): ?><td><?= esc($p['brand']) ?></td><?php endforeach; ?></tr>
                <tr><td>DPI</td><?php foreach ($products as $p): ?><td><?= esc($p['dpi']) ?></td><?php endforeach; ?></tr>
                <tr><td>Sensor Type</td><?php foreach ($products as $p): ?><td><?= esc($p['sensor_type']) ?></td><?php endforeach; ?></tr>
                <tr><td>Connectivity</td><?php foreach ($products as $p): ?><td><?= esc($p['connectivity']) ?></td><?php endforeach; ?></tr>
                <tr><td>Weight (g)</td><?php foreach ($products as $p): ?><td><?= esc($p['weight_grams']) ?></td><?php endforeach; ?></tr>
                <tr><td>Hand Orientation</td><?php foreach ($products as $p): ?><td><?= esc($p['hand_orientation']) ?></td><?php endforeach; ?></tr>
                <tr><td>Button Count</td><?php foreach ($products as $p): ?><td><?= esc($p['button_count']) ?></td><?php endforeach; ?></tr>
                <tr><td>RGB Lighting</td><?php foreach ($products as $p): ?><td><?= $p['rgb_lighting'] ? 'Yes' : 'No' ?></td><?php endforeach; ?></tr>
                <tr><td>Stock</td><?php foreach ($products as $p): ?><td><?= $p['stock_quantity'] > 0 ? 'In Stock' : 'Out of Stock' ?></td><?php endforeach; ?></tr>
                <tr>
                    <td>Action</td>
                    <?php foreach ($products as $p): ?>
                        <td>
                            <a href="<?= PRODUCTS_URL ?>/view.php?id=<?= $p['id'] ?>">View Details</a><br>
                            <?php if ($p['stock_quantity'] > 0): ?>
                                <form action="<?= CART_URL ?>/add_to_cart.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                    <button type="submit" style="font-size:0.9em;">Add to Cart</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    <?php endforeach; ?>
                </tr>
            </tbody>
        </table>
    </div>
     <!-- 引入开源 CSV 导出脚本 -->
    <script src="<?= ASSETS_URL ?>/js/table-to-csv.js"></script>
    <p>
        <button 
    onclick="exportTableToCSV('aeroclick-comparison.csv')" 
    class="export-btn"
>
    Export to CSV
</button>
    </p>
<?php endif; ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>