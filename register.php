<?php
require 'header.php';
require_once 'pdo.php';
require 'mailer.php';

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm  = $_POST['confirm'];

    if (empty($username) || empty($email) || empty($password)) {
        $error = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } else {
        $pdo   = getDB();
        $check = $pdo->prepare('SELECT id FROM users WHERE username = ? OR email = ? LIMIT 1');
        $check->execute(array($username, $email));

        if ($check->fetch()) {
            $error = 'Username or email is already taken.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $token = bin2hex(random_bytes(32));
            
            $ins  = $pdo->prepare('INSERT INTO users (username, email, password, verification_token) VALUES (?, ?, ?, ?)');
            $ins->execute(array($username, $email, $hash, $token));
            
      
            $verifyLink = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/verify.php?token=$token";
            $subject = "Verify your SecurePortal account";
            $msg = "<h1>Welcome to SecurePortal!</h1><p>Please click the link below to verify your account:</p><p><a href='$verifyLink'>$verifyLink</a></p>";
            
            sendMail($email, $subject, $msg);
            
            $success = 'Account created! Please check your email to verify your account before signing in.';
        }
    }
}
?>
<section class="auth-section">
    <div class="auth-card glass-card">
        <h1 class="auth-title">Create account</h1>
        <p class="auth-sub">Join SecurePortal today</p>
        <?php if ($error):   ?><div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
            <?php if (!empty($_SESSION['_dev_mail_link'])): ?>
            <div class="alert" style="background:rgba(108,99,255,.12);border-color:#6c63ff;color:#a78bfa;margin-top:.5rem">
                <strong>&#128274; Email not delivered?</strong> Click your verification link directly:<br>
                <a href="<?php echo htmlspecialchars($_SESSION['_dev_mail_link']); ?>" style="color:#6c63ff;word-break:break-all">
                    <?php echo htmlspecialchars($_SESSION['_dev_mail_link']); ?>
                </a>
            </div>
            <?php unset($_SESSION['_dev_mail_link'], $_SESSION['_dev_mail_subj']); ?>
            <?php endif; ?>
        <?php endif; ?>
        <form method="POST" action="register.php">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="cooluser99"
                    value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="you@example.com"
                    value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
            </div>
            <div class="form-group">
                <label for="password">Password <span class="hint">(min. 8 characters)</span></label>
                <div class="input-wrapper">
                    <input type="password" id="password" name="password" placeholder="Choose a strong password" required>
                    <button type="button" class="toggle-pw" data-target="password">&#128065;</button>
                </div>
                <div class="strength-bar"><div class="strength-fill" id="strengthFill"></div></div>
                <span class="strength-label" id="strengthLabel"></span>
            </div>
            <div class="form-group">
                <label for="confirm">Confirm Password</label>
                <input type="password" id="confirm" name="confirm" placeholder="Repeat your password" required>
            </div>
            <button type="submit" class="btn btn-primary btn-full">Create Account</button>
        </form>
        <p class="auth-switch">Already have an account? <a href="login.php">Sign in</a></p>
    </div>
</section>
<?php require 'footer.php'; ?>
