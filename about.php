<?php require 'header.php'; ?>
<section class="about-section">
    <div class="about-hero">
        <p class="eyebrow">Week 10 - Dynamic Secure Portal</p>
        <h1>How it's built</h1>
        <p class="about-intro">A breakdown of the architecture, security choices, and code structure.</p>
    </div>
    <div class="about-grid">
        <article class="about-card glass-card">
            <h2>Database Schema</h2>
            <pre class="code-block"><code>CREATE TABLE users (
  id         INT PRIMARY KEY AUTO_INCREMENT,
  username   VARCHAR(50)  NOT NULL UNIQUE,
  email      VARCHAR(100) NOT NULL UNIQUE,
  password   VARCHAR(255) NOT NULL,
  role       ENUM('user','admin') DEFAULT 'user',
  is_verified TINYINT(1)  DEFAULT 0,
  created_at TIMESTAMP DEFAULT NOW()
);</code></pre>
        </article>
        <article class="about-card glass-card">
            <h2>PDO Connection</h2>
            <pre class="code-block"><code>$pdo = new PDO($dsn, $user, $pass, [
  PDO::ATTR_ERRMODE =>
    PDO::ERRMODE_EXCEPTION,
  PDO::ATTR_DEFAULT_FETCH_MODE =>
    PDO::FETCH_ASSOC,
]);</code></pre>
        </article>
        <article class="about-card glass-card">
            <h2>Modular Templating</h2>
            <pre class="code-block"><code>// Every page starts with:
require 'header.php';

// ... page content ...

require 'footer.php';</code></pre>
        </article>
        <article class="about-card glass-card">
            <h2>Secure Auth Flow</h2>
            <pre class="code-block"><code>// Register
$hash = password_hash(
  $password, PASSWORD_DEFAULT
);
// Login
if (password_verify($input, $hash)) {
  $_SESSION['user_id'] = $user['id'];
}</code></pre>
        </article>
    </div>
</section>
<?php require 'footer.php'; ?>
