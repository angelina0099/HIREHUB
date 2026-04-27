<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' : ''; ?>HireHub</title>
    <?php 
    // Determine base path based on current directory
    $isAdmin = strpos($_SERVER['PHP_SELF'], '/admin/') !== false;
    $isError = strpos($_SERVER['PHP_SELF'], '/errors/') !== false;
    $isPublic = strpos($_SERVER['PHP_SELF'], '/public/') !== false;
    $basePath = ($isAdmin || $isError) ? '../' : ($isPublic ? '../' : '');
    ?>
    <link rel="stylesheet" href="<?php echo $basePath; ?>assets/css/style.css">
</head>
<body>
    <header>
        <nav>
            <div class="container">
                <?php
                // Fix navigation paths based on current location
                $homeLink = $isPublic ? 'index.php' : ($isAdmin || $isError ? '../public/index.php' : 'public/index.php');
                $loginLink = $isPublic ? 'login.php' : ($isAdmin || $isError ? '../public/login.php' : 'public/login.php');
                $registerLink = $isPublic ? 'register.php' : ($isAdmin || $isError ? '../public/register.php' : 'public/register.php');
                $logoutLink = $isPublic ? 'logout.php' : ($isAdmin || $isError ? '../public/logout.php' : 'public/logout.php');
                $myAppsLink = $isPublic ? 'my_application.php' : ($isAdmin || $isError ? '../public/my_application.php' : 'public/my_application.php');
                $dashboardLink = $isAdmin ? 'dashboard.php' : ($isPublic ? '../admin/dashboard.php' : 'admin/dashboard.php');
                ?>
                <a href="<?php echo $homeLink; ?>" class="logo">HireHub</a>
                <ul class="nav-links">
                    <?php if (isset($_SESSION['user'])) { ?>
                        <li>
                            <span class="user-info">
                                Welcome <strong><?php echo htmlspecialchars($_SESSION['user']['name']); ?></strong>
                            </span>
                        </li>
                        <?php if ($_SESSION['user']['role'] === 'ADMIN') { ?>
                            <li><a href="<?php echo $dashboardLink; ?>">Dashboard</a></li>
                        <?php } else { ?>
                            <li><a href="<?php echo $myAppsLink; ?>">My Applications</a></li>
                        <?php } ?>
                        <li><a href="<?php echo $logoutLink; ?>">Logout</a></li>
                    <?php } else { ?>
                        <li><a href="<?php echo $loginLink; ?>">Login</a></li>
                        <li><a href="<?php echo $registerLink; ?>">Register</a></li>
                    <?php } ?>
                </ul>
            </div>
        </nav>
    </header>
    <main>
        <div class="container">
