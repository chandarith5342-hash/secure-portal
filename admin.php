<?php
require 'header.php';


if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit;
}

require_once 'pdo.php';
$pdo = getDB();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['change_role'])) {
        $targetId = $_POST['user_id'];
        $newRole  = $_POST['role'];
        if ($targetId != $_SESSION['user_id']) { // Don't change own role
            $upd = $pdo->prepare('UPDATE users SET role = ? WHERE id = ?');
            $upd->execute([$newRole, $targetId]);
            $success = "User role updated.";
        } else {
            $error = "You cannot change your own role.";
        }
    }
}

$stmt = $pdo->query('SELECT id, username, email, role, is_verified, created_at FROM users ORDER BY created_at DESC');
$users = $stmt->fetchAll();
?>

<section class="dashboard">
    <header class="dash-header">
        <div>
            <p class="eyebrow">Administration</p>
            <h1 class="dash-title">User Management</h1>
        </div>
    </header>

    <?php if ($error): ?><div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
    <?php if ($success): ?><div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div><?php endif; ?>

    <div class="dash-card glass-card dash-card--full">
        <h2 class="card-title">All Users</h2>
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; margin-top: 1rem;">
                <thead>
                    <tr style="text-align: left; border-bottom: 2px solid var(--border);">
                        <th style="padding: 1rem;">ID</th>
                        <th style="padding: 1rem;">Username</th>
                        <th style="padding: 1rem;">Email</th>
                        <th style="padding: 1rem;">Role</th>
                        <th style="padding: 1rem;">Verified</th>
                        <th style="padding: 1rem;">Joined</th>
                        <th style="padding: 1rem;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): ?>
                    <tr style="border-bottom: 1px solid var(--border);">
                        <td style="padding: 1rem;"><?php echo $u['id']; ?></td>
                        <td style="padding: 1rem;"><?php echo htmlspecialchars($u['username']); ?></td>
                        <td style="padding: 1rem;"><?php echo htmlspecialchars($u['email']); ?></td>
                        <td style="padding: 1rem; font-weight: 600; color: <?php echo $u['role'] === 'admin' ? 'var(--accent)' : 'inherit'; ?>;">
                            <?php echo strtoupper($u['role']); ?>
                        </td>
                        <td style="padding: 1rem;">
                            <span style="color: <?php echo $u['is_verified'] ? 'var(--success)' : 'var(--error)'; ?>;">
                                <?php echo $u['is_verified'] ? '&#10004;' : '&#10008;'; ?>
                            </span>
                        </td>
                        <td style="padding: 1rem; font-size: .85rem; color: var(--text-muted);">
                            <?php echo date('d M Y', strtotime($u['created_at'])); ?>
                        </td>
                        <td style="padding: 1rem;">
                            <?php if ($u['id'] != $_SESSION['user_id']): ?>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                                <select name="role" onchange="this.form.submit()" style="background: var(--bg-2); color: var(--text); border: 1px solid var(--border); border-radius: 4px; padding: 2px 5px;">
                                    <option value="user" <?php echo $u['role'] === 'user' ? 'selected' : ''; ?>>User</option>
                                    <option value="admin" <?php echo $u['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                                </select>
                                <input type="hidden" name="change_role" value="1">
                            </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<?php require 'footer.php'; ?>
