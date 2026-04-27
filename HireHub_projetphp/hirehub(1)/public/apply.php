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
$errors = [];
$success = "";

// Vérifier l'ID de l'offre
if (!isset($_GET['offer_id']) || !is_numeric($_GET['offer_id'])) {
    header("Location: index.php");
    exit;
}

$offer_id = (int) $_GET['offer_id'];
$user_id = $_SESSION['user']['id'];

// Récupérer les détails de l'offre
$jobSql = "SELECT title, description FROM job_offers WHERE id = ?";
$stmt = mysqli_prepare($db->conn, $jobSql);
mysqli_stmt_bind_param($stmt, "i", $offer_id);
mysqli_stmt_execute($stmt);
$jobResult = mysqli_stmt_get_result($stmt);
$job = mysqli_fetch_assoc($jobResult);

if (!$job) {
    header("Location: index.php");
    exit;
}

// Vérifier si l'utilisateur a déjà postulé
$checkSql = "SELECT id FROM applications WHERE offer_id=? AND user_id=?";
$stmt = mysqli_prepare($db->conn, $checkSql);
mysqli_stmt_bind_param($stmt, "ii", $offer_id, $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) > 0) {
    $errors[] = "You have already applied for this job";
}

// Traitement du formulaire
if (isset($_POST['apply']) && empty($errors)) {

    if (!isset($_FILES['cv']) || $_FILES['cv']['error'] != 0) {
        $errors[] = "CV file is required";
    } else {

        $fileName = $_FILES['cv']['name'];
        $fileTmp = $_FILES['cv']['tmp_name'];
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        // Autoriser seulement PDF
        if ($fileExt !== 'pdf') {
            $errors[] = "Only PDF files are allowed";
        }

        if ($_FILES['cv']['size'] > 2 * 1024 * 1024) {
            $errors[] = "File size must be less than 2MB";
        }
    }

    // Insertion en base
    if (empty($errors)) {

        $newName = uniqid() . ".pdf";
        move_uploaded_file($fileTmp, "../uploads/" . $newName);

        $sql = "INSERT INTO applications (offer_id, user_id, cv_path)
                VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($db->conn, $sql);
        mysqli_stmt_bind_param($stmt, "iis", $offer_id, $user_id, $newName);

        if (mysqli_stmt_execute($stmt)) {
            $success = "Application submitted successfully!";
        } else {
            $errors[] = "Error while applying";
        }
    }
}
$headers = "From: HireHub <jobs@hirehub.local>";
mail($_SESSION['user']['email'],
"Application Received - HireHub",
"Hello ".$_SESSION['user']['name'] .",

We have received your CV successfully.
Your application is under review.

HireHub Team",
$headers);

$pageTitle = "Apply for Job";
require_once "../includes/header.php";
?>

<div class="card">
    <?php if (!empty($job['image_path']) && file_exists("../uploads/" . $job['image_path'])) { ?>
        <div style="margin-bottom: 20px; border-radius: var(--radius-md); overflow: hidden;">
            <img src="../uploads/<?php echo htmlspecialchars($job['image_path']); ?>" 
                 alt="<?php echo htmlspecialchars($job['title']); ?>" 
                 style="width: 100%; max-height: 300px; object-fit: cover; display: block; border-radius: var(--radius-md);">
        </div>
    <?php } ?>
    <h2>Apply for: <?php echo htmlspecialchars($job['title']); ?></h2>
    <p style="color: #666; margin-bottom: 20px;"><?php echo nl2br(htmlspecialchars($job['description'])); ?></p>
</div>

<div class="card" style="max-width: 600px; margin: 0 auto;">
    <h3>Submit Your Application</h3>

    <!-- ERREURS -->
    <?php
    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo "<div class='alert alert-error'>$error</div>";
        }
    }

    if ($success) {
        echo "<div class='alert alert-success'>$success</div>";
        echo "<p><a href='index.php' class='btn'>Back to Job Offers</a></p>";
    } else {
    ?>

    <!-- FORMULAIRE -->
    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label>Upload your CV (PDF only, max 2MB):</label>
            <input id="cv"  type="file" name="cv" accept=".pdf" required>
        </div>

        <button type="submit" name="apply" class="btn">Submit Application</button>
    </form>
    <?php } ?>
</div>

<p class="mt-20">
    <a href="index.php" class="back-link">← Back to job offers</a>
</p>

<?php require_once "../includes/footer.php"; ?>
