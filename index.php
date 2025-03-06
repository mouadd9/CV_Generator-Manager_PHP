<?php
/**
 * Main Entry Point - Application Router and Controller
 * ------------------------------------------------
 * This file serves as the central hub for the entire application.
 * It handles routing, authentication, and request processing.
 */

// Initialize session for user tracking and authentication
session_start();

// Include essential files
require_once 'Traitement/AdminTraitement.php';  // Admin authentication handling

/**
 * AUTHENTICATION HANDLING
 * ---------------------
 * Process login attempts when username and password are submitted
 * This block handles the initial login request before any other processing
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username']) && isset($_POST['password'])) {
    handleAdminLogin($_POST['username'], $_POST['password']);
    exit;
}

/**
 * ACTION PROCESSING
 * ---------------
 * Handle specific actions like logout, CV operations, etc.
 * These are processed before page routing
 */
if (isset($_GET['action'])) {
    require_once 'Traitement/process.php';
    exit;
}

/**
 * PAGE ROUTING
 * -----------
 * Determine which page to display based on the 'page' parameter
 * Default to the CV form if no page is specified
 */
$page = isset($_GET['page']) ? $_GET['page'] : 'public/cv-form';

/**
 * ACCESS CONTROL
 * -------------
 * Handle special routing cases and access restrictions
 */
// Special handling for login page
if ($page === 'admin/login') {
    // Redirect already logged-in users to dashboard
    if (isset($_SESSION['admin_id'])) {
        header('Location: ?page=admin/dashboard');
        exit;
    }
} 
// Protect admin area - require authentication
else if (strpos($page, 'admin/') === 0) {
    requireAdminAuth();
} 
// Redirect logged-in users trying to access public pages
else if (isset($_SESSION['admin_id'])) {
    header('Location: ?page=admin/dashboard');
    exit;
}

/**
 * SECURITY AND PAGE LOADING
 * -----------------------
 * Sanitize the page parameter and load the appropriate file
 */
// Remove any potentially harmful characters from the page path
$page = preg_replace('/[^a-zA-Z0-9\-\/]/', '', $page);
// Prevent directory traversal attacks
$page = str_replace('..', '', $page);

// Construct the full path to the page file
$page_file = 'IHM/' . $page . '.php';

// Load the page if it exists, otherwise redirect to the default page
if (file_exists($page_file)) {
    require_once $page_file;
} else {
    header('Location: ?page=public/cv-form');
    exit;
}

/**
 * URL PARAMETER EXPLANATION
 * ----------------------
 * Everything after the ? in the URL are query parameters:
 * - Parameters follow key=value format
 * - Multiple parameters separated by &
 * - Example: http://localhost/index.php?action=post&name=john&age=25
 * - Accessible via $_GET array in PHP
 */



