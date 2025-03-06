<?php
/**
 * CV Processing Controller
 * ----------------------
 * This file contains functions that handle CV-related operations.
 * It serves as a bridge between the interface and the database layer.
 */

require_once 'BD/CV.php';
require_once 'Traitement/cvModel.php';

/**
 * Handle CV List Retrieval
 * ----------------------
 * Retrieves all CVs for display in the admin dashboard.
 * Requires admin authentication.
 * 
 * @return array List of all CVs in the database
 */
function handleCVList() {
    requireAdminAuth();
    return getAllCVs();
}

/**
 * Handle CV Deletion
 * ----------------
 * Processes CV deletion requests.
 * Requires admin authentication.
 * 
 * Process:
 * 1. Verifies the request method and CV ID
 * 2. Attempts to delete the CV
 * 3. Sets appropriate success/error messages
 * 4. Redirects back to the dashboard
 * 
 * @return void
 */
function handleCVDelete() {
    requireAdminAuth();
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
        if (deleteCV($_POST['id'])) {
            $_SESSION['success'] = 'CV deleted successfully';
        } else {
            $_SESSION['error'] = 'Error deleting CV';
        }
    }
    header('Location: ?page=admin/dashboard');
    exit;
}

/**
 * Handle CV Download
 * ---------------
 * Generates and serves a PDF version of a CV.
 * Requires admin authentication.
 * 
 * Process:
 * 1. Retrieves the CV data by ID
 * 2. Formats the data for PDF generation
 * 3. Generates the PDF
 * 4. Serves the PDF for download
 * 
 * @return void
 */
function handleCVDownload() {
    requireAdminAuth();
    
    if (isset($_GET['id'])) {
        $cv = getCVById($_GET['id']);
        if ($cv) {
            // Format the data as expected by generateCV function
            $data = [
                'prenom' => $cv['first_name'],
                'nom' => $cv['last_name'],
                'titre' => $cv['job_title'],
                'email' => $cv['email'],
                'telephone' => $cv['phone'],
                'adresse' => $cv['address'],
                'code_postal' => $cv['postal_code'],
                'ville' => $cv['city'],
                'description' => $cv['description'],
                'photo' => [
                    'tmp_name' => $cv['photo_path']
                ],
                'formations' => json_decode($cv['education'], true),
                'experiences' => json_decode($cv['experience'], true),
                'competences' => json_decode($cv['skills'], true),
                'langues' => json_decode($cv['languages'], true)
            ];
            
            // Generate the PDF
            $pdf = generateCV($data);
            $filename = $cv['first_name'] . '_' . $cv['last_name'] . '_CV.pdf';
            
            // Set headers for PDF download
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Cache-Control: max-age=0');
            
            // Output the PDF and exit
            $pdf->Output('D', $filename);
            exit;
        }
    }
    
    // If we get here, something went wrong
    $_SESSION['error'] = 'CV not found or could not be downloaded';
    header('Location: ?page=admin/dashboard');
    exit;
}

/**
 * Handle CV Submission
 * -----------------
 * Processes new CV submissions from the public form.
 * 
 * Process:
 * 1. Validates required fields and array inputs
 * 2. Handles photo upload
 * 3. Creates the CV record in the database
 * 4. Generates and downloads the PDF
 * 
 * @return void
 */
function handleCVSubmit() {
    // Only process POST requests
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: ?page=public/cv-form');
        exit;
    }
    
    // Validate required fields
    $requiredFields = ['prenom', 'nom', 'titre', 'email', 'telephone', 'adresse', 'code_postal', 'ville', 'description'];
    foreach ($requiredFields as $field) {
        if (empty($_POST[$field])) {
            $_SESSION['error'] = 'All fields are required';
            header('Location: ?page=public/cv-form');
            exit;
        }
    }
    
    // Validate array fields
    $arrayFields = ['formations', 'experiences', 'competences', 'langues'];
    foreach ($arrayFields as $field) {
        if (!isset($_POST[$field]) || !is_array($_POST[$field])) {
            $_SESSION['error'] = 'Invalid form data';
            header('Location: ?page=public/cv-form');
            exit;
        }
    }
    
    // Validate photo upload
    if (!isset($_FILES['photo']) || $_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
        $_SESSION['error'] = 'Photo upload is required';
        header('Location: ?page=public/cv-form');
        exit;
    }
    
    // Handle photo upload
    $uploadDir = 'uploads/photos/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    $photoName = uniqid() . '_' . basename($_FILES['photo']['name']);
    $photoPath = $uploadDir . $photoName;
    
    if (!move_uploaded_file($_FILES['photo']['tmp_name'], $photoPath)) {
        $_SESSION['error'] = 'Failed to upload photo';
        header('Location: ?page=public/cv-form');
        exit;
    }
    
    // Prepare data for database
    $data = $_POST;
    $data['photo'] = [
        'tmp_name' => $photoPath
    ];
    
    try {
        // Create CV in database
        $cvId = createCV($data);
        
        if ($cvId) {
            // Generate and download the PDF
            // Clear any previous output to prevent PDF corruption
            if (ob_get_length()) ob_clean();
            
            // Generate the PDF
            $pdf = generateCV($data);
            $filename = $data['prenom'] . '_' . $data['nom'] . '_CV.pdf';
            
            // Set headers for PDF download
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Cache-Control: max-age=0');
            
            // Output the PDF and exit
            $pdf->Output('D', $filename);
            exit;
        } else {
            $_SESSION['error'] = 'Failed to create CV';
            header('Location: ?page=public/cv-form');
        }
    } catch (Exception $e) {
        $_SESSION['error'] = 'An error occurred: ' . $e->getMessage();
        header('Location: ?page=public/cv-form');
    }
    
    exit;
}

/**
 * Handle CV Update
 * -------------
 * Processes CV update requests from the admin edit form.
 * Requires admin authentication.
 * 
 * Process:
 * 1. Validates required fields and array inputs
 * 2. Handles photo upload if a new photo is provided
 * 3. Updates the CV record in the database
 * 4. Sets appropriate success/error messages
 * 5. Redirects back to the dashboard
 * 
 * @return void
 */
function handleCVUpdate() {
    // Require admin authentication
    requireAdminAuth();
    
    // Only process POST requests
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: ?page=admin/dashboard');
        exit;
    }
    
    // Check if CV ID is provided
    if (!isset($_POST['id'])) {
        $_SESSION['error'] = 'CV ID is required';
        header('Location: ?page=admin/dashboard');
        exit;
    }
    
    $id = $_POST['id'];
    
    // Validate required fields
    $requiredFields = ['prenom', 'nom', 'titre', 'email', 'telephone', 'adresse', 'code_postal', 'ville', 'description'];
    foreach ($requiredFields as $field) {
        if (empty($_POST[$field])) {
            $_SESSION['error'] = 'All fields are required';
            header("Location: ?page=admin/edit-cv&id=$id");
            exit;
        }
    }
    
    // Validate array fields
    $arrayFields = ['formations', 'experiences', 'competences', 'langues'];
    foreach ($arrayFields as $field) {
        if (!isset($_POST[$field]) || !is_array($_POST[$field])) {
            $_SESSION['error'] = 'Invalid form data';
            header("Location: ?page=admin/edit-cv&id=$id");
            exit;
        }
    }
    
    // Get current CV data
    $cv = getCVById($id);
    if (!$cv) {
        $_SESSION['error'] = 'CV not found';
        header('Location: ?page=admin/dashboard');
        exit;
    }
    
    // Handle photo upload if a new photo is provided
    $photoPath = $cv['photo_path'];
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/photos/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        // Delete old photo if it exists
        if (file_exists($photoPath)) {
            unlink($photoPath);
        }
        
        $photoName = uniqid() . '_' . basename($_FILES['photo']['name']);
        $photoPath = $uploadDir . $photoName;
        
        if (!move_uploaded_file($_FILES['photo']['tmp_name'], $photoPath)) {
            $_SESSION['error'] = 'Failed to upload photo';
            header("Location: ?page=admin/edit-cv&id=$id");
            exit;
        }
    }
    
    // Prepare data for database update
    $data = [
        'first_name' => $_POST['prenom'],
        'last_name' => $_POST['nom'],
        'job_title' => $_POST['titre'],
        'email' => $_POST['email'],
        'phone' => $_POST['telephone'],
        'address' => $_POST['adresse'],
        'postal_code' => $_POST['code_postal'],
        'city' => $_POST['ville'],
        'description' => $_POST['description'],
        'photo_path' => $photoPath,
        'education' => json_encode($_POST['formations']),
        'experience' => json_encode($_POST['experiences']),
        'skills' => json_encode($_POST['competences']),
        'languages' => json_encode($_POST['langues'])
    ];
    
    // Update CV in database
    if (updateCV($id, $data)) {
        $_SESSION['success'] = 'CV updated successfully';
        header('Location: ?page=admin/dashboard');
    } else {
        $_SESSION['error'] = 'Failed to update CV';
        header("Location: ?page=admin/edit-cv&id=$id");
    }
    
    exit;
} 