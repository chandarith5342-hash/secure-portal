<?php
require 'header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require 'pdo.php';
$pdo  = getDB();
$stmt = $pdo->prepare('SELECT username, email, created_at FROM users WHERE id = ?');
$stmt->execute(array($_SESSION['user_id']));
$user = $stmt->fetch();
?>
<section class="dashboard">
    <header class="dash-header">
        <div>
            <p class="eyebrow">Welcome back</p>
            <h1 class="dash-title">Hello, <?php echo htmlspecialchars($user['username']); ?>!</h1>
        </div>
        <a href="logout.php" class="btn btn-outline">Sign Out</a>
    </header>
    <div class="dash-grid">
        <div class="dash-card glass-card">
            <h2 class="card-title">Your Profile</h2>
            <dl class="profile-list">
                <div class="profile-row">
                    <dt>Username</dt>
                    <dd><?php echo htmlspecialchars($user['username']); ?></dd>
                </div>
                <div class="profile-row">
                    <dt>Email</dt>
                    <dd><?php echo htmlspecialchars($user['email']); ?></dd>
                </div>
                <div class="profile-row">
                    <dt>Member since</dt>
                    <dd><?php echo date('d M Y', strtotime($user['created_at'])); ?></dd>
                </div>
            </dl>
        </div>
        <div class="dash-card glass-card">
            <h2 class="card-title">Active Session</h2>
            <dl class="profile-list">
                <div class="profile-row">
                    <dt>Session ID</dt>
                    <dd class="mono"><?php echo substr(session_id(), 0, 16); ?>...</dd>
                </div>
                <div class="profile-row">
                    <dt>PHP Version</dt>
                    <dd><?php echo PHP_VERSION; ?></dd>
                </div>
                <div class="profile-row">
                    <dt>Server Time</dt>
                    <dd id="serverTime"><?php echo date('H:i:s'); ?></dd>
                </div>
            </dl>
        </div>
        <div class="dash-card glass-card dash-card--full">
            <h2 class="card-title">Security Info</h2>
            <div class="security-grid">
                <div class="security-item">
                    <span class="sec-icon">&#128274;</span>
                    <span>Password stored with bcrypt via <code>PASSWORD_DEFAULT</code></span>
                </div>
                <div class="security-item">
                    <span class="sec-icon">&#128737;</span>
                    <span>All queries use PDO prepared statements</span>
                </div>
                <div class="security-item">
                    <span class="sec-icon">&#129529;</span>
                    <span>Output sanitized with <code>htmlspecialchars()</code></span>
                </div>
                <div class="security-item">
                    <span class="sec-icon">&#128682;</span>
                    <span>Session fully destroyed on logout</span>
                </div>
            </div>
        </div>
    </div>
</section>
<?php require 'footer.php'; ?>
