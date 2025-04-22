<?php
require_once 'config.php';

// Create a database connection
function connectDB() {
    $connection = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
    
    // Check connection
    if ($connection->connect_error) {
        die("Connection failed: " . $connection->connect_error);
    }
    
    return $connection;
}

// Execute a prepared statement with parameters
function executeQuery($sql, $types = null, $params = []) {
    $connection = connectDB();
    
    $stmt = $connection->prepare($sql);
    
    if ($stmt === false) {
        die("Error preparing statement: " . $connection->error);
    }
    
    if ($types !== null && $params !== []) {
        $stmt->bind_param($types, ...$params);
    }
    
    $stmt->execute();
    
    $result = $stmt->get_result();
    
    $stmt->close();
    $connection->close();
    
    return $result;
}

// Insert data and return the inserted ID
function insertData($sql, $types, $params) {
    $connection = connectDB();
    
    $stmt = $connection->prepare($sql);
    
    if ($stmt === false) {
        die("Error preparing statement: " . $connection->error);
    }
    
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    
    $insertId = $stmt->insert_id;
    
    $stmt->close();
    $connection->close();
    
    return $insertId;
}
?>