<?php
// setup.php - Run this once to create database and tables
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database configuration
$servername = 'localhost';
$username = 'root';
$password = '';

try {
    // Connect to MySQL without selecting a database
    $conn = new PDO("mysql:host=$servername", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Connected to MySQL successfully<br>";
    
    // Create database if it doesn't exist
    $sql = "CREATE DATABASE IF NOT EXISTS food_db 
            CHARACTER SET utf8mb4 
            COLLATE utf8mb4_unicode_ci";
    
    if ($conn->exec($sql)) {
        echo "Database 'food_db' created or already exists<br>";
    }
    
    // Now connect to the specific database
    $conn->exec("USE food_db");
    
    // Create users table
    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        user_type ENUM('user', 'admin') DEFAULT 'user',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $conn->exec($sql);
    echo "Table 'users' created successfully<br>";
    
    // Create products table
    $sql = "CREATE TABLE IF NOT EXISTS products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        category VARCHAR(100) NOT NULL,
        price DECIMAL(10,2) NOT NULL,
        image VARCHAR(255) NOT NULL,
        popularity INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $conn->exec($sql);
    echo "Table 'products' created successfully<br>";
    
    // Create cart table
    $sql = "CREATE TABLE IF NOT EXISTS cart (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        pid INT NOT NULL,
        name VARCHAR(100) NOT NULL,
        price DECIMAL(10,2) NOT NULL,
        quantity INT NOT NULL,
        image VARCHAR(100) NOT NULL,
        added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $conn->exec($sql);
    echo "Table 'cart' created successfully<br>";
    
    // Insert sample products
    $sample_products = [
        ['Turmeric Spiced Coffee', 'Coffee', 4.99, 'home-img-1.1.png'],
        ['Delicious Pizza', 'main dish', 12.99, 'home-img-1.png'],
        ['Cheesy Hamburger', 'main dish', 9.99, 'home-img-2.png'],
        ['Roasted Chicken', 'main dish', 14.99, 'home-img-3.png'],
        ['Espresso', 'Coffee', 3.50, 'cat-1.png'],
        ['Special Pasta', 'main dish', 11.99, 'cat-2.png'],
        ['Fresh Juice', 'drinks', 4.50, 'cat-3.png'],
        ['Chocolate Cake', 'desserts', 6.99, 'cat-4.png'],
        ['Iced Latte', 'Coffee', 4.75, 'coffee1.jpg'],
        ['Grilled Sandwich', 'main dish', 8.99, 'sandwich.jpg'],
        ['Mango Smoothie', 'drinks', 5.25, 'smoothie.jpg'],
        ['Tiramisu', 'desserts', 7.50, 'tiramisu.jpg']
    ];
    
    foreach ($sample_products as $product) {
        $check_sql = "SELECT COUNT(*) FROM products WHERE name = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->execute([$product[0]]);
        $count = $check_stmt->fetchColumn();
        
        if ($count == 0) {
            $insert_sql = "INSERT INTO products (name, category, price, image) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($insert_sql);
            $stmt->execute($product);
        }
    }
    
    echo "Sample products inserted successfully<br>";
    
    // Create admin user (password: admin123)
    $hashed_password = password_hash('admin123', PASSWORD_DEFAULT);
    $check_user = "SELECT COUNT(*) FROM users WHERE email = 'admin@example.com'";
    $check_stmt = $conn->prepare($check_user);
    $check_stmt->execute();
    $user_count = $check_stmt->fetchColumn();
    
    if ($user_count == 0) {
        $insert_user = "INSERT INTO users (name, email, password, user_type) 
                        VALUES ('Administrator', 'admin@example.com', ?, 'admin')";
        $stmt = $conn->prepare($insert_user);
        $stmt->execute([$hashed_password]);
        echo "Admin user created successfully<br>";
        echo "Login: admin@example.com<br>";
        echo "Password: admin123<br>";
    } else {
        echo "Admin user already exists<br>";
    }
    
    echo "<br><strong>Setup completed successfully!</strong><br>";
    echo '<a href="home.php">Go to Home Page</a>';
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>