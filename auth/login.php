<?php
require_once __DIR__ . '/../includes/init.php';

// 如果已经登录，重定向到首页
if (is_logged_in()) {
    redirect(BASE_URL . '/');
}

$error = $_SESSION['error'] ?? null;
$success = $_SESSION['success'] ?? null;
unset($_SESSION['error'], $_SESSION['success']);
?>

<?php include __DIR__ . '/../includes/header.php'; ?>

<div class="auth-container">
    <h2>Login to AeroClick</h2>
    
    <?php if ($success): ?>
        <div class="message success"><?= esc($success) ?></div>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <div class="message error"><?= esc($error) ?></div>
    <?php endif; ?>
    
    <form method="POST" action="process_login.php">
        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
        
        <div class="form-group">
            <label for="username">Email or Phone Number:</label>
            <input type="text" id="username" name="username" value="<?= esc($_POST['username'] ?? '') ?>" required 
                   placeholder="Enter your email or phone number">
        </div>
        
        <button type="submit" class="btn">Login</button>
    </form>
    
    <p>Don't have an account? <a href="<?= AUTH_URL ?>/register.php">Register here</a></p>
    
    <div style="margin-top: 20px; padding: 15px; background: #f8f9fa; border-radius: 4px;">
        <h4>Demo Accounts:</h4>
        <p><strong>Admin Account:</strong> 1459321941@qq.com</p>
        <p><strong>User Account:</strong> hty1326547@163.com</p>
        <p><strong>User Phone:</strong> 13738053838</p>
        <p><em>No password required - just enter email/phone and click Login</em></p>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>