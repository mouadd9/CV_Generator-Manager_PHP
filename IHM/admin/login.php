<?php
/**
 * Admin Login Page
 * --------------
 * This page provides the login form for admin authentication.
 * It redirects already authenticated users to the dashboard.
 */

// Redirect already logged-in users to dashboard
if (isset($_SESSION['admin_id'])) {
    header('Location: ?page=admin/dashboard');
    exit;
}

// Get error message from session if exists
$error = isset($_SESSION['login_error']) ? $_SESSION['login_error'] : '';
unset($_SESSION['login_error']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - CV Form</title>
    <!-- Bootstrap CSS for styling -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-6">
                <!-- Login card -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-center">Admin Login</h3>
                    </div>
                    <div class="card-body">
                        <!-- Display error message if exists -->
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                        <?php endif; ?>
                        
                        <!-- Login form -->
                        <form method="POST" action="index.php">
                            <!-- Username field -->
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            
                            <!-- Password field -->
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            
                            <!-- Submit button -->
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Login</button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Back to public area link -->
                <div class="text-center mt-3">
                    <a href="?page=public/cv-form" class="text-decoration-none">Back to CV Form</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 