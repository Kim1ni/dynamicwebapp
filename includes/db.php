<?php
require_once 'config.php';
/*You can use this to run it locally
// Create a database connection
function connectDB() {
    $connection = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
    
    // Check connection
    if ($connection->connect_error) {
        die("Connection failed: " . $connection->connect_error);
    }
    
    return $connection;
}
*/
function connectDB() {
    $host = getenv('RENDER_DB_HOST');
    $database = getenv('RENDER_DB_NAME');
    $username = getenv('RENDER_DB_USER');
    $password = getenv('RENDER_DB_PASSWORD');
    $port = getenv('RENDER_DB_PORT') ?: '5432';
    
    $connection_string = "pgsql:host={$host};port={$port};dbname={$database};user={$username};password={$password}";
    
    try {
        $connection = new PDO($connection_string);
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $connection;
    } catch (PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
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