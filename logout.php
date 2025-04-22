<?php
// Add at the top of each main PHP file
require_once 'includes/error_handler.php';
require_once 'includes/session.php';
require_once 'includes/functions.php';
secureSessionStart();

// If user is logged in, delete remember me token
if (isset($_SESSION['user_id'])) {
    // Delete the remember me token from database
    deleteRememberMeToken($_SESSION['user_id']);
    
    // Clear the cookie
    setcookie('remember_me', '', time() - 3600, '/', '', true, true);
}

// Unset all session variables
$_SESSION = [];

// If it's desired to kill the session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy the session
session_destroy();

// Redirect to home page
header("Location: index.php");
exit;
?>