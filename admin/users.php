<?php
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/admin_auth.php';
require_admin_auth();
log_admin_action('view_users'); 

if (!is_logged_in() || !is_admin()) {
    redirect(BASE_URL . '/');
}

try {
    // 获取所有用户
    $stmt = executeQuery("SELECT id, username, email, phone,role, created_at, status FROM users ORDER BY created_at DESC");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log("Admin users fetch error: " . $e->getMessage());
    $users = [];
}
?>

<?php include __DIR__ . '/../includes/header.php'; ?>
<link rel="stylesheet" href="<?= ASSETS_URL ?>/css/admin.css">

<div class="admin-container">
    <h1>User Management</h1>

    <table class="inventory-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Role</th>
                <th>Status</th>
                <th>Joined</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= $user['id'] ?></td>
                    <td><?= esc($user['username']) ?></td>
                    <td><?= esc($user['email']) ?></td>
                    <td><?= esc($user['phone']) ?></td>
                    <td><?= ucfirst(esc($user['role'])) ?></td>
                    <td><?= ucfirst(esc($user['status'])) ?></td>
                    <td><?= date('Y-m-d', strtotime($user['created_at'])) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>