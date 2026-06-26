<?php
define('DB_HOST',    'sql311.infinityfree.com');
define('DB_NAME',    'if0_42177058_secure_portal');
define('DB_USER',    'if0_42177058');
define('DB_PASS',    'AbA86r9pcAuk');
define('DB_CHARSET', 'utf8mb4');

function getDB() {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            die('<div style="font-family:sans-serif;padding:2rem;background:#fff5f5;border:2px solid red;border-radius:8px;margin:2rem auto;max-width:600px">
                <h2>&#9888; Database Connection Error</h2>
                <p>Could not connect to the database. Check your password in <code>pdo.php</code>.</p>
                <p style="color:#888;font-size:.85rem">Error: ' . htmlspecialchars($e->getMessage()) . '</p>
            </div>');
        }
    }
    return $pdo;
}
