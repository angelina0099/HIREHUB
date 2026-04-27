<?php
// Sécurité : utilisateur connecté
require_once "../includes/auth.php";

// Autoriser seulement ADMIN
if ($_SESSION['user']['role'] !== 'ADMIN') {
    header("Location: ../errors/403.php");
    exit;
}

require_once "../classes/Database.php";
$db = new Database();

// Get statistics with error handling
$jobsResult = mysqli_query($db->conn, "SELECT COUNT(*) as count FROM job_offers");
$jobsCount = $jobsResult ? (mysqli_fetch_assoc($jobsResult)['count'] ?? 0) : 0;

$applicationsResult = mysqli_query($db->conn, "SELECT COUNT(*) as count FROM applications");
$applicationsCount = $applicationsResult ? (mysqli_fetch_assoc($applicationsResult)['count'] ?? 0) : 0;

$candidatesResult = mysqli_query($db->conn, "SELECT COUNT(DISTINCT user_id) as count FROM applications");
$candidatesCount = $candidatesResult ? (mysqli_fetch_assoc($candidatesResult)['count'] ?? 0) : 0;

$pageTitle = "Admin Dashboard";
require_once "../includes/header.php";
?>

<div class="hero-section">
    <div class="hero-content">
        <h1>Admin Dashboard</h1>
        <p>Welcome back, <strong><?php echo htmlspecialchars($_SESSION['user']['name']); ?></strong> 👋</p>
    </div>
</div>

<div class="dashboard-grid">
    <div class="dashboard-card">
        <h3>Job Offers</h3>
        <p style="font-size: 2rem; color: #2563eb; margin: 10px 0;"><?php echo $jobsCount; ?></p>
        <a href="jobs_edit.php" class="btn btn-small">Manage</a>
    </div>
    
    <div class="dashboard-card">
        <h3>Applications</h3>
        <p style="font-size: 2rem; color: #2563eb; margin: 10px 0;"><?php echo $applicationsCount; ?></p>
        <a href="candidates.php" class="btn btn-small">View All</a>
    </div>
    
    <div class="dashboard-card">
        <h3>Candidates</h3>
        <p style="font-size: 2rem; color: #2563eb; margin: 10px 0;"><?php echo $candidatesCount; ?></p>
        <a href="candidates.php" class="btn btn-small">View All</a>
    </div>
</div>

<div class="card">
    <h3>Quick Actions</h3>
    <div class="action-grid">
        <a href="jobs_add.php" class="action-card">
            <span class="action-icon">➕</span>
            <span class="action-text">Add New Job Offer</span>
        </a>
        <a href="jobs_edit.php" class="action-card">
            <span class="action-icon">✏️</span>
            <span class="action-text">Manage Job Offers</span>
        </a>
        <a href="candidates.php" class="action-card">
            <span class="action-icon">👥</span>
            <span class="action-text">View Candidates</span>
        </a>
    </div>
</div>

<p class="mt-20">
    <a href="../public/index.php" class="back-link">← Back to website</a>
</p>

<?php require_once "../includes/footer.php"; ?>
