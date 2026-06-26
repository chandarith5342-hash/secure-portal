<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'pdo.php';
if (isset($_SESSION['user_id'])) {
    $pdo = getDB();
    $upd = $pdo->prepare('UPDATE users SET remember_token = NULL WHERE id = ?');
    $upd->execute([$_SESSION['user_id']]);
}

$_SESSION = array();
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params['path'], $params['domain'],
        $params['secure'], $params['httponly']
    );
}

// Clear Remember Me cookie
setcookie('remember_me', '', time() - 3600, '/');

session_destroy();
header('Location: login.php');
exit;
