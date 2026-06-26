<?php
require 'header.php';
require_once 'pdo.php';

$error = '';
$success = '';

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    $pdo = getDB();
    
    $stmt = $pdo->prepare('SELECT id FROM users WHERE verification_token = ? LIMIT 1');
    $stmt->execute([$token]);
    $user = $stmt->fetch();
    
    if ($user) {
        $upd = $pdo->prepare('UPDATE users SET is_verified = 1, verification_token = NULL WHERE id = ?');
        $upd->execute([$user['id']]);
        $success = 'Your account has been verified! You can now <a href="login.php">sign in</a>.';
    } else {
        $error = 'Invalid or expired verification token.';
    }
} else {
    $error = 'No verification token provided.';
}
?>

<section class="auth-section">
    <div class="auth-card glass-card">
        <h1 class="auth-title">Account Verification</h1>
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        <p class="auth-switch"><a href="index.php">Return to Home</a></p>
    </div>
</section>

<?php require 'footer.php'; ?>
