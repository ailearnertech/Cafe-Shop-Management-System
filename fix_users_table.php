<?php
// fix_users_table.php - Add user_type column to users table
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'components/connect.php';

echo "<h1>Fix Users Table</h1>";
echo "<style>
    body { font-family: Arial; padding: 20px; }
    .success { color: green; }
    .error { color: red; }
</style>";

try {
    // Check if user_type column exists
    $columns = $conn->query("SHOW COLUMNS FROM users LIKE 'user_type'")->fetch();
    
    if ($columns) {
        echo "<p class='success'>✓ user_type column already exists</p>";
    } else {
        // Add user_type column
        $sql = "ALTER TABLE users 
                ADD COLUMN user_type ENUM('user', 'admin') DEFAULT 'user' 
                AFTER password";
        
        $conn->exec($sql);
        echo "<p class='success'>✓ Added user_type column to users table</p>";
    }
    
    // Update existing users to have 'user' type
    $conn->exec("UPDATE users SET user_type = 'user' WHERE user_type IS NULL OR user_type = ''");
    echo "<p class='success'>✓ Updated existing users</p>";
    
    // Show table structure
    echo "<h2>Current Users Table Structure:</h2>";
    $structure = $conn->query("DESCRIBE users");
    echo "<table border='1' cellpadding='5'>
            <tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    while($row = $structure->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>
                <td>{$row['Field']}</td>
                <td>{$row['Type']}</td>
                <td>{$row['Null']}</td>
                <td>{$row['Key']}</td>
                <td>{$row['Default']}</td>
              </tr>";
    }
    echo "</table>";
    
    echo "<p class='success'>✓ Table fixed successfully!</p>";
    echo '<a href="register.php">Go to Registration Page</a>';
    
} catch (PDOException $e) {
    echo "<p class='error'>Error: " . $e->getMessage() . "</p>";
}
?>