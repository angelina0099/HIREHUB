<?php
require_once "../includes/auth.php";

// Autoriser seulement ADMIN
if ($_SESSION['user']['role'] !== 'ADMIN') {
    header("Location: ../errors/403.php");
    exit;
}

require_once "../classes/Database.php";
$db = new Database();

// Vérifier si les tables existent
$tablesCheck = mysqli_query($db->conn, "SHOW TABLES LIKE 'users'");
if (mysqli_num_rows($tablesCheck) == 0) {
    die("Error: Database tables not found. Please run the SQL script first.");
}

// Vérifier si la colonne 'name' existe dans users
$hasNameColumn=false;
$columns =mysqli_query($db->conn, "SHOW COLUMNS FROM users LIKE 'name'");
if ($columns && mysqli_num_rows($columns) > 0) {
    $hasNameColumn = true;
}

// Requête pour récupérer les candidats + offres + users
if ($hasNameColumn) {
    $sql = "
        SELECT 
            users.name,
            users.email,
            job_offers.title AS job_title,
            applications.cv_path,
            applications.created_at
        FROM applications
        JOIN users ON applications.user_id = users.id
        JOIN job_offers ON applications.offer_id = job_offers.id
        ORDER BY applications.created_at DESC
    ";
} else {
    $sql = "
        SELECT 
            users.email AS name,
            users.email,
            job_offers.title AS job_title,
            applications.cv_path,
            applications.created_at
        FROM applications
        JOIN users ON applications.user_id = users.id
        JOIN job_offers ON applications.offer_id = job_offers.id
        ORDER BY applications.created_at DESC
    ";
}

$result = mysqli_query($db->conn, $sql);
$errors = [];

// Vérifier les erreurs SQL
if (!$result) {
    $errorMsg = mysqli_error($db->conn);
    // Messages d'erreur plus clairs
    if (strpos($errorMsg, "Unknown column 'name'") !== false) {
        // Ajouter la colonne name si elle n'existe pas
        mysqli_query($db->conn, "ALTER TABLE users ADD COLUMN name VARCHAR(255) AFTER email");
        // Réessayer la requête
        $result = mysqli_query($db->conn, $sql);
        if (!$result) {
            $errors[] = "Database error: " . mysqli_error($db->conn);
        }
    } else {
        $errors[] = "Database error: " . $errorMsg;
    }
}
?>

<?php
$pageTitle = "Candidates";
require_once "../includes/header.php";
?>

<div class="hero-section">
    <div class="hero-content">
        <h1>Candidates List</h1>
        <p>View and manage job applications</p>
    </div>
</div>

<?php
// Afficher les erreurs s'il y en a
if (!empty($errors)) {
    foreach ($errors as $error) {
        echo "<div class='alert alert-error'>$error</div>";
    }
}

if ($result && mysqli_num_rows($result) > 0) {
    ?>
    <div class="job-list">
    <?php
    while ($row = mysqli_fetch_assoc($result)) {
        ?>
        <div class="job-card">
            <div class="job-header">
                <h3 class="job-title"><?php echo htmlspecialchars($row['job_title']); ?></h3>
                <?php if (isset($row['created_at'])) { ?>
                    <span class="job-date">
                        Applied: <?php echo date('M j, Y', strtotime($row['created_at'])); ?>
                    </span>
                <?php } ?>
            </div>
            
            <div class="job-description">
                <p>
                    <strong>Name:</strong>
                    <?php echo htmlspecialchars($row['name'] ?? $row['email']); ?>
                </p>

                <p>
                    <strong>Email:</strong>
                    <a href="mailto:<?php echo htmlspecialchars($row['email']); ?>">
                        <?php echo htmlspecialchars($row['email']); ?>
                    </a>
                </p>
            </div>

            <div class="job-footer">
                <a href="../uploads/<?php echo htmlspecialchars($row['cv_path']); ?>" target="_blank" class="btn btn-secondary">
                    📄 View CV
                </a>
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
        <p>No candidates found.</p>
    </div>
    <?php
}
?>

<p class="mt-20">
    <a href="dashboard.php" class="back-link">← Back to dashboard</a>
</p>

<?php require_once "../includes/footer.php"; ?>
