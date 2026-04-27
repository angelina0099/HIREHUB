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

// ---------- SUPPRESSION ----------
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {

    $id = (int) $_GET['delete'];

    // Supprimer l'image associée
    $imgQuery = mysqli_prepare($db->conn, "SELECT image_path FROM job_offers WHERE id=?");
    mysqli_stmt_bind_param($imgQuery, "i", $id);
    mysqli_stmt_execute($imgQuery);
    $imgResult = mysqli_stmt_get_result($imgQuery);
    $imgRow = mysqli_fetch_assoc($imgResult);

    if (!empty($imgRow['image_path']) && file_exists("../uploads/" . $imgRow['image_path'])) {
        unlink("../uploads/" . $imgRow['image_path']);
    }

    $sql = "DELETE FROM job_offers WHERE id=?";
    $stmt = mysqli_prepare($db->conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);

    if (mysqli_stmt_execute($stmt)) {
        $success = "Job offer deleted successfully";
    } else {
        $errors[] = "Error while deleting job offer";
    }
}

// ---------- MODIFICATION ----------
if (isset($_POST['update'])) {

    $id = (int) $_POST['id'];
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $current_image = $_POST['current_image'] ?? null;
    $image_path = $current_image;

    if (empty($title) || empty($description)) {
        $errors[] = "All fields are required";
    }

    // -------- TRAITEMENT IMAGE (OPTIONNEL) --------
    if (!empty($_FILES['image']['name'])) {

        $fileTmp  = $_FILES['image']['tmp_name'];
        $fileSize = $_FILES['image']['size'];
        $fileExt  = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

        $allowedExt = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (!in_array($fileExt, $allowedExt)) {
            $errors[] = "Only JPG, PNG, GIF or WEBP images are allowed";
        } elseif ($fileSize > 5 * 1024 * 1024) {
            $errors[] = "Image must be less than 5MB";
        } else {

            $uploadDir = "../uploads/jobs/";
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            // Supprimer l'ancienne image
            if ($current_image && file_exists("../uploads/" . $current_image)) {
                unlink("../uploads/" . $current_image);
            }

            $newName = uniqid("job_", true) . "." . $fileExt;
            $targetPath = $uploadDir . $newName;

            if (move_uploaded_file($fileTmp, $targetPath)) {
                $image_path = "jobs/" . $newName;
            } else {
                $errors[] = "Error uploading image";
            }
        }
    }

    // -------- UPDATE --------
    if (empty($errors)) {

        $sql = "UPDATE job_offers 
                SET title=?, description=?, image_path=?
                WHERE id=?";

        $stmt = mysqli_prepare($db->conn, $sql);
        mysqli_stmt_bind_param($stmt, "sssi", $title, $description, $image_path, $id);

        if (mysqli_stmt_execute($stmt)) {
            $success = "Job offer updated successfully";
        } else {
            $errors[] = "Error while updating job offer";
        }
    }
}

// ---------- LISTE DES OFFRES ----------
$result = mysqli_query($db->conn, "SELECT * FROM job_offers ORDER BY created_at DESC");

$pageTitle = "Manage Job Offers";
require_once "../includes/header.php";
?>

<div class="hero-section">
    <div class="hero-content">
        <h1>Manage Job Offers</h1>
        <p>Edit or delete existing job offers</p>
    </div>
</div>

<?php
foreach ($errors as $error) {
    echo "<div class='alert alert-error'>$error</div>";
}
if ($success) {
    echo "<div class='alert alert-success'>$success</div>";
}
?>

<?php if (mysqli_num_rows($result) > 0) { ?>
<div class="job-list">

<?php while ($job = mysqli_fetch_assoc($result)) { ?>
<div class="job-card">

<form method="POST" enctype="multipart/form-data">

<input type="hidden" name="id" value="<?php echo $job['id']; ?>">
<input type="hidden" name="current_image" value="<?php echo htmlspecialchars($job['image_path'] ?? ''); ?>">

<?php if (!empty($job['image_path']) && file_exists("../uploads/" . $job['image_path'])) { ?>
    <img src="../uploads/<?php echo htmlspecialchars($job['image_path']); ?>" class="job-image">
<?php } else { ?>
    <img src="../assets/images/default.png" class="job-image">
<?php } ?>

<div class="form-group">
    <label>Title</label>
    <input type="text" name="title" value="<?php echo htmlspecialchars($job['title']); ?>" required>
</div>

<div class="form-group">
    <label>Description</label>
    <textarea name="description" rows="6" required><?php echo htmlspecialchars($job['description']); ?></textarea>
</div>

<div class="form-group">
    <label>Update Image (optional)</label>
    <input type="file" name="image" accept="image/jpeg,image/png,image/gif,image/webp">
</div>

<div class="job-footer">
    <button type="submit" name="update" class="btn">Update</button>
    <a href="?delete=<?php echo $job['id']; ?>" class="btn btn-danger"
       onclick="return confirm('Are you sure?')">Delete</a>
</div>

</form>
</div>
<?php } ?>

</div>
<?php } else { ?>

<div class="card">
    <p>No job offers found.</p>
    <a href="jobs_add.php" class="btn">Add Job Offer</a>
</div>

<?php } ?>

<a href="dashboard.php" class="back-link">← Back to dashboard</a>

<?php require_once "../includes/footer.php"; ?>
