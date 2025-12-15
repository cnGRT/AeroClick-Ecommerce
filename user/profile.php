<?php
require_once __DIR__ . '/../includes/init.php';

if (!is_logged_in()) {
    redirect(AUTH_URL . '/login.php');
}

$current_user = get_logged_in_user();
if (!$current_user) {
    session_destroy();
    redirect(AUTH_URL . '/login.php');
}

$success = $_SESSION['success'] ?? null;
$error = $_SESSION['error'] ?? null;
unset($_SESSION['success'], $_SESSION['error']);
?>

<?php include __DIR__ . '/../includes/header.php'; ?>

<h2>My Profile</h2>

<?php if ($success): ?>
    <div class="message success"><?= esc($success) ?></div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="message error"><?= esc($error) ?></div>
<?php endif; ?>

<div class="profile-info">
    <div class="profile-card">
        <h3>Account Information</h3>
        <p><strong>Username:</strong> <?= esc($current_user['username']) ?></p>
        <p><strong>Email:</strong> <?= esc($current_user['email']) ?></p>
        <p><strong>Role:</strong> <?= ucfirst(esc($current_user['role'])) ?></p>
        <p><strong>Member since:</strong> <?= date('F j, Y', strtotime($current_user['created_at'])) ?></p>
    </div>

    <div class="profile-actions">
        <h3>Quick Actions</h3>
        <ul>
            <li><a href="<?= USER_URL ?>/orders.php">View Order History</a></li>
            <li><a href="<?= CART_URL ?>/">View Shopping Cart</a></li>
            <li><a href="<?= AUTH_URL ?>/logout.php" style="color: #ff5555;">Logout</a></li>
        </ul>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>