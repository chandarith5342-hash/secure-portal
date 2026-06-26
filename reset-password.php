<?php
require 'header.php';
require_once 'pdo.php';

$error = '';
$success = '';
$token = $_GET['token'] ?? ($_POST['token'] ?? '');

if (empty($token)) {
    header('Location: login.php');
    exit;
}

$pdo = getDB();
$stmt = $pdo->prepare('SELECT id FROM users WHERE reset_token = ? AND reset_expiry > NOW() LIMIT 1');
$stmt->execute([$token]);
$user = $stmt->fetch();

if (!$user) {
    $error = 'Invalid or expired reset token.';
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'];
    $confirm  = $_POST['confirm'];
    
    if (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $upd = $pdo->prepare('UPDATE users SET password = ?, reset_token = NULL, reset_expiry = NULL WHERE id = ?');
        $upd->execute([$hash, $user['id']]);
        $success = 'Your password has been reset! You can now <a href="login.php">sign in</a>.';
    }
}
?>

<section class="auth-section">
    <div class="auth-card glass-card">
        <h1 class="auth-title">Reset Password</h1>
        <p class="auth-sub">Enter your new password below</p>
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if ($user && !$success): ?>
        <form method="POST" action="reset-password.php">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
            <div class="form-group">
                <label for="password">New Password</label>
                <div class="input-wrapper">
                    <input type="password" id="password" name="password" placeholder="Min. 8 characters" required>
                    <button type="button" class="toggle-pw" data-target="password">&#128065;</button>
                </div>
            </div>
            <div class="form-group">
                <label for="confirm">Confirm New Password</label>
                <input type="password" id="confirm" name="confirm" placeholder="Repeat new password" required>
            </div>
            <button type="submit" class="btn btn-primary btn-full">Reset Password</button>
        </form>
        <?php endif; ?>
        <p class="auth-switch"><a href="login.php">Back to Login</a></p>
    </div>
</section>

<?php require 'footer.php'; ?>
