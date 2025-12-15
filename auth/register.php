<?php
// auth/register.php
require_once __DIR__ . '/../includes/init.php';

// 如果已经登录，重定向到首页
if (is_logged_in()) {
    redirect(BASE_URL . '/');
}

$error = $_SESSION['error'] ?? null;
unset($_SESSION['error']);
?>

<?php include __DIR__ . '/../includes/header.php'; ?>

<div class="auth-container">
    <h2>Create Account</h2>
    
    <?php if ($error): ?>
        <div class="message error"><?= esc($error) ?></div>
    <?php endif; ?>
    
    <form method="POST" action="process_register.php">
    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
    
    <div class="form-group">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" value="<?= esc($_POST['username'] ?? '') ?>" required minlength="3" maxlength="50">
    </div>
    
    <div class="form-group">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?= esc($_POST['email'] ?? '') ?>" required>
    </div>
    
    <div class="form-group">
        <label for="phone">Phone Number:</label>
        <input type="tel" id="phone" name="phone" value="<?= esc($_POST['phone'] ?? '') ?>" required pattern="[0-9]{10,15}" placeholder="Enter 10-15 digit phone number">
    </div>
    
    <div class="form-group">
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required minlength="6">
    </div>
    
    <div class="form-group">
        <label for="confirm_password">Confirm Password:</label>
        <input type="password" id="confirm_password" name="confirm_password" required minlength="6">
    </div>
    
    <button type="submit" class="btn">Register</button>
</form>
    
    <p>Already have an account? <a href="<?= AUTH_URL ?>/login.php">Login here</a></p>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>