<?php
require_once 'Traitement/AdminTraitement.php';
require_once 'Traitement/CVTraitement.php';

requireAdminAuth();
$cvs = handleCVList();

$success = isset($_SESSION['success']) ? $_SESSION['success'] : '';
unset($_SESSION['success']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - CV Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="?page=admin/dashboard">Admin Dashboard</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="?action=logout">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="card">
            <div class="card-header">
                <h3>CV List</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Job Title</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cvs as $cv): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($cv['name']); ?></td>
                                    <td><?php echo htmlspecialchars($cv['email']); ?></td>
                                    <td><?php echo htmlspecialchars($cv['phone']); ?></td>
                                    <td><?php echo htmlspecialchars($cv['job_title']); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($cv['created_at'])); ?></td>
                                    <td>
                                        <a href="?page=admin/edit-cv&id=<?php echo $cv['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                                        <a href="?action=download&id=<?php echo $cv['id']; ?>" class="btn btn-sm btn-success">Download</a>
                                        <form method="POST" action="?action=delete" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this CV?');">
                                            <input type="hidden" name="id" value="<?php echo $cv['id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 