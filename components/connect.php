<?php
// components/connect.php - Updated with better error handling
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = 'localhost';
$username = 'root';
$password = '';
$dbname = 'food_db';

try {
    // Try to connect with database
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    // Test connection
    $conn->query("SELECT 1");
    
} catch(PDOException $e) {
    // If database doesn't exist, create it
    if ($e->getCode() == 1049) { // Unknown database
        try {
            // Connect without database
            $temp_conn = new PDO("mysql:host=$servername", $username, $password);
            $temp_conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Create database
            $temp_conn->exec("CREATE DATABASE IF NOT EXISTS $dbname CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            
            // Connect to new database
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            
            echo "Database '$dbname' was created automatically.<br>";
            echo '<a href="setup_tables.php">Click here to set up tables</a>';
            exit();
            
        } catch(PDOException $e2) {
            die("Failed to create database: " . $e2->getMessage());
        }
    } else {
        die("Connection failed: " . $e->getMessage());
    }
}


// In the setupDatabase function, update the users table creation:
$sql = "CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(100) UNIQUE NOT NULL,
    `number` VARCHAR(20),
    `password` VARCHAR(255) NOT NULL,
    `user_type` ENUM('user', 'admin') DEFAULT 'user',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
?>

