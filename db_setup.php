<?php


require_once 'pdo.php';

define('SETUP_TOKEN', 'setup2024');

$run = $_GET['run'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>DB Setup – SecurePortal</title>
<style>
  body{font-family:sans-serif;background:#0a0e1a;color:#f0f0f8;display:flex;justify-content:center;padding:3rem 1rem}
  .box{background:#111827;border:1px solid rgba(255,255,255,.1);border-radius:14px;padding:2rem;max-width:620px;width:100%}
  h1{margin-top:0;color:#6c63ff}
  p,li{line-height:1.7;color:#8892a4}
  code{background:rgba(108,99,255,.15);padding:.1em .4em;border-radius:4px;font-size:.88em;color:#a78bfa}
  .ok{color:#34d399}.err{color:#f87171}
  .btn{display:inline-block;margin-top:1.5rem;padding:.7rem 1.5rem;background:#6c63ff;color:#fff;border-radius:999px;text-decoration:none;font-weight:600}
  .warn{background:rgba(251,146,60,.1);border-left:3px solid #fb923c;padding:1rem;border-radius:6px;margin-top:1.5rem}
</style>
</head>
<body>
<div class="box">
<h1>&#9889; SecurePortal Setup</h1>

<?php if ($run !== SETUP_TOKEN): ?>
  <p>To run the database setup, visit this URL with the token appended:</p>
  <p><code><?php echo htmlspecialchars("http://{$_SERVER['HTTP_HOST']}{$_SERVER['PHP_SELF']}?run=" . SETUP_TOKEN); ?></code></p>
  <p>You can change <code>SETUP_TOKEN</code> inside this file to anything you like before uploading.</p>
<?php else:
    $steps = [];
    $ok = true;
    try {
        $pdo = getDB();
        $steps[] = ['ok', 'Connected to database <strong>' . DB_NAME . '</strong>'];

        // Drop old table for clean install
        $pdo->exec("DROP TABLE IF EXISTS users");
        $steps[] = ['ok', 'Cleared old <code>users</code> table (if any)'];

        // Create table
        $pdo->exec("CREATE TABLE users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL UNIQUE,
            email VARCHAR(100) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            role ENUM('user','admin') DEFAULT 'user',
            is_verified TINYINT(1) DEFAULT 0,
            verification_token VARCHAR(255) DEFAULT NULL,
            reset_token VARCHAR(255) DEFAULT NULL,
            reset_expiry DATETIME DEFAULT NULL,
            remember_token VARCHAR(255) DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )");
        $steps[] = ['ok', 'Created <code>users</code> table'];


        $hash = password_hash('password123', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role, is_verified) VALUES (?,?,?,?,?)");
        $stmt->execute(['admin', 'admin@example.com', $hash, 'admin', 1]);
        $steps[] = ['ok', 'Default admin account created'];

    } catch (Exception $e) {
        $steps[] = ['err', 'Error: ' . htmlspecialchars($e->getMessage())];
        $ok = false;
    }

    foreach ($steps as [$type, $msg]) {
        echo "<p class='$type'>" . ($type === 'ok' ? '&#10004; ' : '&#10008; ') . $msg . "</p>";
    }

    if ($ok): ?>
        <p class="ok" style="font-size:1.1rem;margin-top:1.5rem"><strong>&#127881; Setup complete!</strong></p>
        <p>Login with: <code>admin</code> / <code>password123</code></p>
        <a href="login.php" class="btn">Go to Login</a>
        <div class="warn">
            <strong>&#9888; Important:</strong> Delete <code>db_setup.php</code> from your server now.
            Leaving it up lets anyone reset your database.
        </div>
    <?php endif; ?>
<?php endif; ?>
</div>
</body>
</html>
