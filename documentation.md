# CV Form Application Documentation

## Application Structure Overview

### Root Directory
- `index.php`: Main entry point of the application
  * Handles routing between different pages
  * Manages session initialization
  * Controls access to admin/public areas
  * Processes form submissions and actions

### BD (Database) Directory
Contains all database-related functionality:

- `Connexion.php`:
  * Manages database connection using PDO
  * Implements singleton pattern for connection reuse
  * Used by all other database operations

- `Admin.php`:
  * Handles admin authentication
  * Verifies admin credentials
  * Manages admin sessions
  * Related to: `AdminTraitement.php`, `login.php`

- `CV.php`:
  * Core database operations for CVs
  * Functions: getAllCVs, getCVById, createCV, updateCV, deleteCV
  * Handles CRUD operations for CV data
  * Related to: `CVTraitement.php`, `edit-cv.php`, `dashboard.php`

### IHM (Interface Homme-Machine) Directory
User interface components:

#### admin/
- `dashboard.php`:
  * Admin control panel
  * Displays list of all CVs
  * Provides actions: edit, download, delete
  * Related to: `CV.php`, `AdminTraitement.php`

- `login.php`:
  * Admin authentication form
  * Handles login attempts
  * Related to: `Admin.php`, `AdminTraitement.php`

- `edit-cv.php`:
  * CV editing interface
  * Pre-fills form with existing CV data
  * Handles CV updates
  * Related to: `CV.php`, `CVTraitement.php`

#### public/
- `cv-form.php`:
  * Main CV creation form
  * Handles new CV submissions
  * Related to: `CVTraitement.php`

- `style.css`:
  * Global stylesheet
  * Defines application appearance
  * Used by all pages

#### components/
- `navbar.php`:
  * Reusable navigation component
  * Shows different options for admin/public users
  * Used across all pages

### Traitement (Processing) Directory
Business logic and request handling:

- `AdminTraitement.php`:
  * Processes admin authentication
  * Handles login/logout
  * Access control
  * Related to: `Admin.php`, `login.php`

- `CVTraitement.php`:
  * Processes CV operations
  * Handles file uploads
  * Manages CV creation/updates
  * Related to: `CV.php`, `cvModel.php`

- `cvModel.php`:
  * PDF generation logic
  * CV layout and formatting
  * Uses FPDF library
  * Related to: `CVTraitement.php`

- `debug.php`:
  * Debugging utilities
  * Formats and displays debug information
  * Used across all files for troubleshooting

### uploads/ Directory
- Stores uploaded files:
  * `photos/`: CV profile pictures
  * Generated PDFs

## Key Relationships and Dependencies

1. Authentication Flow:
   * `login.php` → `AdminTraitement.php` → `Admin.php` → `Connexion.php`

2. CV Creation Flow:
   * `cv-form.php` → `CVTraitement.php` → `CV.php` → `Connexion.php`
   * `CVTraitement.php` → `cvModel.php` (for PDF generation)

3. CV Management Flow:
   * `dashboard.php` → `CVTraitement.php` → `CV.php`
   * `edit-cv.php` → `CVTraitement.php` → `CV.php`

## Security Measures
- Session-based authentication
- PDO prepared statements for SQL injection prevention
- Input validation and sanitization
- Access control checks
- Secure file upload handling

## Database Schema
1. admins table:
   - id (Primary Key)
   - username
   - password (hashed)
   - created_at

2. cvs table:
   - id (Primary Key)
   - first_name
   - last_name
   - job_title
   - email
   - phone
   - address
   - postal_code
   - city
   - description
   - photo_path
   - education (JSON)
   - experience (JSON)
   - skills (JSON)
   - languages (JSON)
   - created_at 