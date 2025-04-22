<?php
// Session configuration and security
function secureSessionStart() {
    // Set secure cookie parameters
    $cookieParams = session_get_cookie_params();
    session_set_cookie_params(
        $cookieParams["lifetime"], 
        $cookieParams["path"], 
        $cookieParams["domain"], 
        true,     // Secure flag - cookies only sent over HTTPS
        true      // HttpOnly flag - prevents JavaScript access to cookies
    );
    
    // Use a secure session name
    session_name('secure_session');
    
    // Start the session
    session_start();
    
    // Regenerate session ID to prevent session fixation
    if (!isset($_SESSION['created'])) {
        session_regenerate_id(true);
        $_SESSION['created'] = time();
    } else if (time() - $_SESSION['created'] > 1800) {
        // Regenerate session ID every 30 minutes
        session_regenerate_id(true);
        $_SESSION['created'] = time();
    }
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Force HTTPS (for production)
function forceHTTPS() {
    if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] !== 'on') {
        // Redirect to HTTPS if not already using it
        // Note: This won't work in local development unless HTTPS is set up
        // Uncomment this in production
        /*
        $redirect = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        header("Location: $redirect");
        exit;
        */
    }
}

// Set security headers
function setSecurityHeaders() {
    // Prevent clickjacking
    header("X-Frame-Options: DENY");
    // Prevent MIME type sniffing
    header("X-Content-Type-Options: nosniff");
    // Enable XSS protection in browsers
    header("X-XSS-Protection: 1; mode=block");
    // Content Security Policy
    header("Content-Security-Policy: default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self'; connect-src 'self';");
}
?>