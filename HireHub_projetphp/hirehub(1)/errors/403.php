<?php
session_start();
$pageTitle = "Access Forbidden";
require_once "../includes/header.php";
?>

<div class="card" style="max-width: 600px; margin: 0 auto; text-align: center;">
    <h2 style="color: #dc2626; font-size: 4rem; margin-bottom: 20px;">403</h2>
    <h3>Access Forbidden</h3>
    <p style="color: #666; margin: 20px 0;">You don't have permission to access this page.</p>
    <p>
        <a href="../public/index.php" class="btn">Go to Homepage</a>
    </p>
</div>

<?php require_once "../includes/footer.php"; ?>
