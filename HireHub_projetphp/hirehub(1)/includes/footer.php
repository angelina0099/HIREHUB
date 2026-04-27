        </div>
    </main>
    <footer>
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> HireHub. All rights reserved.</p>
        </div>
    </footer>
    <?php 
    // Use same base path as header
    $isAdmin = strpos($_SERVER['PHP_SELF'], '/admin/') !== false;
    $isError = strpos($_SERVER['PHP_SELF'], '/errors/') !== false;
    $isPublic = strpos($_SERVER['PHP_SELF'], '/public/') !== false;
    $basePath = ($isAdmin || $isError) ? '../' : ($isPublic ? '../' : '');
    ?>
    <script src="<?php echo $basePath; ?>assets/js/main.js"></script>
</body>
</html>
