<?php
// forum_view.php
require_once __DIR__ . '/includes/init.php';

// 检查登录
if (!is_logged_in()) {
    $_SESSION['error'] = 'Please login to view forum posts.';
    redirect(AUTH_URL . '/login.php');
}

// 获取帖子ID
$post_id = intval($_GET['id'] ?? 0);
if (!$post_id) {
    $_SESSION['error'] = 'Post not found.';
    redirect(BASE_URL . '/forum.php');
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
        if ($_SESSION['email'] === '1459321941@qq.com') {
            $is_admin = true;
        }
    } catch (Exception $e) {
        error_log("Admin check error: " . $e->getMessage());
    }
}

// 处理删除操作
if (isset($_GET['delete_reply']) && $is_admin) {
    $reply_id = intval($_GET['delete_reply']);
    
    if ($reply_id > 0) {
        try {
            // 首先获取回复所属的帖子ID
            $stmt = $wp_pdo->prepare("SELECT post_id FROM forum_replies WHERE id = ?");
            $stmt->execute([$reply_id]);
            $reply = $stmt->fetch();
            
            if ($reply) {
                // 删除回复
                $delete_stmt = $wp_pdo->prepare("DELETE FROM forum_replies WHERE id = ?");
                $delete_stmt->execute([$reply_id]);
                
                // 更新帖子的回复数
                $update_stmt = $wp_pdo->prepare("UPDATE forum_posts SET replies = replies - 1 WHERE id = ?");
                $update_stmt->execute([$reply['post_id']]);
                
                $_SESSION['success'] = 'Reply deleted successfully!';
                header("Location: forum_view.php?id=" . $post_id);
                exit();
            }
        } catch (Exception $e) {
            error_log("Reply deletion error: " . $e->getMessage());
            $_SESSION['error'] = 'Failed to delete reply: ' . $e->getMessage();
        }
    }
}

// 处理新回复
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_reply'])) {
    $content = trim($_POST['content']);
    
    if (strlen($content) >= 5) {
        if ($wp_pdo) {
            try {
                // 检查 forum_replies 表是否存在
                $check_table = $wp_pdo->query("SHOW TABLES LIKE 'forum_replies'");
                if (!$check_table->fetch()) {
                    // 创建回复表
                    $create_table = "CREATE TABLE forum_replies (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        post_id INT NOT NULL,
                        user_id BIGINT UNSIGNED NOT NULL,
                        content TEXT NOT NULL,
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
                    $wp_pdo->exec($create_table);
                }
                
                // 插入新回复
                $stmt = $wp_pdo->prepare("INSERT INTO forum_replies (post_id, user_id, content) VALUES (?, ?, ?)");
                $stmt->execute([$post_id, $_SESSION['user_id'], $content]);
                
                // 更新帖子的回复数和最后回复时间
                $update_stmt = $wp_pdo->prepare("UPDATE forum_posts SET replies = replies + 1, last_reply_at = NOW() WHERE id = ?");
                $update_stmt->execute([$post_id]);
                
                $_SESSION['success'] = 'Reply posted successfully!';
                header("Location: forum_view.php?id=" . $post_id);
                exit();
                
            } catch (Exception $e) {
                error_log("Reply creation error: " . $e->getMessage());
                $_SESSION['error'] = 'Failed to post reply. Error: ' . $e->getMessage();
            }
        } else {
            $_SESSION['error'] = 'Database not available. Cannot post reply.';
        }
    } else {
        $_SESSION['error'] = 'Reply must be at least 5 characters.';
    }
}

// 获取帖子详情
$post = null;
$replies = [];
$current_user_info = ['first_name' => 'User', 'last_name' => '', 'email' => ''];

if ($wp_pdo) {
    try {
        // 获取帖子详情及作者信息
        $post_stmt = $wp_pdo->prepare("
            SELECT fp.*, wps.first_name, wps.last_name, wps.email 
            FROM forum_posts fp 
            LEFT JOIN wpri_fc_subscribers wps ON fp.user_id = wps.id 
            WHERE fp.id = ?
        ");
        $post_stmt->execute([$post_id]);
        $post = $post_stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$post) {
            $_SESSION['error'] = 'Post not found.';
            redirect(BASE_URL . '/forum.php');
        }
        
        // 增加浏览量
        $view_stmt = $wp_pdo->prepare("UPDATE forum_posts SET views = views + 1 WHERE id = ?");
        $view_stmt->execute([$post_id]);
        
        // 获取当前用户信息
        $user_stmt = $wp_pdo->prepare("SELECT first_name, last_name, email FROM wpri_fc_subscribers WHERE id = ?");
        $user_stmt->execute([$_SESSION['user_id']]);
        $current_user_info = $user_stmt->fetch(PDO::FETCH_ASSOC) ?: $current_user_info;
        
        // 获取回复
        $reply_stmt = $wp_pdo->prepare("
            SELECT fr.*, wps.first_name, wps.last_name 
            FROM forum_replies fr 
            LEFT JOIN wpri_fc_subscribers wps ON fr.user_id = wps.id 
            WHERE fr.post_id = ? 
            ORDER BY fr.created_at ASC
        ");
        $reply_stmt->execute([$post_id]);
        $replies = $reply_stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (Exception $e) {
        error_log("Forum view error: " . $e->getMessage());
        $_SESSION['error'] = 'Error loading post: ' . $e->getMessage();
    }
} else {
    $_SESSION['error'] = 'Database connection failed.';
}

$success = $_SESSION['success'] ?? null;
$error = $_SESSION['error'] ?? null;
unset($_SESSION['success'], $_SESSION['error']);
?>

<?php include __DIR__ . '/includes/header.php'; ?>

<div class="forum-view-container">
    <!-- 返回按钮和面包屑导航 -->
    <div class="forum-nav" style="margin-bottom: 20px;">
        <a href="forum.php" class="btn" style="background: #252525; color: #00d9ff; text-decoration: none; padding: 8px 16px; border-radius: 5px; border: 1px solid #333; display: inline-flex; align-items: center; gap: 8px;">
            ← Back to Forum
        </a>
        <span style="color: #888; margin: 0 10px;">/</span>
        <span style="color: #00d9ff; font-weight: bold;">Discussion</span>
    </div>
    
    <?php if ($success): ?>
        <div class="message success"><?= esc($success) ?></div>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <div class="message error"><?= esc($error) ?></div>
    <?php endif; ?>
    
    <?php if ($post): ?>
        <!-- 主帖子 -->
        <div class="main-post">
            <div class="post-header">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px;">
                    <div>
                        <h1 style="color: #00d9ff; margin: 0 0 10px 0;"><?= esc($post['title']) ?></h1>
                        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 15px;">
                            <span class="post-category" style="background: linear-gradient(135deg, #00d9ff 0%, #008cff 100%); color: #0f0f0f; padding: 4px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: bold;">
                                <?= esc(ucfirst($post['category'])) ?>
                            </span>
                            <?php if ($is_admin): ?>
                                <a href="forum.php?delete_post=<?= $post['id'] ?>" 
                                   onclick="return confirm('Are you sure you want to delete this entire post? This will also delete all replies.');"
                                   class="delete-btn" style="color: #ff5555; text-decoration: none; padding: 4px 10px; background: rgba(255, 85, 85, 0.1); border-radius: 5px; border: 1px solid #ff5555; font-size: 0.8rem;">
                                    🗑️ Delete Post
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="post-author-info" style="display: flex; justify-content: space-between; align-items: center; background: #252525; padding: 15px; border-radius: 8px; border: 1px solid #333; margin-bottom: 20px;">
                    <div>
                        <strong style="color: #00d9ff; display: block;"><?= esc($post['first_name'] . ' ' . $post['last_name']) ?></strong>
                        <small style="color: #888;"><?= esc($post['email']) ?></small>
                    </div>
                    <div style="text-align: right;">
                        <div style="color: #888; font-size: 0.9rem;">
                            Posted on <?= date('F j, Y g:i A', strtotime($post['created_at'])) ?>
                        </div>
                        <?php if ($post['last_reply_at']): ?>
                            <div style="color: #888; font-size: 0.8rem; margin-top: 5px;">
                                Last reply: <?= date('M j, Y', strtotime($post['last_reply_at'])) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="post-content" style="background: #252525; padding: 25px; border-radius: 10px; border: 1px solid #333; margin-bottom: 25px; line-height: 1.8; font-size: 1.1rem; color: #e0e0e0; border-left: 4px solid #00d9ff;">
                <?= nl2br(esc($post['content'])) ?>
            </div>
            
            <div class="post-stats" style="display: flex; justify-content: space-between; align-items: center; background: #1a1a1a; padding: 15px; border-radius: 8px; border: 1px solid #333; margin-bottom: 30px;">
                <div style="display: flex; gap: 20px;">
                    <span style="color: #00d9ff; font-weight: bold;">👁️ <?= $post['views'] ?> views</span>
                    <span style="color: #00d9ff; font-weight: bold;">💬 <?= $post['replies'] ?> replies</span>
                </div>
                <div style="color: #888; font-size: 0.9rem;">
                    Updated: <?= date('M j, Y g:i A', strtotime($post['updated_at'])) ?>
                </div>
            </div>
        </div>
        
        <!-- 回复部分 -->
        <div class="replies-section">
            <h2 style="color: #00d9ff; margin-bottom: 25px; border-bottom: 2px solid #00d9ff; padding-bottom: 10px;">
                Replies (<?= count($replies) ?>)
            </h2>
            
            <?php if (empty($replies)): ?>
                <div class="no-replies" style="text-align: center; padding: 40px; background: #1a1a1a; border: 2px dashed #333; border-radius: 10px; color: #888; margin-bottom: 30px;">
                    <p>No replies yet. Be the first to respond!</p>
                </div>
            <?php else: ?>
                <?php foreach ($replies as $reply): ?>
                    <div class="reply-card" style="background: #1a1a1a; border: 1px solid #333; border-radius: 8px; padding: 20px; margin-bottom: 15px; position: relative;">
                        <?php if ($is_admin): ?>
                            <a href="?id=<?= $post_id ?>&delete_reply=<?= $reply['id'] ?>" 
                               onclick="return confirm('Are you sure you want to delete this reply?');"
                               style="position: absolute; top: 10px; right: 10px; color: #ff5555; text-decoration: none; font-size: 0.7rem; padding: 3px 6px; background: rgba(255, 85, 85, 0.1); border-radius: 4px; border: 1px solid #ff5555;">
                                × Delete
                            </a>
                        <?php endif; ?>
                        
                        <div class="reply-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                            <div>
                                <strong style="color: #00d9ff; display: block;"><?= esc($reply['first_name'] . ' ' . $reply['last_name']) ?></strong>
                                <small style="color: #888;">Member</small>
                            </div>
                            <div style="color: #888; font-size: 0.9rem;">
                                <?= date('F j, Y g:i A', strtotime($reply['created_at'])) ?>
                            </div>
                        </div>
                        
                        <div class="reply-content" style="color: #ccc; line-height: 1.7; padding: 15px; background: #252525; border-radius: 5px; border-left: 3px solid #444;">
                            <?= nl2br(esc($reply['content'])) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
            
            <!-- 回复表单 -->
            <div class="reply-form" style="margin-top: 40px; padding: 25px; background: #1a1a1a; border-radius: 10px; border: 1px solid #333;">
                <h3 style="color: #00d9ff; margin-bottom: 20px;">Post a Reply</h3>
                <form method="POST">
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 8px; color: #00d9ff; font-weight: bold;">Your Reply:</label>
                        <textarea name="content" required placeholder="Write your reply here..." 
                                  rows="6" style="width: 100%; padding: 12px; background: #252525; border: 1px solid #333; color: #e0e0e0; border-radius: 5px; font-family: inherit; font-size: 1rem; resize: vertical;"></textarea>
                        <small style="color: #888; display: block; margin-top: 5px;">Minimum 5 characters required.</small>
                    </div>
                    
                    <div style="display: flex; align-items: center; gap: 15px;">
                        <button type="submit" name="new_reply" class="btn" style="background: linear-gradient(135deg, #00d9ff 0%, #008cff 100%); color: white; padding: 12px 25px; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; font-size: 1rem;">
                            Post Reply
                        </button>
                        <div style="color: #888; font-size: 0.9rem;">
                            Replying as: <strong style="color: #00d9ff;"><?= esc($current_user_info['first_name'] . ' ' . $current_user_info['last_name']) ?></strong>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    <?php else: ?>
        <div class="post-not-found" style="text-align: center; padding: 50px; background: #1a1a1a; border: 2px dashed #333; border-radius: 10px; color: #888;">
            <p style="font-size: 1.2rem;">Post not found or has been deleted.</p>
            <a href="forum.php" class="btn" style="display: inline-block; margin-top: 20px; background: linear-gradient(135deg, #00d9ff 0%, #008cff 100%); color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">
                Back to Forum
            </a>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>