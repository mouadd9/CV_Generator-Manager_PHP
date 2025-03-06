<?php
/**
 * Database Connection Manager
 * -------------------------
 * This file manages the database connection using the Singleton pattern
 * to ensure only one connection is created and reused throughout the application.
 * 
 * Benefits:
 * - Prevents multiple unnecessary database connections
 * - Centralizes database configuration
 * - Provides consistent error handling
 */

/**
 * Get Database Connection
 * ---------------------
 * Returns a PDO database connection instance.
 * Creates a new connection if none exists, otherwise returns the existing one.
 * 
 * @return PDO The database connection object
 * @throws PDOException If connection fails
 */
function getConnexion() {
    // Static variable persists between function calls
    static $connexion = null;
    
    // Only create a new connection if one doesn't exist
    if ($connexion === null) {
        try {
            // Create new PDO connection with error handling configuration
            $connexion = new PDO(
                'mysql:host=localhost;dbname=cv_form;charset=utf8mb4',  // Connection string with UTF-8 support
                'root',                                                 // Database username
                '',                                                     // Database password
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]          // Enable exception-based error handling
            );
        } catch (PDOException $e) {
            // Log error and terminate execution if connection fails
            die('Erreur de connexion : ' . $e->getMessage());
        }
    }
    
    // Return the existing or newly created connection
    return $connexion;
} 