<?php
// setup_tables.php - Create all required tables
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include connect.php to use the same connection
include 'components/connect.php';

echo "<!DOCTYPE html>
<html>
<head>
    <title>Setup Database Tables</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .success { color: #27ae60; }
        .error { color: #e74c3c; }
        .btn { display: inline-block; padding: 10px 20px; background: #3498db; color: white; text-decoration: none; border-radius: 5px; margin-top: 20px; }
        .btn:hover { background: #2980b9; }
        .btn-success { background: #27ae60; }
        .btn-success:hover { background: #219653; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>Setting Up Database Tables</h1>";

try {
    // 1. Create users table
    echo "<h3>Creating Users Table...</h3>";
    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        user_type ENUM('user', 'admin') DEFAULT 'user',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    $conn->exec($sql);
    echo "<p class='success'>✓ Users table created successfully</p>";
    
    // 2. Create products table
    echo "<h3>Creating Products Table...</h3>";
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
    echo "<p class='success'>✓ Products table created successfully</p>";
    
    // 3. Create cart table (this is the missing one!)
    echo "<h3>Creating Cart Table...</h3>";
    $sql = "CREATE TABLE IF NOT EXISTS cart (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        pid INT NOT NULL,
        name VARCHAR(100) NOT NULL,
        price DECIMAL(10,2) NOT NULL,
        quantity INT NOT NULL DEFAULT 1,
        image VARCHAR(100) NOT NULL,
        added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    $conn->exec($sql);
    echo "<p class='success'>✓ Cart table created successfully</p>";
    
    // 4. Create messages table
    echo "<h3>Creating Messages Table...</h3>";
    $sql = "CREATE TABLE IF NOT EXISTS messages (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL,
        message TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    $conn->exec($sql);
    echo "<p class='success'>✓ Messages table created successfully</p>";
    
    // 5. Create orders table
    echo "<h3>Creating Orders Table...</h3>";
    $sql = "CREATE TABLE IF NOT EXISTS orders (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        total_price DECIMAL(10,2) NOT NULL,
        status ENUM('pending', 'processing', 'completed', 'cancelled') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    $conn->exec($sql);
    echo "<p class='success'>✓ Orders table created successfully</p>";
    
    // 6. Create order_items table
    echo "<h3>Creating Order Items Table...</h3>";
    $sql = "CREATE TABLE IF NOT EXISTS order_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        order_id INT NOT NULL,
        product_id INT NOT NULL,
        product_name VARCHAR(255) NOT NULL,
        quantity INT NOT NULL,
        price DECIMAL(10,2) NOT NULL,
        FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    $conn->exec($sql);
    echo "<p class='success'>✓ Order items table created successfully</p>";
    
    // Insert sample products
    echo "<h2>Adding Sample Products</h2>";
    
    $sample_products = [
        ['Turmeric Spiced Coffee', 'Coffee', 4.99, 'home-img-1.1.png'],
        ['Delicious Pizza', 'main dish', 12.99, 'home-img-1.png'],
        ['Cheesy Hamburger', 'main dish', 9.99, 'home-img-2.png'],
        ['Roasted Chicken', 'main dish', 14.99, 'home-img-3.png'],
        ['Espresso', 'Coffee', 3.50, 'cat-1.png'],
        ['Special Pasta', 'main dish', 11.99, 'cat-2.png'],
        ['Fresh Juice', 'drinks', 4.50, 'cat-3.png'],
        ['Chocolate Cake', 'desserts', 6.99, 'cat-4.png'],
        ['Iced Coffee', 'Coffee', 4.25, 'coffee-iced.jpg'],
        ['Caesar Salad', 'main dish', 8.99, 'salad.jpg'],
        ['Lemonade', 'drinks', 3.75, 'lemonade.jpg'],
        ['Cheesecake', 'desserts', 7.50, 'cheesecake.jpg']
    ];
    
    $added_count = 0;
    foreach ($sample_products as $product) {
        // Check if product already exists
        $check = $conn->prepare("SELECT COUNT(*) FROM products WHERE name = ?");
        $check->execute([$product[0]]);
        $exists = $check->fetchColumn();
        
        if ($exists == 0) {
            $stmt = $conn->prepare("INSERT INTO products (name, category, price, image) VALUES (?, ?, ?, ?)");
            $stmt->execute($product);
            $added_count++;
            echo "<p class='success'>✓ Added: " . htmlspecialchars($product[0]) . "</p>";
        } else {
            echo "<p>✓ Already exists: " . htmlspecialchars($product[0]) . "</p>";
        }
    }
    
    echo "<p class='success'>✓ Total products added: $added_count</p>";
    
    // Create admin user
    echo "<h2>Creating Admin User</h2>";
    
    $check_admin = $conn->prepare("SELECT COUNT(*) FROM users WHERE email = 'admin@example.com'");
    $check_admin->execute();
    $admin_exists = $check_admin->fetchColumn();
    
    if ($admin_exists == 0) {
        $hashed_password = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, user_type) VALUES (?, ?, ?, ?)");
        $stmt->execute(['Administrator', 'admin@example.com', $hashed_password, 'admin']);
        echo "<p class='success'>✓ Admin user created successfully</p>";
        echo "<p>Email: admin@example.com</p>";
        echo "<p>Password: admin123</p>";
    } else {
        echo "<p>✓ Admin user already exists</p>";
    }
    
    // Verify tables were created
    echo "<h2>Verification</h2>";
    $tables = ['users', 'products', 'cart', 'messages', 'orders', 'order_items'];
    
    foreach ($tables as $table) {
        try {
            $result = $conn->query("SELECT 1 FROM $table LIMIT 1");
            echo "<p class='success'>✓ Table '$table' exists and is accessible</p>";
        } catch (Exception $e) {
            echo "<p class='error'>✗ Table '$table' has issues: " . $e->getMessage() . "</p>";
        }
    }
    
    echo "<h2 style='color: #27ae60;'>✓ Setup Completed Successfully!</h2>";
    echo '<p>All database tables have been created and sample data has been added.</p>';
    echo '<a href="home.php" class="btn btn-success">Go to Home Page</a>';
    echo '&nbsp;&nbsp;<a href="setup_tables.php" class="btn">Run Setup Again</a>';
    
} catch(PDOException $e) {
    echo "<h2 style='color: #e74c3c;'>Error:</h2>";
    echo "<p class='error'>" . $e->getMessage() . "</p>";
    echo "<p>Try running the setup again or check your database connection.</p>";
    echo '<a href="setup_tables.php" class="btn">Try Again</a>';
}

echo "</div></body></html>";
?>