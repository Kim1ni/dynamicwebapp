<?php
// Custom error handler
function customErrorHandler($errno, $errstr, $errfile, $errline) {
    $logFile = '../logs/error.log';
    $timestamp = date('Y-m-d H:i:s');
    
    // Format the error message
    $errorMessage = "[{$timestamp}] Error: [{$errno}] {$errstr} in {$errfile} on line {$errline}\n";
    
    // Log the error
    error_log($errorMessage, 3, $logFile);
    
    // Don't show errors to the user in production
    if (defined('ENVIRONMENT') && ENVIRONMENT === 'production') {
        return true;
    } else {
        return false; // Let PHP handle the error in development
    }
}

// Set the custom error handler
set_error_handler('customErrorHandler');

// Set exception handler
function customExceptionHandler($exception) {
    $logFile = '../logs/error.log';
    $timestamp = date('Y-m-d H:i:s');
    
    // Format the exception message
    $errorMessage = "[{$timestamp}] Exception: " . $exception->getMessage() . 
                   " in " . $exception->getFile() . 
                   " on line " . $exception->getLine() . "\n";
    
    // Log the exception
    error_log($errorMessage, 3, $logFile);
    
    // Show a generic error page
    header('HTTP/1.1 500 Internal Server Error');
    include('../error.html');
    exit;
}

// Set the custom exception handler
set_exception_handler('customExceptionHandler');
?>