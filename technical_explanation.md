# CV Form Application - Technical Explanation

## Architecture Overview

The CV Form application is built using a custom PHP architecture that follows a modified Model-View-Controller (MVC) pattern. The application is organized into distinct layers with specific responsibilities:

1. **Database Layer (BD/)**: Handles data persistence and retrieval
2. **Processing Layer (Traitement/)**: Contains business logic and request handling
3. **Interface Layer (IHM/)**: Manages the presentation and user interaction
4. **Entry Point (index.php)**: Controls routing and request flow

## Request Flow and Execution Cycle

### 1. Entry Point (index.php)

All requests to the application are processed through `index.php`, which acts as a front controller. The execution flow follows these steps:

1. **Session Initialization**: `session_start()` initializes or resumes the user's session
2. **Authentication Handling**: Processes login attempts when username/password are submitted
3. **Action Processing**: Routes action requests (like logout, delete, etc.) to `process.php`
4. **Page Routing**: Determines which page to display based on the `page` parameter
5. **Access Control**: Enforces authentication requirements for protected pages
6. **Page Loading**: Loads the appropriate page file from the IHM directory

### 2. URL Structure and Routing

The application uses query parameters for routing:

- `?page=path/to/page`: Determines which page to display (e.g., `?page=admin/dashboard`)
- `?action=operation`: Triggers specific operations (e.g., `?action=logout`)

For example:
- `index.php?page=admin/login`: Displays the admin login page
- `index.php?action=submit-cv`: Processes a CV submission
- `index.php?page=admin/edit-cv&id=5`: Loads the edit form for CV with ID 5

### 3. Authentication System

Authentication is implemented using PHP sessions:

1. **Login Process**:
   - User submits credentials via the login form
   - `handleAdminLogin()` in `AdminTraitement.php` processes the request
   - `verifyAdminLogin()` in `Admin.php` checks credentials against the database
   - On success, admin ID and username are stored in the session

2. **Session Management**:
   - `isAdminLoggedIn()` checks if an admin session exists
   - `requireAdmin()` and `requireAdminAuth()` enforce authentication for protected pages
   - `handleAdminLogout()` destroys the session on logout

3. **Access Control**:
   - Admin pages (prefixed with `admin/`) require authentication
   - Authenticated users are redirected to the dashboard when accessing public pages
   - Unauthenticated users are redirected to login when accessing protected pages

### 4. Database Operations

The application uses PDO for database access with a singleton connection pattern:

1. **Connection Management**:
   - `getConnexion()` in `Connexion.php` provides a single database connection instance
   - Uses prepared statements for security against SQL injection

2. **CV Operations** (`CV.php`):
   - `getAllCVs()`: Retrieves all CVs for the admin dashboard
   - `getCVById()`: Gets a specific CV for viewing/editing
   - `createCV()`: Inserts a new CV record
   - `updateCV()`: Updates an existing CV
   - `deleteCV()`: Removes a CV and its associated files

3. **Admin Operations** (`Admin.php`):
   - `verifyAdminLogin()`: Authenticates admin credentials

### 5. Business Logic Layer

The `Traitement/` directory contains handler functions that implement business logic:

1. **Admin Processing** (`AdminTraitement.php`):
   - `handleAdminLogin()`: Processes login attempts
   - `handleAdminLogout()`: Manages logout operations
   - `requireAdminAuth()`: Enforces authentication

2. **CV Processing** (`CVTraitement.php`):
   - `handleCVList()`: Retrieves CVs for the dashboard
   - `handleCVDelete()`: Processes CV deletion
   - `handleCVDownload()`: Generates and serves PDF CVs
   - `handleCVSubmit()`: Processes new CV submissions
   - `handleCVUpdate()`: Handles CV updates

3. **Action Routing** (`process.php`):
   - Routes actions to appropriate handler functions based on the `action` parameter

4. **PDF Generation** (`cvModel.php`):
   - Extends FPDF library to create PDF CVs
   - Handles layout, formatting, and UTF-8 text

### 6. User Interface Layer

The `IHM/` directory contains the presentation layer:

1. **Admin Interface**:
   - `dashboard.php`: Lists all CVs with management options
   - `login.php`: Admin authentication form
   - `edit-cv.php`: Form for editing existing CVs

2. **Public Interface**:
   - `cv-form.php`: Form for creating new CVs

3. **Shared Components**:
   - `navbar.php`: Navigation bar with context-aware options

### 7. Form Processing and File Uploads

1. **CV Submission Process**:
   - User submits the CV form with personal information and photo
   - `handleCVSubmit()` validates the input data
   - Photo is uploaded to `uploads/photos/` directory
   - CV data is stored in the database
   - PDF is generated and stored

2. **File Upload Handling**:
   - Photos are validated for type and size
   - Unique filenames are generated to prevent conflicts
   - File paths are stored in the database for reference

### 8. PDF Generation

The application generates PDF CVs using a custom extension of the FPDF library:

1. **PDF Class** (`cvModel.php`):
   - Extends FPDF with UTF-8 support
   - Implements custom header and footer
   - Defines layout for different CV sections

2. **Generation Process**:
   - `generateCV()` creates a new PDF instance
   - Formats and adds personal information
   - Processes arrays (education, experience, etc.) into formatted sections
   - Returns the PDF for download or storage

## Security Considerations

1. **Input Validation**:
   - Form inputs are validated for required fields and format
   - Array inputs (education, experience, etc.) are checked for structure

2. **SQL Injection Prevention**:
   - All database queries use PDO prepared statements
   - Parameters are bound rather than concatenated

3. **Authentication**:
   - Session-based authentication for admin access
   - Protected routes enforce authentication checks

4. **File Upload Security**:
   - File types are validated
   - Secure file paths prevent directory traversal
   - Unique filenames prevent overwriting

5. **Output Escaping**:
   - `htmlspecialchars()` is used to prevent XSS attacks when displaying user data

## Data Flow Diagram

```
User Request → index.php (Router) → Process Action/Load Page
                                   ↓
                 ┌─────────────────┴─────────────────┐
                 ↓                                   ↓
         Action Processing                     Page Loading
         (process.php)                     (IHM/*.php files)
                 ↓                                   ↓
         Handler Functions                    Render Interface
    (AdminTraitement.php/CVTraitement.php)           ↓
                 ↓                             User Response
         Database Operations
         (Admin.php/CV.php)
                 ↓
           Data Storage
           (MySQL DB)
```

## Technical Challenges and Solutions

1. **PDF Generation with UTF-8**:
   - Challenge: FPDF doesn't natively support UTF-8
   - Solution: Custom wrapper methods that convert UTF-8 to ISO-8859-1

2. **Dynamic Form Fields**:
   - Challenge: Handling variable numbers of education/experience entries
   - Solution: JavaScript for dynamic field addition and PHP array processing

3. **Image Handling**:
   - Challenge: Processing different image types for PDF inclusion
   - Solution: Image type detection and appropriate processing based on mime type

4. **Session Management**:
   - Challenge: Maintaining secure admin sessions
   - Solution: Session-based authentication with proper logout handling

## Conclusion

The CV Form application implements a complete CV management system with a clean separation of concerns between database access, business logic, and presentation. The architecture follows PHP best practices for security and maintainability while providing a user-friendly interface for both CV creation and administration. 