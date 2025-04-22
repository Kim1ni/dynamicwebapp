<?php
require_once 'db.php';

// Function to sanitize input data more thoroughly
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

// Function to check if a username already exists
function usernameExists($username) {
    $sql = "SELECT id FROM users WHERE username = ?";
    $result = executeQuery($sql, "s", [$username]);
    
    return $result->num_rows > 0;
}

// Function to check if an email already exists
function emailExists($email) {
    $sql = "SELECT id FROM users WHERE email = ?";
    $result = executeQuery($sql, "s", [$email]);
    
    return $result->num_rows > 0;
}

// Function to register a new user
function registerUser($username, $email, $password) {
    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
    $userId = insertData($sql, "sss", [$username, $email, $hashedPassword]);
    
    return $userId;
}

// Function to validate user login
function validateLogin($username, $password) {
    $sql = "SELECT id, username, password FROM users WHERE username = ?";
    $result = executeQuery($sql, "s", [$username]);
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        if (password_verify($password, $user['password'])) {
            return $user;
        }
    }
    
    return false;
}

// Function to add a new form submission
function addFormSubmission($userId, $title, $category, $content) {
    $sql = "INSERT INTO form_submissions (user_id, title, category, content) VALUES (?, ?, ?, ?)";
    $submissionId = insertData($sql, "isss", [$userId, $title, $category, $content]);
    
    return $submissionId;
}

// Function to get all form submissions for a user
function getUserFormSubmissions($userId) {
    $sql = "SELECT id, title, category, content, submitted_at FROM form_submissions WHERE user_id = ? ORDER BY submitted_at DESC";
    $result = executeQuery($sql, "i", [$userId]);
    
    $submissions = [];
    while ($row = $result->fetch_assoc()) {
        $submissions[] = $row;
    }
    
    return $submissions;
}

// Generate a secure token for "Remember Me" functionality
function generateRememberMeToken() {
    return bin2hex(random_bytes(32));
}

// Store remember me token in database
function storeRememberMeToken($userId, $token, $expiry) {
    // First, delete any existing tokens for this user
    $sql = "DELETE FROM remember_tokens WHERE user_id = ?";
    executeQuery($sql, "i", [$userId]);
    
    // Insert new token
    $sql = "INSERT INTO remember_tokens (user_id, token, expiry) VALUES (?, ?, ?)";
    insertData($sql, "iss", [$userId, $token, $expiry]);
}

// Validate remember me token
function validateRememberMeToken($userId, $token) {
    $sql = "SELECT * FROM remember_tokens WHERE user_id = ? AND token = ? AND expiry > NOW()";
    $result = executeQuery($sql, "is", [$userId, $token]);
    
    return $result->num_rows === 1;
}

// Delete remember me token
function deleteRememberMeToken($userId) {
    $sql = "DELETE FROM remember_tokens WHERE user_id = ?";
    executeQuery($sql, "i", [$userId]);
}

// Function to validate password strength
function validatePasswordStrength($password) {
    // At least 8 characters
    if (strlen($password) < 8) {
        return "Password must be at least 8 characters long.";
    }
    
    // At least one uppercase letter
    if (!preg_match('/[A-Z]/', $password)) {
        return "Password must contain at least one uppercase letter.";
    }
    
    // At least one lowercase letter
    if (!preg_match('/[a-z]/', $password)) {
        return "Password must contain at least one lowercase letter.";
    }
    
    // At least one number
    if (!preg_match('/\d/', $password)) {
        return "Password must contain at least one number.";
    }
    
    // At least one special character
    if (!preg_match('/[^A-Za-z0-9]/', $password)) {
        return "Password must contain at least one special character.";
    }
    
    return true;
}
?>