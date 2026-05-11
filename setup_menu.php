<?php
// setup_menu.php - Setup database for menu
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include connect.php
include 'components/connect.php';

echo "<!DOCTYPE html>
<html>
<head>
    <title>Setup Menu Database</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f4f4f4; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; }
        .success { color: green; }
        .error { color: red; }
        .btn { display: inline-block; padding: 10px 20px; background: #27ae60; color: white; text-decoration: none; border-radius: 5px; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>Setting Up Menu Database</h1>";

try {
    // Create products table if it doesn't exist
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
    echo "<p class='success'>✓ Products table created</p>";
    
    // Insert sample menu items
    echo "<h2>Adding Menu Items</h2>";
    
    $menu_items = [
        // Coffee items
        ['Espresso', 'Coffee', 3.50, 'cat-1.png'],
        ['Cappuccino', 'Coffee', 4.25, 'coffee1.jpg'],
        ['Latte', 'Coffee', 4.50, 'coffee2.jpg'],
        ['Americano', 'Coffee', 3.75, 'coffee3.jpg'],
        ['Mocha', 'Coffee', 4.75, 'coffee4.jpg'],
        ['Macchiato', 'Coffee', 4.00, 'coffee5.jpg'],
        ['Flat White', 'Coffee', 4.25, 'coffee6.jpg'],
        ['Turkish Coffee', 'Coffee', 4.50, 'coffee7.jpg'],
        
        // Main Dishes
        ['Margherita Pizza', 'main dish', 12.99, 'home-img-1.png'],
        ['Pepperoni Pizza', 'main dish', 14.99, 'pizza1.jpg'],
        ['BBQ Chicken Pizza', 'main dish', 15.99, 'pizza2.jpg'],
        ['Cheeseburger', 'main dish', 9.99, 'home-img-2.png'],
        ['Chicken Burger', 'main dish', 10.99, 'burger2.jpg'],
        ['Grilled Chicken', 'main dish', 14.99, 'home-img-3.png'],
        ['Spaghetti Carbonara', 'main dish', 11.99, 'pasta1.jpg'],
        ['Caesar Salad', 'main dish', 8.99, 'salad1.jpg'],
        ['Club Sandwich', 'main dish', 9.50, 'sandwich1.jpg'],
        ['Fish & Chips', 'main dish', 13.99, 'fish1.jpg'],
        
        // Drinks
        ['Orange Juice', 'drinks', 4.50, 'cat-3.png'],
        ['Apple Juice', 'drinks', 4.25, 'juice1.jpg'],
        ['Mango Smoothie', 'drinks', 5.50, 'smoothie1.jpg'],
        ['Iced Tea', 'drinks', 3.75, 'tea1.jpg'],
        ['Lemonade', 'drinks', 3.50, 'lemonade1.jpg'],
        ['Milkshake', 'drinks', 5.25, 'milkshake1.jpg'],
        ['Soft Drinks', 'drinks', 2.50, 'soda1.jpg'],
        ['Mineral Water', 'drinks', 1.50, 'water1.jpg'],
        
        // Desserts
        ['Chocolate Cake', 'desserts', 6.99, 'cat-4.png'],
        ['Cheesecake', 'desserts', 7.50, 'cake1.jpg'],
        ['Tiramisu', 'desserts', 7.25, 'cake2.jpg'],
        ['Apple Pie', 'desserts', 5.75, 'pie1.jpg'],
        ['Ice Cream Sundae', 'desserts', 6.25, 'icecream1.jpg'],
        ['Brownie', 'desserts', 4.99, 'brownie1.jpg'],
        ['Fruit Tart', 'desserts', 6.50, 'tart1.jpg'],
        ['Panna Cotta', 'desserts', 6.75, 'panna1.jpg']
    ];
    
    $added = 0;
    foreach ($menu_items as $item) {
        // Check if item exists
        $check = $conn->prepare("SELECT COUNT(*) FROM products WHERE name = ?");
        $check->execute([$item[0]]);
        $exists = $check->fetchColumn();
        
        if ($exists == 0) {
            $stmt = $conn->prepare("INSERT INTO products (name, category, price, image) VALUES (?, ?, ?, ?)");
            $stmt->execute($item);
            $added++;
            echo "<p class='success'>✓ Added: {$item[0]} ({$item[1]})</p>";
        } else {
            echo "<p>✓ Already exists: {$item[0]}</p>";
        }
    }
    
    echo "<h3 class='success'>✓ Total items added: $added</h3>";
    
    // Verify categories
    echo "<h2>Category Counts</h2>";
    $categories = ['Coffee', 'main dish', 'drinks', 'desserts'];
    
    foreach ($categories as $category) {
        $count = $conn->prepare("SELECT COUNT(*) FROM products WHERE category = ?");
        $count->execute([$category]);
        $total = $count->fetchColumn();
        echo "<p>{$category}: {$total} items</p>";
    }
    
    echo "<h2 class='success'>✓ Menu Setup Complete!</h2>";
    echo '<a href="menu.php" class="btn">Go to Menu Page</a>';
    
} catch(PDOException $e) {
    echo "<p class='error'>Error: " . $e->getMessage() . "</p>";
}

echo "</div></body></html>";
?>