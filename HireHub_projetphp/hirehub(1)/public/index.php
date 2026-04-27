<?php
session_start();

// Inclure la connexion à la base
require_once "../classes/Database.php";

// Créer l'objet Database
$db = new Database();

// Récupérer toutes les offres
$sql = "SELECT * FROM job_offers ORDER BY created_at DESC";
$result = mysqli_query($db->conn, $sql);

$pageTitle = "Job Offers";
require_once "../includes/header.php";
?>

<div class="hero-section">
    <div class="hero-content">
        <h1>Available Job Offers</h1>
        <p>Discover your next career opportunity</p>
    </div>
</div>

<?php
// Vérifier s'il y a des offres
if (mysqli_num_rows($result) > 0) {
    ?>
    <div class="job-list">
    <?php
    // Boucle pour afficher chaque offre
    while ($job = mysqli_fetch_assoc($result)) {
        // Determine image path
        $imagePath = 'assets/images/default.png';
        if (!empty($job['image_path']) && file_exists("../uploads/" . $job['image_path'])) {
            $imagePath = "../uploads/" . $job['image_path'];
        }
        ?>
        <div class="job-card">
            <div class="job-content-wrapper">
                <div class="job-image-wrapper">
                    <img src="<?php echo htmlspecialchars($imagePath); ?>" 
                         alt="<?php echo htmlspecialchars($job['title']); ?>" 
                         class="job-image">
                </div>
                
                <div class="job-info">
                    <div class="job-header">
                        <h3 class="job-title"><?php echo htmlspecialchars($job['title']); ?></h3>
                        <?php if (isset($job['created_at'])) { ?>
                            <span class="job-date">
                                <i class="icon-calendar">📅</i> <?php echo date('M j, Y', strtotime($job['created_at'])); ?>
                            </span>
                        <?php } ?>
                    </div>
                    <div class="job-description">
                        <p><?php echo nl2br(htmlspecialchars($job['description'])); ?></p>
                    </div>
                    <div class="job-footer">
                        <?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'USER') { ?>
                            <a href="apply.php?offer_id=<?php echo $job['id']; ?>" class="btn btn-primary">Apply Now</a>
                        <?php } else { ?>
                            <p class="login-prompt">Please <a href="login.php">login</a> to apply</p>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
    ?>
    </div>
    <?php
} else {
    ?>
    <div class="card empty-state">
        <p>No job offers available at the moment.</p>
        <p>Check back later for new opportunities!</p>
    </div>
    <?php
}
?>

<?php require_once "../includes/footer.php"; ?>
