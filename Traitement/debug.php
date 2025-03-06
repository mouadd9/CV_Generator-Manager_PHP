<?php
/**
 * Debug Utility Functions
 * ---------------------
 * This file provides debugging utilities for the application.
 */

/**
 * Debug Function
 * ------------
 * Outputs formatted debug information.
 * 
 * @param mixed $var The variable to debug
 * @param bool $die Whether to terminate execution after debugging
 * @return void
 */
function debug($var, $die = false) {
    // Only show debug output in development environment
    if (true) { // Change this condition for production
        echo '<pre style="background-color: #f5f5f5; color: #333; padding: 10px; margin: 10px; border: 1px solid #ccc; border-radius: 5px; font-family: monospace; font-size: 14px; overflow: auto;">';
        
        if (is_array($var) || is_object($var)) {
            print_r($var);
        } else {
            var_dump($var);
        }
        
        echo '</pre>';
        
        if ($die) {
            die('Debug terminated');
        }
    }
} 