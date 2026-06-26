<?php
require 'header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once 'pdo.php';
$pdo = getDB();
$userId = $_SESSION['user_id'];

$error = '';
$success = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        $username = trim($_POST['username']);
        $email    = trim($_POST['email']);
        
        if (empty($username) || empty($email)) {
            $error = 'Username and email are required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Invalid email address.';
        } else {
            // Check if username/email taken by others
            $stmt = $pdo->prepare('SELECT id FROM users WHERE (username = ? OR email = ?) AND id != ?');
            $stmt->execute([$username, $email, $userId]);
            if ($stmt->fetch()) {
                $error = 'Username or email already in use.';
            } else {
                $upd = $pdo->prepare('UPDATE users SET username = ?, email = ? WHERE id = ?');
                $upd->execute([$username, $email, $userId]);
                $_SESSION['username'] = $username;
                $success = 'Profile updated successfully.';
            }
        }
    } elseif (isset($_POST['change_password'])) {
        $current = $_POST['current_password'];
        $new     = $_POST['new_password'];
        $confirm = $_POST['confirm_password'];
        
        $stmt = $pdo->prepare('SELECT password FROM users WHERE id = ?');
        $stmt->execute([$userId]);
        $user = $stmt->fetch();
        
        if (!password_verify($current, $user['password'])) {
            $error = 'Current password is incorrect.';
        } elseif (strlen($new) < 8) {
            $error = 'New password must be at least 8 characters.';
        } elseif ($new !== $confirm) {
            $error = 'New passwords do not match.';
        } else {
            $hash = password_hash($new, PASSWORD_DEFAULT);
            $upd = $pdo->prepare('UPDATE users SET password = ? WHERE id = ?');
            $upd->execute([$hash, $userId]);
            $success = 'Password changed successfully.';
        }
    }
}


$stmt = $pdo->prepare('SELECT username, email FROM users WHERE id = ?');
$stmt->execute([$userId]);
$user = $stmt->fetch();
?>

<section class="dashboard">
    <header class="dash-header">
        <div>
            <p class="eyebrow">Settings</p>
            <h1 class="dash-title">Manage Profile</h1>
        </div>
    </header>

    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <div class="dash-grid">
        <div class="dash-card glass-card">
            <h2 class="card-title">General Info</h2>
            <form method="POST" action="profile.php">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>
                <button type="submit" name="update_profile" class="btn btn-primary">Update Profile</button>
            </form>
        </div>

        <div class="dash-card glass-card">
            <h2 class="card-title">Change Password</h2>
            <form method="POST" action="profile.php">
                <div class="form-group">
                    <label for="current_password">Current Password</label>
                    <input type="password" id="current_password" name="current_password" required>
                </div>
                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <input type="password" id="new_password" name="new_password" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirm New Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
                <button type="submit" name="change_password" class="btn btn-outline">Change Password</button>
            </form>
        </div>
    </div>
</section>

<?php require 'footer.php'; ?>
