<?php
// Démarrer la session
session_start();

// Inclure la classe User
require_once "../classes/User.php";

// Initialisation (évite Undefined variable)

$errors = [];
$success = "";
$name = "";
$email = "";

// Vérifier si le formulaire est soumis
if (isset($_POST['register'])) {

    // Récupérer et nettoyer les données
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // -------- VALIDATION --------
    if (empty($name) || empty($email) || empty($password)) {
        $errors[] = "All fields are required";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }

    if (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters";
    }

    // -------- INSERTION --------
    if (empty($errors)) {
        $user = new User();

        if ($user->register($name, $email, $password)) {
            $success = "Account created successfully. You can now login.";

            // ✅ ENVOI EMAIL (SEULEMENT SI INSCRIPTION OK)
            $headers = "From: HireHub <welcome@hirehub.local>";
            mail(
                $email,
                "Welcome to HireHub 🎉",
                "Hello $name,

Welcome to HireHub!
Your account has been successfully created.

HireHub Team",
                $headers
            );

        } else {
            $errors[] = "Email already exists";
        }
    }
}

$pageTitle = "Register";
require_once "../includes/header.php";
?>

<div class="card" style="max-width: 500px; margin: 0 auto;">
    <div style="text-align: center; margin-bottom: 32px;">
        <h2 style="margin-bottom: 8px;">Create an Account</h2>
        <p style="color: var(--text-secondary);">Join HireHub and start your career journey</p>
    </div>

    <!-- AFFICHAGE DES ERREURS -->
    <?php if (!empty($errors)): ?>
        <?php foreach ($errors as $error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endforeach; ?>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <!-- FORMULAIRE -->
    <form method="POST" action="">
        <div class="form-group">
            <label>Name:</label>
            <input type="text" name="name" value="<?= htmlspecialchars($name) ?>" required>
        </div>

        <div class="form-group">
            <label>Email:</label>
            <input type="email" name="email" value="<?= htmlspecialchars($email) ?>" required>
        </div>

        <div class="form-group">
            <label>Password:</label>
            <input type="password" name="password" required>
            <small style="color: #666; display: block; margin-top: 5px;">
                Minimum 6 characters
            </small>
        </div>

        <button type="submit" name="register" class="btn" style="width: 100%;">
            🚀 Create Account
        </button>
    </form>

    <p class="mt-20 text-center">
        <a href="login.php">Already have an account? Login</a>
    </p>
</div>

<?php require_once "../includes/footer.php"; ?>
