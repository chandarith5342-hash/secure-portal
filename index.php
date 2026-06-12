<?php
require 'header.php';
?>
<section class="hero">
    <div class="hero-grid">
        <div class="hero-text">
            <p class="eyebrow">Secure · Dynamic · Modern</p>
            <h1 class="hero-heading">Your portal.<br><span class="accent">Protected.</span></h1>
            <p class="hero-sub">A PHP and MySQL-powered authentication system built with clean, modular code and secure database practices.</p>
            <div class="hero-actions">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="dashboard.php" class="btn btn-primary">Go to Dashboard</a>
                <?php else: ?>
                    <a href="register.php" class="btn btn-primary">Create Account</a>
                    <a href="login.php" class="btn btn-ghost">Sign In</a>
                <?php endif; ?>
            </div>
        </div>
        <div class="hero-card glass-card">
            <div class="card-badge">Tech Stack</div>
            <ul class="stack-list">
                <li><span class="stack-icon">&#128024;</span> PHP 8.x</li>
                <li><span class="stack-icon">&#128451;</span> MySQL + PDO</li>
                <li><span class="stack-icon">&#128274;</span> password_hash()</li>
                <li><span class="stack-icon">&#129513;</span> Modular Templates</li>
                <li><span class="stack-icon">&#127912;</span> CSS3 Flexbox/Grid</li>
                <li><span class="stack-icon">&#9889;</span> ES6+ JavaScript</li>
            </ul>
        </div>
    </div>
</section>
<section class="features">
    <h2 class="section-title">What's inside</h2>
    <div class="features-grid">
        <div class="feature-card" data-reveal>
            <div class="feature-icon">&#128272;</div>
            <h3>Secure Auth</h3>
            <p>Passwords hashed with <code>password_hash()</code>. No plain-text, ever.</p>
        </div>
        <div class="feature-card" data-reveal>
            <div class="feature-icon">&#129683;</div>
            <h3>Modular PHP</h3>
            <p>Header, footer and DB connection are reusable components via <code>require()</code>.</p>
        </div>
        <div class="feature-card" data-reveal>
            <div class="feature-icon">&#128737;</div>
            <h3>PDO Queries</h3>
            <p>Prepared statements prevent SQL injection at every database call.</p>
        </div>
        <div class="feature-card" data-reveal>
            <div class="feature-icon">&#128241;</div>
            <h3>Responsive Design</h3>
            <p>Looks great on phones and desktops.</p>
        </div>
    </div>
</section>
<?php require 'footer.php'; ?>
