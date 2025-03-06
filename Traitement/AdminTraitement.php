<?php
/**
 * Admin Authentication Processing
 * -----------------------------
 * This file contains functions that handle admin authentication operations.
 * It serves as a bridge between the interface and the database layer.
 */

require_once 'BD/Admin.php';

/**
 * Handle Admin Login
 * ----------------
 * Processes admin login attempts and manages session creation.
 * 
 * Process:
 * 1. Verifies credentials against the database
 * 2. Creates a session if authentication succeeds
 * 3. Sets appropriate redirect and error messages
 * 
 * @param string $username The submitted username
 * @param string $password The submitted password
 * @return void
 */
function handleAdminLogin($username, $password) {
    // Attempt to verify admin credentials
    if ($admin = verifyAdminLogin($username, $password)) {
        // Authentication successful - create session
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];
        header('Location: ?page=admin/dashboard');
    } else {
        // Authentication failed - set error message
        $_SESSION['login_error'] = 'Invalid username or password';
        header('Location: ?page=admin/login');
    }
    exit;
}

/**
 * Handle Admin Logout
 * -----------------
 * Terminates the admin session and cleans up session data.
 * 
 * Process:
 * 1. Clears all session variables
 * 2. Destroys the session cookie
 * 3. Destroys the session
 * 4. Redirects to the public area
 * 
 * @return void
 */
function handleAdminLogout() {
    // Clear all session variables
    $_SESSION = array();

    // Destroy the session cookie
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time()-3600, '/');
    }

    // Destroy the session
    session_destroy();

    // Redirect to login page
    header('Location: ?page=public/cv-form');
    exit;
}

/**
 * Require Admin Authentication
 * --------------------------
 * Enforces admin authentication for protected pages.
 * Redirects to login page if not authenticated.
 * 
 * @return void
 */
function requireAdminAuth() {
    //  if (!isset($_SESSION['admin_id'])) {
    if (!isAdminLoggedIn()) {
        header('Location: ?page=admin/login');
        exit;
    }
} 