// 在 products/ 目录下创建 export_csv.php
<?php
require_once __DIR__ . '/../includes/init.php';

// 获取所有产品数据
try {
    $stmt = executeQuery("SELECT * FROM products");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    die("Error fetching products: " . $e->getMessage());
}

// 设置响应头以下载CSV文件
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="aeroclick-products.csv"');

// 创建文件句柄
$output = fopen('php://output', 'w');

// 写入CSV头部
if (!empty($products)) {
    fputcsv($output, array_keys($products[0]));
    
    // 写入数据行
    foreach ($products as $product) {
        fputcsv($output, $product);
    }
}

fclose($output);
exit();
?>