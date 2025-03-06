<?php
/**
 * Action Processing Controller
 * --------------------------
 * This file routes action requests to the appropriate handler functions.
 * It serves as a central hub for all operations triggered by the 'action' parameter.
 * 
 * How it works:
 * 1. Gets the 'action' parameter from the URL
 * 2. Routes to the appropriate handler function based on the action
 * 3. Sets appropriate messages and redirects
 */

// Include required files
require_once __DIR__ . '/../BD/Admin.php';
require_once __DIR__ . '/AdminTraitement.php';
require_once __DIR__ . '/CVTraitement.php';

// Get the action from GET parameter
$action = $_GET['action'] ?? '';



/**
 * Action Routing
 * ------------
 * Routes each action to its corresponding handler function.
 * Each case handles a specific operation in the application.
 */
switch ($action) {
    case 'logout':
        // Handle admin logout
        handleAdminLogout();
        break;
        
    case 'delete':
        // Delete a CV
        handleCVDelete();
        break;
        
    case 'download':
        // Clear any previous output to prevent PDF corruption
        ob_clean();
        // Generate and download a CV as PDF
        handleCVDownload();
        break;
        
    case 'submit-cv':
        // Clear any previous output to prevent PDF corruption
        ob_clean();
        // Process new CV submission and generate PDF
        handleCVSubmit();
        break;
        
    case 'update-cv':
        // Process CV update
        handleCVUpdate();
        break;
        
    default:
        // Handle invalid action
        $_SESSION['error'] = 'Invalid action';
        header('Location: ?page=public/cv-form');
        exit;
} 