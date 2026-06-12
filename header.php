<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
$isLoggedIn  = isset($_SESSION['user_id']);
$username    = $isLoggedIn ? htmlspecialchars($_SESSION['username']) : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SecurePortal</title>
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@300;400;500&display=swap" rel="stylesheet">
</head>
<body>
<nav class="navbar">
    <a href="index.php" class="nav-brand">
        <span class="brand-icon">&#11043;</span>
        <span class="brand-name">SecurePortal</span>
    </a>
    <button class="hamburger" id="hamburger" aria-label="Toggle menu">
        <span></span><span></span><span></span>
    </button>
    <ul class="nav-links" id="navLinks">
        <li><a href="index.php"  class="<?php echo $currentPage === 'index'  ? 'active' : ''; ?>">Home</a></li>
        <li><a href="about.php" class="<?php echo $currentPage === 'about'  ? 'active' : ''; ?>">About</a></li>
        <?php if ($isLoggedIn): ?>
            <li><a href="dashboard.php" class="<?php echo $currentPage === 'dashboard' ? 'active' : ''; ?>">Dashboard</a></li>
            <li><a href="logout.php" class="nav-btn btn-outline">Sign out (<?php echo $username; ?>)</a></li>
        <?php else: ?>
            <li><a href="login.php" class="<?php echo $currentPage === 'login' ? 'active' : ''; ?>">Login</a></li>
            <li><a href="register.php" class="nav-btn btn-primary">Register</a></li>
        <?php endif; ?>
    </ul>
</nav>
<main>
