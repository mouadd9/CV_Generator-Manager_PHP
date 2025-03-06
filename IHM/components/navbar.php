<?php
function displayNavbar() {
    $isAdmin = isset($_SESSION['admin_id']);
    ?>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container">
            <a href="?page=public/cv-form" class="navbar-brand">CV Form</a>
            <div class="ms-auto">
                <?php if ($isAdmin): ?>
                    <span class="text-light me-3">Welcome, <?php echo htmlspecialchars($_SESSION['admin_username']); ?></span>
                    <a href="?action=logout" class="btn btn-danger">Logout</a>
                <?php else: ?>
                    <a href="?page=admin/login" class="btn btn-primary">Admin Login</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
    <?php
} 