<?php
// Démarrer la session
session_start();

// Inclure la classe User
require_once "../classes/User.php";

// Tableau des erreurs
$errors = [];

// Vérifier si le formulaire est envoyé
if (isset($_POST['login'])) {

    // Récupérer les données
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // -------- VALIDATION --------
    if (empty($email) || empty($password)) {
        $errors[] = "All fields are required";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }

    // -------- AUTHENTIFICATION --------
    if (empty($errors)) {
        $user = new User();

        if ($user->login($email, $password)) {

            // Redirection selon le rôle
            if ($_SESSION['user']['role'] === 'ADMIN') {
                header("Location: ../admin/dashboard.php");
            } else {
                header("Location: index.php");
            }
            exit;

        } else {
            $errors[] = "Incorrect email or password";
        }
    }
}

$pageTitle = "Login";
require_once "../includes/header.php";
?>

<div class="card" style="max-width: 500px; margin: 0 auto;">
    <div style="text-align: center; margin-bottom: 32px;">
        <h2 style="margin-bottom: 8px;">Welcome Back</h2>
        <p style="color: var(--text-secondary);">Sign in to your account to continue</p>
    </div>

    <!-- AFFICHAGE DES ERREURS -->
    <?php
    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo "<div class='alert alert-error'>$error</div>";
        }
    }
    ?>

    <!-- FORMULAIRE -->
    <form method="POST" action="">
        <div class="form-group">
            <label>Email:</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
        </div>

        <div class="form-group">
            <label>Password:</label>
            <input type="password" name="password" required>
        </div>

        <button type="submit" name="login" class="btn" style="width: 100%;">🔐 Sign In</button>
    </form>

    <p class="mt-20 text-center">
        <a href="register.php">Create an account</a>
    </p>
</div>

<?php require_once "../includes/footer.php"; ?>
