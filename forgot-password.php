<?php
require 'header.php';
require_once 'pdo.php';
require 'mailer.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    
    if (empty($email)) {
        $error = 'Please enter your email address.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email address.';
    } else {
        $pdo = getDB();
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user) {
            $token = bin2hex(random_bytes(32));
            $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            $upd = $pdo->prepare('UPDATE users SET reset_token = ?, reset_expiry = ? WHERE id = ?');
            $upd->execute([$token, $expiry, $user['id']]);
            
            $resetLink = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/reset-password.php?token=$token";
            $subject = "Reset your SecurePortal password";
            $msg = "<h1>Password Reset Request</h1><p>You requested a password reset. Please click the link below to set a new password. This link expires in 1 hour.</p><p><a href='$resetLink'>$resetLink</a></p>";
            
            sendMail($email, $subject, $msg);
        }
        

        $success = 'If an account exists with that email, a reset link has been sent.';
    }
}
?>

<section class="auth-section">
    <div class="auth-card glass-card">
        <h1 class="auth-title">Forgot Password</h1>
        <p class="auth-sub">Enter your email to receive a reset link</p>
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
            <?php if (!empty($_SESSION['_dev_mail_link'])): ?>
            <div class="alert" style="background:rgba(108,99,255,.12);border-color:#6c63ff;color:#a78bfa;margin-top:.5rem">
                <strong>&#128274; Email not delivered?</strong> Click your password reset link directly:<br>
                <a href="<?php echo htmlspecialchars($_SESSION['_dev_mail_link']); ?>" style="color:#6c63ff;word-break:break-all">
                    <?php echo htmlspecialchars($_SESSION['_dev_mail_link']); ?>
                </a>
            </div>
            <?php unset($_SESSION['_dev_mail_link'], $_SESSION['_dev_mail_subj']); ?>
            <?php endif; ?>
        <?php endif; ?>
        <form method="POST" action="forgot-password.php">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" placeholder="you@example.com" required>
            </div>
            <button type="submit" class="btn btn-primary btn-full">Send Reset Link</button>
        </form>
        <p class="auth-switch"><a href="login.php">Back to Login</a></p>
    </div>
</section>

<?php require 'footer.php'; ?>
