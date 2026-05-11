<?php
// setup_products.php - Create database and sample products
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database configuration
$servername = 'localhost';
$username = 'root';
$password = '';
$dbname = 'food_db';

try {
    // Connect to MySQL
    $conn = new PDO("mysql:host=$servername", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h1>Setting Up Products Database</h1>";
    
    // Create database if not exists
    $conn->exec("CREATE DATABASE IF NOT EXISTS $dbname");
    $conn->exec("USE $dbname");
    
    // Create products table
    $sql = "CREATE TABLE IF NOT EXISTS products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        category VARCHAR(100) NOT NULL,
        price DECIMAL(10,2) NOT NULL,
        image VARCHAR(255) NOT NULL,
        popularity INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    $conn->exec($sql);
    echo "<p style='color:green'>✓ Products table created</p>";
    
    // Insert sample products
    $products = [
        ['Espresso', 'Coffee', 3.50, 'cat-1.png'],
        ['Cappuccino', 'Coffee', 4.25, 'coffee1.jpg'],
        ['Latte', 'Coffee', 4.50, 'coffee2.jpg'],
        ['Mocha', 'Coffee', 4.75, 'coffee3.jpg'],
        ['Americano', 'Coffee', 3.75, 'coffee4.jpg'],
        ['Macchiato', 'Coffee', 4.00, 'coffee5.jpg'],
        ['Margherita Pizza', 'main dish', 12.99, 'home-img-1.png'],
        ['Pepperoni Pizza', 'main dish', 14.99, 'pizza1.jpg'],
        ['Cheeseburger', 'main dish', 9.99, 'home-img-2.png'],
        ['Chicken Burger', 'main dish', 10.99, 'burger2.jpg'],
        ['Grilled Chicken', 'main dish', 14.99, 'home-img-3.png'],
        ['Spaghetti', 'main dish', 11.99, 'pasta1.jpg'],
        ['Orange Juice', 'drinks', 4.50, 'cat-3.png'],
        ['Apple Juice', 'drinks', 4.25, 'juice1.jpg'],
        ['Lemonade', 'drinks', 3.50, 'lemonade1.jpg'],
        ['Iced Tea', 'drinks', 3.75, 'tea1.jpg'],
        ['Chocolate Cake', 'desserts', 6.99, 'cat-4.png'],
        ['Cheesecake', 'desserts', 7.50, 'cake1.jpg'],
        ['Tiramisu', 'desserts', 7.25, 'cake2.jpg'],
        ['Apple Pie', 'desserts', 5.75, 'pie1.jpg']
    ];
    
    $added = 0;
    foreach ($products as $product) {
        $check = $conn->prepare("SELECT COUNT(*) FROM products WHERE name = ?");
        $check->execute([$product[0]]);
        if ($check->fetchColumn() == 0) {
            $stmt = $conn->prepare("INSERT INTO products (name, category, price, image) VALUES (?, ?, ?, ?)");
            $stmt->execute($product);
            $added++;
        }
    }
    
    echo "<p style='color:green'>✓ Added $added sample products</p>";
    echo "<p><a href='product.php'>Go to Products Page</a></p>";
    
} catch(PDOException $e) {
    echo "<p style='color:red'>Error: " . $e->getMessage() . "</p>";
}
?>