<?php
require_once 'db.php';

// Function to track login attempts
function trackLoginAttempt($username, $success) {
    $ip = $_SERVER['REMOTE_ADDR'];
    $timestamp = date('Y-m-d H:i:s');
    
    $sql = "INSERT INTO login_attempts (username, ip_address, attempt_time, success) VALUES (?, ?, ?, ?)";
    insertData($sql, "sssi", [$username, $ip, $timestamp, $success ? 1 : 0]);
}

// Function to check if account is locked
function isAccountLocked($username) {
    $ip = $_SERVER['REMOTE_ADDR'];
    $timeframe = date('Y-m-d H:i:s', time() - (15 * 60)); // 15 minutes ago
    
    $sql = "SELECT COUNT(*) as attempts FROM login_attempts 
            WHERE (username = ? OR ip_address = ?) 
            AND attempt_time > ? 
            AND success = 0";
    $result = executeQuery($sql, "sss", [$username, $ip, $timeframe]);
    
    $row = $result->fetch_assoc();
    
    // Lock after 5 failed attempts
    return $row['attempts'] >= 5;
}

// Function to check form submission rate
function checkFormSubmissionRate($userId) {
    $timeframe = date('Y-m-d H:i:s', time() - (60 * 60)); // Past hour
    
    $sql = "SELECT COUNT(*) as submissions FROM form_submissions 
            WHERE user_id = ? AND submitted_at > ?";
    $result = executeQuery($sql, "is", [$userId, $timeframe]);
    
    $row = $result->fetch_assoc();
    
    // Limit to 10 submissions per hour
    return $row['submissions'] < 10;
}
?>