<?php
require 'header.php';
require 'pdo.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = trim($_POST['identifier']);
    $password   = $_POST['password'];

    if (empty($identifier) || empty($password)) {
        $error = 'Please fill in all fields.';
    } else {
        $pdo  = getDB();
        $stmt = $pdo->prepare('SELECT id, username, password FROM users WHERE username = :id OR email = :id LIMIT 1');
        $stmt->execute(array(':id' => $identifier));
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id']  = $user['id'];
            $_SESSION['username'] = $user['username'];
            header('Location: dashboard.php');
            exit;
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
            <button type="submit" class="btn btn-primary btn-full">Sign In</button>
        </form>
        <p class="auth-switch">No account yet? <a href="register.php">Register here</a></p>
    </div>
</section>
<?php require 'footer.php'; ?>
