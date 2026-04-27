<?php
session_start();
$pageTitle = "Page Not Found";
require_once "../includes/header.php";
?>

<div class="card" style="max-width: 600px; margin: 0 auto; text-align: center;">
    <h2 style="color: #2563eb; font-size: 4rem; margin-bottom: 20px;">404</h2>
    <h3>Page Not Found</h3>
    <p style="color: #666; margin: 20px 0;">The page you're looking for doesn't exist.</p>
    <p>
        <a href="../public/index.php" class="btn">Go to Homepage</a>
    </p>
</div>

<?php require_once "../includes/footer.php"; ?>
