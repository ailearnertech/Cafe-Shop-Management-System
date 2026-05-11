<?php
// test_database.php - Check database status
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Database Status Test</h1>";
echo "<style>
    body { font-family: Arial; padding: 20px; }
    .success { color: green; font-weight: bold; }
    .error { color: red; font-weight: bold; }
    .info { color: blue; }
    pre { background: #f4f4f4; padding: 10px; border: 1px solid #ddd; }
</style>";

// Test 1: Check PHP MySQL extension
echo "<h2>1. PHP MySQL Extension</h2>";
if (extension_loaded('pdo_mysql')) {
    echo "<p class='success'>✓ PDO MySQL extension is loaded</p>";
} else {
    echo "<p class='error'>✗ PDO MySQL extension is NOT loaded</p>";
}

// Test 2: Try to connect to MySQL
echo "<h2>2. MySQL Connection Test</h2>";
$servername = 'localhost';
$username = 'root';
$password = '';  // XAMPP default is empty

try {
    $conn = new PDO("mysql:host=$servername", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p class='success'>✓ Connected to MySQL server successfully</p>";
    
    // List all databases
    $stmt = $conn->query("SHOW DATABASES");
    $databases = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "<p>Found " . count($databases) . " databases:</p>";
    echo "<ul>";
    foreach ($databases as $db) {
        if ($db == 'food_db') {
            echo "<li class='success'><strong>$db</strong> ✓</li>";
        } else {
            echo "<li>$db</li>";
        }
    }
    echo "</ul>";
    
    // Check if food_db exists
    if (in_array('food_db', $databases)) {
        echo "<p class='success'>✓ Database 'food_db' exists</p>";
        
        // Connect to food_db
        $conn_db = new PDO("mysql:host=$servername;dbname=food_db", $username, $password);
        $conn_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // List tables in food_db
        $tables = $conn_db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
        
        echo "<p>Found " . count($tables) . " tables in food_db:</p>";
        if (count($tables) > 0) {
            echo "<ul>";
            foreach ($tables as $table) {
                echo "<li>$table</li>";
            }
            echo "</ul>";
        } else {
            echo "<p class='error'>✗ No tables found in food_db</p>";
        }
        
    } else {
        echo "<p class='error'>✗ Database 'food_db' does NOT exist</p>";
    }
    
} catch (PDOException $e) {
    echo "<p class='error'>✗ MySQL Connection Failed: " . $e->getMessage() . "</p>";
    echo "<p class='info'>Common solutions:</p>";
    echo "<ul>
            <li>Make sure MySQL is running in XAMPP</li>
            <li>Check if port 3306 is available</li>
            <li>Default XAMPP password is usually empty</li>
            <li>Try username: 'root', password: ''</li>
          </ul>";
}

// Test 3: Check current connect.php file
echo "<h2>3. Current connect.php File</h2>";
$connect_file = 'components/connect.php';
if (file_exists($connect_file)) {
    echo "<p class='success'>✓ connect.php exists</p>";
    echo "<pre>" . htmlspecialchars(file_get_contents($connect_file)) . "</pre>";
} else {
    echo "<p class='error'>✗ connect.php NOT FOUND at: $connect_file</p>";
}

// Test 4: Test creating database and tables
echo "<h2>4. Quick Database Setup Test</h2>";
echo '<form method="post">
        <input type="submit" name="create_db" value="Create Database & Tables" style="padding:10px 20px; background:#27ae60; color:white; border:none; cursor:pointer;">
      </form>';

if (isset($_POST['create_db'])) {
    echo "<h3>Creating Database and Tables...</h3>";
    
    try {
        // Connect to MySQL
        $conn = new PDO("mysql:host=$servername", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Create database
        $conn->exec("CREATE DATABASE IF NOT EXISTS food_db 
                    CHARACTER SET utf8mb4 
                    COLLATE utf8mb4_unicode_ci");
        echo "<p class='success'>✓ Database 'food_db' created</p>";
        
        // Connect to food_db
        $conn->exec("USE food_db");
        
        // Create users table
        $sql = "CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(100) UNIQUE NOT NULL,
            number VARCHAR(20),
            password VARCHAR(255) NOT NULL,
            user_type ENUM('user', 'admin') DEFAULT 'user',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        
        $conn->exec($sql);
        echo "<p class='success'>✓ Table 'users' created</p>";
        
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
        echo "<p class='success'>✓ Table 'products' created</p>";
        
        // Create cart table
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
        echo "<p class='success'>✓ Table 'cart' created</p>";
        
        // Add admin user
        $hashed_password = password_hash('admin123', PASSWORD_DEFAULT);
        $check = $conn->query("SELECT COUNT(*) FROM users WHERE email = 'admin@example.com'")->fetchColumn();
        
        if ($check == 0) {
            $stmt = $conn->prepare("INSERT INTO users (name, email, number, password, user_type) 
                                   VALUES (?, ?, ?, ?, ?)");
            $stmt->execute(['Administrator', 'admin@example.com', '1234567890', $hashed_password, 'admin']);
            echo "<p class='success'>✓ Admin user created</p>";
            echo "<p>Email: admin@example.com</p>";
            echo "<p>Password: admin123</p>";
        }
        
        // Add sample products
        $products = [
            ['Espresso', 'Coffee', 3.50, 'cat-1.png'],
            ['Cappuccino', 'Coffee', 4.25, 'coffee1.jpg'],
            ['Latte', 'Coffee', 4.50, 'coffee2.jpg'],
            ['Margherita Pizza', 'main dish', 12.99, 'home-img-1.png'],
            ['Cheeseburger', 'main dish', 9.99, 'home-img-2.png'],
            ['Orange Juice', 'drinks', 4.50, 'cat-3.png'],
            ['Chocolate Cake', 'desserts', 6.99, 'cat-4.png']
        ];
        
        foreach ($products as $product) {
            $check = $conn->prepare("SELECT COUNT(*) FROM products WHERE name = ?")->execute([$product[0]]);
            $exists = $conn->query("SELECT COUNT(*) FROM products WHERE name = '" . $product[0] . "'")->fetchColumn();
            
            if ($exists == 0) {
                $stmt = $conn->prepare("INSERT INTO products (name, category, price, image) VALUES (?, ?, ?, ?)");
                $stmt->execute($product);
            }
        }
        
        echo "<p class='success'>✓ Sample products added</p>";
        echo "<p class='success'>✓ Database setup complete!</p>";
        
    } catch (PDOException $e) {
        echo "<p class='error'>✗ Setup failed: " . $e->getMessage() . "</p>";
    }
}

echo "<hr>";
echo '<a href="home.php" style="padding:10px 20px; background:#3498db; color:white; text-decoration:none;">Test Home Page</a>';
?>