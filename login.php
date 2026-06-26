<?php
require 'header.php';
require_once 'pdo.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = trim($_POST['identifier']);
    $password   = $_POST['password'];
    $remember   = isset($_POST['remember']);

    if (empty($identifier) || empty($password)) {
        $error = 'Please fill in all fields.';
    } else {
        $pdo  = getDB();
        $stmt = $pdo->prepare('SELECT id, username, password, role, is_verified FROM users WHERE username = :username OR email = :email LIMIT 1');
        $stmt->execute(array(':username' => $identifier, ':email' => $identifier));
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            if (!$user['is_verified']) {
                $error = 'Please verify your email address before signing in.';
            } else {
                $_SESSION['user_id']  = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role']     = $user['role'];

                if ($remember) {
                    $token = bin2hex(random_bytes(32));
                    $upd = $pdo->prepare('UPDATE users SET remember_token = ? WHERE id = ?');
                    $upd->execute([$token, $user['id']]);
                    setcookie('remember_me', $token, time() + (86400 * 30), "/", "", false, true); // 30 days
                }

                header('Location: dashboard.php');
                exit;
            }
        } else {
            $error = 'Invalid credentials. Please try again.';
        }
    }
}
?>
<section class="auth-section">
    <div class="auth-card glass-card">
        <h1 class="auth-title">Welcome back</h1>
        <p class="auth-sub">Sign in to your account</p>
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form method="POST" action="login.php">
            <div class="form-group">
                <label for="identifier">Username or Email</label>
                <input type="text" id="identifier" name="identifier" placeholder="you@example.com" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-wrapper">
                    <input type="password" id="password" name="password" placeholder="Your password" required>
                    <button type="button" class="toggle-pw" data-target="password">&#128065;</button>
                </div>
            </div>
            <div class="form-group form-checkbox">
                <label><input type="checkbox" name="remember"> Remember me</label>
            </div>
            <button type="submit" class="btn btn-primary btn-full">Sign In</button>
        </form>
        <p class="auth-switch">Forgot password? <a href="forgot-password.php">Reset here</a></p>
        <p class="auth-switch">No account yet? <a href="register.php">Register here</a></p>
    </div>
</section>
<?php require 'footer.php'; ?>
