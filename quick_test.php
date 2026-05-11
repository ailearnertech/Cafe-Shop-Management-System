<?php
// quick_test.php - Simple database test
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Quick Database Test</h1>";

// Test 1: Try to include connect.php
echo "<h2>1. Testing connect.php</h2>";
try {
    include 'components/connect.php';
    echo "<p style='color:green'>✓ connect.php included successfully</p>";
    
    // Test query
    $result = $conn->query("SELECT 1");
    echo "<p style='color:green'>✓ Database query successful</p>";
    
    // Check tables
    $tables = $conn->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo "<p>Found " . count($tables) . " tables:</p>";
    echo "<ul>";
    foreach ($tables as $table) {
        echo "<li>$table</li>";
    }
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<p style='color:red'>✗ Error: " . $e->getMessage() . "</p>";
}

// Test 2: Check if we can create a user
echo "<h2>2. Test User Creation</h2>";
echo '<form method="post">
        <input type="text" name="test_name" placeholder="Test Name" value="Test User">
        <input type="email" name="test_email" placeholder="Email" value="test@example.com">
        <input type="text" name="test_phone" placeholder="Phone" value="1234567890">
        <input type="password" name="test_pass" placeholder="Password" value="test123">
        <input type="submit" name="test_create" value="Test Create User">
      </form>';

if (isset($_POST['test_create'])) {
    try {
        $name = $_POST['test_name'];
        $email = $_POST['test_email'];
        $number = $_POST['test_phone'];
        $pass = password_hash($_POST['test_pass'], PASSWORD_DEFAULT);
        
        $stmt = $conn->prepare("INSERT INTO users (name, email, number, password) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $email, $number, $pass]);
        
        echo "<p style='color:green'>✓ Test user created successfully!</p>";
        echo "<p>User ID: " . $conn->lastInsertId() . "</p>";
        
    } catch (Exception $e) {
        echo "<p style='color:red'>✗ Error creating user: " . $e->getMessage() . "</p>";
    }
}
?>