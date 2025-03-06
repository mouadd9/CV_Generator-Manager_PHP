<?php
require_once 'Connexion.php';

function verifyAdminLogin($username, $password) {
    $connexion = getConnexion();

    $query = "SELECT id, username, password FROM admins WHERE username = :username AND password = :password";
    $stmt = $connexion->prepare($query);
    $stmt->execute([
        'username' => $username,
        'password' => $password
    ]);
    
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($admin) {
        return $admin;
    }
    
    return false;
}

function isAdminLoggedIn() {
    return isset($_SESSION['admin_id']);
}

function requireAdmin() {
    if (!isAdminLoggedIn()) {
        header('Location: ?page=admin/login');
        exit;
    }
} 