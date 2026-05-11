<?php
// setup_users.php - Create users table and sample data
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'components/connect.php';

echo "<!DOCTYPE html>
<html>
<head>
    <title>Setup Users Database</title>
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
        <h1>Setting Up Users Database</h1>";

try {
    // Check if users table exists, create if not
    $table_check = $conn->query("SHOW TABLES LIKE 'users'")->fetch();
    
    if (!$table_check) {
        // Create users table
        $sql = "CREATE TABLE users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(100) UNIQUE NOT NULL,
            number VARCHAR(20),
            password VARCHAR(255) NOT NULL,
            user_type ENUM('user', 'admin') DEFAULT 'user',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        
        $conn->exec($sql);
        echo "<p class='success'>✓ Users table created successfully</p>";
    } else {
        echo "<p>✓ Users table already exists</p>";
    }
    
    // Check if admin user exists
    $check_admin = $conn->prepare("SELECT COUNT(*) FROM users WHERE email = 'admin@example.com'");
    $check_admin->execute();
    $admin_exists = $check_admin->fetchColumn();
    
    if ($admin_exists == 0) {
        // Create admin user (password: admin123)
        $hashed_password = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (name, email, number, password, user_type) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute(['Administrator', 'admin@example.com', '1234567890', $hashed_password, 'admin']);
        echo "<p class='success'>✓ Admin user created</p>";
        echo "<p>Email: admin@example.com</p>";
        echo "<p>Password: admin123</p>";
    } else {
        echo "<p>✓ Admin user already exists</p>";
    }
    
    // Show all users
    echo "<h2>Current Users in Database</h2>";
    $users = $conn->query("SELECT id, name, email, user_type FROM users");
    
    if ($users->rowCount() > 0) {
        echo "<table border='1' style='width:100%; border-collapse:collapse;'>
                <tr style='background:#f2f2f2;'>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>User Type</th>
                </tr>";
        while($user = $users->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>
                    <td>{$user['id']}</td>
                    <td>{$user['name']}</td>
                    <td>{$user['email']}</td>
                    <td>{$user['user_type']}</td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No users found in database</p>";
    }
    
    echo "<h2 class='success'>✓ Setup Complete!</h2>";
    echo '<a href="register.php" class="btn">Go to Registration Page</a>';
    
} catch(PDOException $e) {
    echo "<p class='error'>Error: " . $e->getMessage() . "</p>";
}

echo "</div></body></html>";
?>