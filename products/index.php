<?php
require_once __DIR__ . '/../includes/init.php';

// 检查数据库连接
if (!$pdo) {
    die("System temporarily unavailable. Please try again later.");
}

// 获取筛选参数（安全处理）
$filters = [
    'brand' => $_GET['brand'] ?? null,
    'connectivity' => $_GET['connectivity'] ?? null,
    'min_dpi' => is_numeric($_GET['min_dpi'] ?? null) ? (int)$_GET['min_dpi'] : null,
    'max_weight' => is_numeric($_GET['max_weight'] ?? null) ? (int)$_GET['max_weight'] : null,
    'hand_orientation' => $_GET['hand_orientation'] ?? null,
    'rgb' => isset($_GET['rgb']) ? true : null,
];

// 构建查询
$sql = "SELECT p.*, c.name AS category_name 
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.id
        WHERE 1=1";
$params = [];

if ($filters['brand']) {
    $sql .= " AND p.brand = ?";
    $params[] = $filters['brand'];
}
if ($filters['connectivity']) {
    $sql .= " AND p.connectivity = ?";
    $params[] = $filters['connectivity'];
}
if ($filters['min_dpi'] !== null) {
    $sql .= " AND p.dpi >= ?";
    $params[] = $filters['min_dpi'];
}
if ($filters['max_weight'] !== null) {
    $sql .= " AND p.weight_grams <= ?";
    $params[] = $filters['max_weight'];
}
if ($filters['hand_orientation']) {
    $sql .= " AND p.hand_orientation = ?";
    $params[] = $filters['hand_orientation'];
}
if ($filters['rgb'] !== null) {
    $sql .= " AND p.rgb_lighting = 1";
}

$sql .= " ORDER BY p.created_at DESC";

try {
    $stmt = executeQuery($sql, $params);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 获取品牌列表用于筛选下拉框（去重）
    $brandStmt = executeQuery("SELECT DISTINCT brand FROM products WHERE brand IS NOT NULL ORDER BY brand");
    $brands = $brandStmt->fetchAll(PDO::FETCH_COLUMN);
} catch (Exception $e) {
    $products = [];
    $brands = [];
    error_log("Product filter error: " . $e->getMessage());
}
?>

<?php include __DIR__ . '/../includes/header.php'; ?>

<h2>Game Mice Catalog</h2>

<!-- 筛选表单 -->
<form method="GET" class="filters">
    <div>
        <label>Brand:</label>
        <select name="brand">
            <option value="">All Brands</option>
            <?php foreach ($brands as $brand): ?>
                <option value="<?= esc($brand) ?>" <?= $filters['brand'] === $brand ? 'selected' : '' ?>>
                    <?= esc($brand) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div>
        <label>Connectivity:</label>
        <select name="connectivity">
            <option value="">Any</option>
            <option value="Wired" <?= $filters['connectivity'] === 'Wired' ? 'selected' : '' ?>>Wired</option>
            <option value="Wireless" <?= $filters['connectivity'] === 'Wireless' ? 'selected' : '' ?>>Wireless</option>
            <option value="Both" <?= $filters['connectivity'] === 'Both' ? 'selected' : '' ?>>Both</option>
        </select>
    </div>

    <div>
        <label>Min DPI:</label>
        <input type="number" name="min_dpi" value="<?= esc($filters['min_dpi'] ?? '') ?>" min="0">
    </div>

    <div>
        <label>Max Weight (g):</label>
        <input type="number" name="max_weight" value="<?= esc($filters['max_weight'] ?? '') ?>" min="0">
    </div>

    <div>
        <label>Hand Orientation:</label>
        <select name="hand_orientation">
            <option value="">Any</option>
            <option value="Right" <?= $filters['hand_orientation'] === 'Right' ? 'selected' : '' ?>>Right</option>
            <option value="Left" <?= $filters['hand_orientation'] === 'Left' ? 'selected' : '' ?>>Left</option>
            <option value="Ambidextrous" <?= $filters['hand_orientation'] === 'Ambidextrous' ? 'selected' : '' ?>>Ambidextrous</option>
        </select>
    </div>

    <div>
        <label>
            <input type="checkbox" name="rgb" value="1" <?= $filters['rgb'] ? 'checked' : '' ?>>
            RGB Lighting
        </label>
    </div>

    <button type="submit">Filter</button>
    <?php if (array_filter($filters)): ?>
        <a href="?">Clear Filters</a>
    <?php endif; ?>
</form>

<!-- 产品列表 -->
<div class="product-grid">
    <?php if (empty($products)): ?>
        <p>No products match your filters.</p>
    <?php else: ?>
        <?php foreach ($products as $product): ?>
            <div class="product-card">
                <?php if (!empty($product['image_url'])): ?>
                    <img src="<?= ASSETS_URL ?>/images/products/<?= esc($product['image_url']) ?>" 
                         alt="<?= esc($product['title']) ?>">
                <?php else: ?>
                    <div class="product-image-placeholder">
                        <span><?= esc($product['title']) ?></span>
                    </div>
                <?php endif; ?>
                <h3><?= esc($product['title']) ?></h3>
                <p class="price">$<?= number_format($product['price'], 2) ?></p>
                <p class="specs">
                    <?= esc($product['dpi']) ?> DPI • <?= esc($product['connectivity']) ?> • 
                    <?= esc($product['weight_grams']) ?>g • <?= esc($product['hand_orientation']) ?>
                </p>
                <a href="<?= PRODUCTS_URL ?>/view.php?id=<?= $product['id'] ?>">View Details</a>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
<a href="<?= PRODUCTS_URL ?>/export_csv.php" class="btn">Export All Products to CSV</a>
<?php include __DIR__ . '/../includes/footer.php'; ?>