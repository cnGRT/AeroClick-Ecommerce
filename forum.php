<?php
// forum.php
require_once __DIR__ . '/includes/init.php';

// 检查登录
if (!is_logged_in()) {
    $_SESSION['error'] = 'Please login to access the forum.';
    redirect(AUTH_URL . '/login.php');
}

// 数据库配置
$db_host = 'sql301.infinityfree.com';
$db_username = 'if0_38341067';
$db_password = 'Grtnb137';
$db_name = 'if0_38341067_wp221';

// 连接 WordPress 数据库
try {
    $wp_pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_username, $db_password);
    $wp_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $wp_pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    error_log("WordPress database connection error: " . $e->getMessage());
    $wp_pdo = null;
    $_SESSION['error'] = 'Forum database connection issue. Please try again later.';
}

// 检查当前用户是否是管理员
$is_admin = false;
if ($wp_pdo && isset($_SESSION['email'])) {
    try {
        // 检查是否是管理员邮箱
        if ($_SESSION['email'] === '1459321941@qq.com') {
            $is_admin = true;
        }
    } catch (Exception $e) {
        error_log("Admin check error: " . $e->getMessage());
    }
}

// 处理删除帖子请求
if (isset($_GET['delete_post']) && $is_admin) {
    $post_id = intval($_GET['delete_post']);
    
    if ($post_id > 0) {
        try {
            // 开始事务
            $wp_pdo->beginTransaction();
            
            // 首先删除该帖子的所有回复
            $delete_replies = $wp_pdo->prepare("DELETE FROM forum_replies WHERE post_id = ?");
            $delete_replies->execute([$post_id]);
            
            // 然后删除帖子本身
            $delete_post = $wp_pdo->prepare("DELETE FROM forum_posts WHERE id = ?");
            $delete_post->execute([$post_id]);
            
            $wp_pdo->commit();
            
            $_SESSION['success'] = 'Post deleted successfully!';
            header("Location: " . BASE_URL . "/forum.php");
            exit();
            
        } catch (Exception $e) {
            if ($wp_pdo->inTransaction()) {
                $wp_pdo->rollback();
            }
            error_log("Post deletion error: " . $e->getMessage());
            $_SESSION['error'] = 'Failed to delete post: ' . $e->getMessage();
        }
    }
}

// 处理新帖子
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_post'])) {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $category = $_POST['category'] ?? 'general';
    
    if (strlen($title) >= 5 && strlen($content) >= 10) {
        if ($wp_pdo) {
            try {
                // 检查 forum_posts 表是否存在
                $check_table = $wp_pdo->query("SHOW TABLES LIKE 'forum_posts'");
                if (!$check_table->fetch()) {
                    // 创建表格
                    $create_table = "CREATE TABLE forum_posts (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        user_id BIGINT UNSIGNED NOT NULL,
                        title VARCHAR(255) NOT NULL,
                        content TEXT NOT NULL,
                        category VARCHAR(50) DEFAULT 'general',
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                        views INT DEFAULT 0,
                        replies INT DEFAULT 0,
                        last_reply_at TIMESTAMP NULL
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
                    $wp_pdo->exec($create_table);
                }
                
                // 插入新帖子
                $stmt = $wp_pdo->prepare("INSERT INTO forum_posts (user_id, title, content, category) VALUES (?, ?, ?, ?)");
                $stmt->execute([$_SESSION['user_id'], $title, $content, $category]);
                
                $_SESSION['success'] = 'Post created successfully!';
                header("Location: " . BASE_URL . "/forum.php");
                exit();
                
            } catch (Exception $e) {
                error_log("Forum post creation error: " . $e->getMessage());
                $_SESSION['error'] = 'Failed to create post. Error: ' . $e->getMessage();
            }
        } else {
            $_SESSION['error'] = 'Database not available. Cannot create post.';
        }
    } else {
        $_SESSION['error'] = 'Title must be at least 5 characters and content at least 10 characters.';
    }
}

// 获取当前用户信息
$current_user = ['first_name' => 'User', 'last_name' => '', 'email' => ''];
if ($wp_pdo) {
    try {
        $user_stmt = $wp_pdo->prepare("SELECT first_name, last_name, email FROM wpri_fc_subscribers WHERE id = ?");
        $user_stmt->execute([$_SESSION['user_id']]);
        $current_user = $user_stmt->fetch(PDO::FETCH_ASSOC) ?: $current_user;
    } catch (Exception $e) {
        error_log("User info error: " . $e->getMessage());
    }
}

// 获取帖子列表
$page = max(1, intval($_GET['page'] ?? 1));
$per_page = 10;
$offset = ($page - 1) * $per_page;

$posts = [];
$total_pages = 1;
$total_posts = 0;

if ($wp_pdo) {
    try {
        // 检查表格是否存在
        $check_table = $wp_pdo->query("SHOW TABLES LIKE 'forum_posts'");
        $table_exists = $check_table->fetch();
        
        if ($table_exists) {
            // 获取总帖子数
            $count_stmt = $wp_pdo->query("SELECT COUNT(*) as total FROM forum_posts");
            $total_result = $count_stmt->fetch(PDO::FETCH_ASSOC);
            $total_posts = $total_result['total'] ?? 0;
            $total_pages = ceil($total_posts / $per_page);
            
            // 如果没有任何帖子，插入一些示例帖子
            if ($total_posts == 0) {
                // 插入示例帖子
                $sample_posts = [
                    [1, 'Welcome to AeroClick Forum!', 'This is our community forum. Feel free to start discussions about gaming mice, share tips, and ask questions.', 'general'],
                    [2, 'Best gaming mouse for FPS?', 'Looking for recommendations for competitive FPS gaming. Currently using a heavy mouse but want something lighter.', 'gaming'],
                    [3, 'Wireless mouse battery issues', 'My wireless mouse only lasts 8 hours on full charge. Is this normal?', 'technical'],
                    [1, 'Mouse sensitivity settings', 'What DPI settings do pro gamers use?', 'gaming'],
                    [4, 'Ergonomic mouse recommendations', 'Need suggestions for ergonomic mice for long work/gaming sessions.', 'health'],
                    [5, 'Mouse feet replacement guide', 'How to replace mouse feet for better glide.', 'guides'],
                    [2, 'RGB Lighting Customization', 'Share your best RGB lighting profiles! Looking for inspiration.', 'customization'],
                    [3, 'Software Compatibility', 'The configuration software crashes on Windows 11. Any fixes?', 'technical'],
                    [1, 'Mouse Pad Recommendations', 'What mouse pads work best with high-DPI sensors?', 'accessories'],
                    [4, 'Left-handed Mouse Options', 'As a left-handed gamer, what are the best options?', 'gaming']
                ];
                
                foreach ($sample_posts as $post) {
                    $stmt = $wp_pdo->prepare("INSERT INTO forum_posts (user_id, title, content, category) VALUES (?, ?, ?, ?)");
                    $stmt->execute($post);
                }
                
                // 重新获取总帖子数
                $count_stmt = $wp_pdo->query("SELECT COUNT(*) as total FROM forum_posts");
                $total_result = $count_stmt->fetch(PDO::FETCH_ASSOC);
                $total_posts = $total_result['total'] ?? 10;
                $total_pages = ceil($total_posts / $per_page);
            }
            
            // 获取帖子及作者信息
            $sql = "SELECT fp.*, wps.first_name, wps.last_name 
                    FROM forum_posts fp 
                    LEFT JOIN wpri_fc_subscribers wps ON fp.user_id = wps.id 
                    ORDER BY fp.created_at DESC 
                    LIMIT :limit OFFSET :offset";
            
            $stmt = $wp_pdo->prepare($sql);
            $stmt->bindValue(':limit', $per_page, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        }
        
    } catch (Exception $e) {
        error_log("Forum posts fetch error: " . $e->getMessage());
        $_SESSION['error'] = 'Error loading forum posts: ' . $e->getMessage();
    }
}

$success = $_SESSION['success'] ?? null;
$error = $_SESSION['error'] ?? null;
unset($_SESSION['success'], $_SESSION['error']);
?>

<?php include __DIR__ . '/includes/header.php'; ?>

<div class="forum-container">
    <h2>Community Forum</h2>
    
    <?php if ($success): ?>
        <div class="message success"><?= esc($success) ?></div>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <div class="message error"><?= esc($error) ?></div>
    <?php endif; ?>
    
    <!-- 管理员提示 -->
    <?php if ($is_admin): ?>
        <div class="admin-notice" style="background: rgba(0, 217, 255, 0.1); border: 1px solid #00d9ff; color: #00d9ff; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
            <strong>👑 Administrator Mode:</strong> You can delete any post.
        </div>
    <?php endif; ?>
    
    <!-- 欢迎信息 -->
    <div class="welcome-message">
        <p><strong>Welcome, <?= esc($current_user['first_name'] ?? 'User') ?>!</strong> Share your thoughts and connect with other gamers.</p>
    </div>
    
    <!-- 新帖子表单 -->
    <div class="new-post-form">
        <h3>Create New Post</h3>
        <form method="POST">
            <div class="form-group">
                <label for="title">Title:</label>
                <input type="text" id="title" name="title" required maxlength="255" placeholder="Enter post title" 
                       value="<?= esc($_POST['title'] ?? '') ?>">
            </div>
            
            <div class="form-group">
                <label for="category">Category:</label>
                <select id="category" name="category">
                    <option value="general" <?= ($_POST['category'] ?? '') === 'general' ? 'selected' : '' ?>>General Discussion</option>
                    <option value="gaming" <?= ($_POST['category'] ?? '') === 'gaming' ? 'selected' : '' ?>>Gaming</option>
                    <option value="technical" <?= ($_POST['category'] ?? '') === 'technical' ? 'selected' : '' ?>>Technical Support</option>
                    <option value="reviews" <?= ($_POST['category'] ?? '') === 'reviews' ? 'selected' : '' ?>>Product Reviews</option>
                    <option value="tips" <?= ($_POST['category'] ?? '') === 'tips' ? 'selected' : '' ?>>Tips & Tricks</option>
                    <option value="help" <?= ($_POST['category'] ?? '') === 'help' ? 'selected' : '' ?>>Help & Support</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="content">Content:</label>
                <textarea id="content" name="content" required placeholder="Share your thoughts..." rows="6"><?= esc($_POST['content'] ?? '') ?></textarea>
            </div>
            
            <button type="submit" name="new_post" class="btn">Create Post</button>
        </form>
    </div>
    
    <!-- 帖子列表 -->
    <div class="posts-list">
        <h3>Recent Discussions <?= $total_posts > 0 ? '(' . $total_posts . ' posts)' : '' ?></h3>
        
        <?php if (empty($posts)): ?>
            <div class="no-posts">
                <p>No posts found. Be the first to start a discussion!</p>
            </div>
        <?php else: ?>
            <?php foreach ($posts as $post): ?>
                <div class="post-card">
                    <div class="post-header">
                        <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                            <h4 class="post-title"><?= esc($post['title']) ?></h4>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <span class="post-category"><?= esc(ucfirst($post['category'])) ?></span>
                                <?php if ($is_admin): ?>
                                    <a href="?delete_post=<?= $post['id'] ?>" 
                                       onclick="return confirm('Are you sure you want to delete this post? This action cannot be undone.');"
                                       style="color: #ff5555; text-decoration: none; font-size: 0.8rem; padding: 3px 8px; background: rgba(255, 85, 85, 0.1); border-radius: 4px; border: 1px solid #ff5555;">
                                        🗑️ Delete
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="post-content">
                        <?= nl2br(esc(substr($post['content'], 0, 200))) ?><?= strlen($post['content']) > 200 ? '...' : '' ?>
                    </div>
                    
                    <div class="post-footer">
                        <div class="post-meta">
                            <span class="post-author">By <?= esc($post['first_name'] . ' ' . $post['last_name']) ?></span>
                            <span class="post-date">📅 <?= date('M j, Y H:i', strtotime($post['created_at'])) ?></span>
                            <span class="post-stats">💬 <?= $post['replies'] ?> replies</span>
                            <span class="post-views">👁️ <?= $post['views'] ?> views</span>
                        </div>
                        <a href="forum_view.php?id=<?= $post['id'] ?>" class="btn">View Discussion</a>
                    </div>
                </div>
            <?php endforeach; ?>
            
            <!-- 分页 -->
            <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?= $page-1 ?>">« Previous</a>
                    <?php endif; ?>
                    
                    <?php 
                    $start_page = max(1, $page - 2);
                    $end_page = min($total_pages, $page + 2);
                    for ($i = $start_page; $i <= $end_page; $i++): ?>
                        <a href="?page=<?= $i ?>" class="<?= $i == $page ? 'active' : '' ?>"><?= $i ?></a>
                    <?php endfor; ?>
                    
                    <?php if ($page < $total_pages): ?>
                        <a href="?page=<?= $page+1 ?>">Next »</a>
                    <?php endif; ?>
                    
                    <span style="color: #888; margin-left: 15px;">Page <?= $page ?> of <?= $total_pages ?></span>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>