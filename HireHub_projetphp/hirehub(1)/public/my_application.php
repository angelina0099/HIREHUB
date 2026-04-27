<?php
// Sécurité : utilisateur connecté
require_once "../includes/auth.php";

// Autoriser seulement USER
if ($_SESSION['user']['role'] !== 'USER') {
    header("Location: ../errors/403.php");
    exit;
}

require_once "../classes/Database.php";

$db = new Database();
$user_id = $_SESSION['user']['id'];

// Requête : récupérer les candidatures du user
$sql = "
    SELECT 
        applications.id,
        job_offers.title,
        applications.cv_path,
        applications.created_at
    FROM applications
    JOIN job_offers ON applications.offer_id = job_offers.id
    WHERE applications.user_id = ?
    ORDER BY applications.created_at DESC
";

$stmt = mysqli_prepare($db->conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$pageTitle = "My Applications";
require_once "../includes/header.php";
?>

<div class="card">
    <h2>My Applications</h2>
</div>

<?php
// Vérifier s'il y a des candidatures
if (mysqli_num_rows($result) > 0) {
    ?>
    <div class="job-list">
    <?php
    while ($row = mysqli_fetch_assoc($result)) {
        ?>
        <div class="job-card">
            <h3><?php echo htmlspecialchars($row['title']); ?></h3>
            <p style="color: #666; margin-bottom: 10px;">
                Applied on: <?php echo date('F j, Y', strtotime($row['created_at'])); ?>
            </p>
            <a href="../uploads/<?php echo htmlspecialchars($row['cv_path']); ?>" target="_blank" class="btn btn-secondary">
                View CV
            </a>
        </div>
        <?php
    }
    ?>
    </div>
    <?php
} else {
    ?>
    <div class="card empty-state">
        <p>You have not applied to any job yet.</p>
        <p><a href="index.php" class="btn">Browse Job Offers</a></p>
    </div>
    <?php
}
?>

<p class="mt-20">
    <a href="index.php" class="back-link">← Back to job offers</a>
</p>

<?php require_once "../includes/footer.php"; ?>
