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
$errors = [];
$success = "";

// Traitement du formulaire
if (isset($_POST['add_job'])) {

    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $image_path = null;

    // -------- VALIDATION --------
    if (empty($title) || empty($description)) {
        $errors[] = "Title and description are required";
    }

    if (strlen($title) < 5) {
        $errors[] = "Job title must be at least 5 characters";
    }

    // -------- TRAITEMENT DE L'IMAGE (ADMIN) --------
    if (!empty($_FILES['image']['name'])) {

        $fileName = $_FILES['image']['name'];
        $fileTmp  = $_FILES['image']['tmp_name'];
        $fileSize = $_FILES['image']['size'];
        $fileExt  = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        $allowedExt = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (!in_array($fileExt, $allowedExt)) {
            $errors[] = "Only JPG, PNG, GIF or WEBP images are allowed";
        } elseif ($fileSize > 5 * 1024 * 1024) {
            $errors[] = "Image size must be less than 5MB";
        } else {

            $uploadDir = "../uploads/jobs/";

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $newName = uniqid("job_", true) . "." . $fileExt;
            $targetPath = $uploadDir . $newName;

            if (move_uploaded_file($fileTmp, $targetPath)) {
                // On stocke seulement le chemin relatif
                $image_path = "jobs/" . $newName;
            } else {
                $errors[] = "Failed to upload image";
            }
        }
    }

    // -------- INSERTION --------
    if (empty($errors)) {

        $sql = "INSERT INTO job_offers (title, description, image_path)
                VALUES (?, ?, ?)";

        $stmt = mysqli_prepare($db->conn, $sql);
        mysqli_stmt_bind_param($stmt, "sss", $title, $description, $image_path);

        if (mysqli_stmt_execute($stmt)) {
            $success = "Job offer added successfully";
            $title = "";
            $description = "";
        } else {
            $errors[] = "Error while adding job offer";
        }
    }
}

$pageTitle = "Add Job Offer";
require_once "../includes/header.php";
?>

<div class="card">
    <h2>Add Job Offer</h2>
</div>

<div class="card" style="max-width: 800px; margin: 0 auto;">

    <?php
    foreach ($errors as $error) {
        echo "<div class='alert alert-error'>$error</div>";
    }

    if ($success) {
        echo "<div class='alert alert-success'>$success</div>";
    }
    ?>

    <form method="POST" enctype="multipart/form-data">

        <div class="form-group">
            <label>Job Title</label>
            <input type="text" name="title"
                   value="<?php echo htmlspecialchars($title ?? ''); ?>" required>
        </div>

        <div class="form-group">
            <label>Description</label>
            <textarea name="description" rows="8" required><?php
                echo htmlspecialchars($description ?? '');
            ?></textarea>
        </div>

        <div class="form-group">
            <label>Job Image (optional)</label>
            <input type="file" name="image"
                   accept="image/jpeg,image/png,image/gif,image/webp">
            <small>Max size 5MB – JPG, PNG, GIF, WEBP</small>
        </div>

        <button type="submit" name="add_job" class="btn">Add Job Offer</button>
        <a href="dashboard.php" class="btn btn-secondary">Cancel</a>

    </form>
</div>

<?php require_once "../includes/footer.php"; ?>
