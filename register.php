<?php
// register.php - Simplified and working version
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection
require_once 'components/connect.php';

session_start();

// If user is already logged in, redirect to home
if (isset($_SESSION['user_id'])) {
    header('Location: home.php');
    exit();
}

// Initialize variables
$name = $email = $number = '';
$errors = [];
$success = false;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    
    // Get form data
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $number = trim($_POST['number'] ?? '');
    $pass = $_POST['pass'] ?? '';
    $cpass = $_POST['cpass'] ?? '';
    
    // Validation
    if (empty($name)) {
        $errors[] = 'Name is required';
    } elseif (strlen($name) < 2) {
        $errors[] = 'Name must be at least 2 characters';
    }
    
    if (empty($email)) {
        $errors[] = 'Email is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email format';
    }
    
    if (empty($number)) {
        $errors[] = 'Phone number is required';
    } elseif (!preg_match('/^[0-9]{10,15}$/', $number)) {
        $errors[] = 'Phone number must be 10-15 digits';
    }
    
    if (empty($pass)) {
        $errors[] = 'Password is required';
    } elseif (strlen($pass) < 6) {
        $errors[] = 'Password must be at least 6 characters';
    }
    
    if ($pass !== $cpass) {
        $errors[] = 'Passwords do not match';
    }
    
    // If no errors, proceed with registration
    if (empty($errors)) {
        try {
            // Check if user already exists
            $check_sql = "SELECT id FROM users WHERE email = ? OR number = ?";
            $check_stmt = $conn->prepare($check_sql);
            $check_stmt->execute([$email, $number]);
            
            if ($check_stmt->rowCount() > 0) {
                $errors[] = 'Email or phone number already registered';
            } else {
                // Hash the password
                $hashed_password = password_hash($pass, PASSWORD_DEFAULT);
                
                // Insert new user
                $insert_sql = "INSERT INTO users (name, email, number, password, user_type) 
                               VALUES (?, ?, ?, ?, 'user')";
                $insert_stmt = $conn->prepare($insert_sql);
                
                if ($insert_stmt->execute([$name, $email, $number, $hashed_password])) {
                    // Get the new user ID
                    $user_id = $conn->lastInsertId();
                    
                    // Set session variables
                    $_SESSION['user_id'] = $user_id;
                    $_SESSION['user_name'] = $name;
                    $_SESSION['user_email'] = $email;
                    
                    // Set success flag
                    $success = true;
                    
                    // Redirect after 2 seconds
                    header('Refresh: 2; URL=home.php');
                } else {
                    $errors[] = 'Registration failed. Please try again.';
                }
            }
        } catch (PDOException $e) {
            $errors[] = 'Database error: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Cafe Shop</title>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/style.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .register-box {
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 500px;
            padding: 40px;
            animation: fadeIn 0.5s ease-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .register-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .register-header h1 {
            color: #333;
            font-size: 2.5rem;
            margin-bottom: 10px;
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .register-header p {
            color: #666;
            font-size: 1rem;
        }
        
        .form-group {
            margin-bottom: 20px;
            position: relative;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: 600;
            font-size: 0.95rem;
        }
        
        .form-control {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid #e1e5ee;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s;
            background: #f8f9fa;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #6a11cb;
            background: white;
            box-shadow: 0 0 0 3px rgba(106, 17, 203, 0.1);
        }
        
        .input-with-icon {
            position: relative;
        }
        
        .input-with-icon i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #888;
        }
        
        .input-with-icon input {
            padding-left: 45px;
        }
        
        .btn-register {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 10px;
        }
        
        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 7px 14px rgba(106, 17, 203, 0.3);
        }
        
        .btn-register:active {
            transform: translateY(0);
        }
        
        .login-link {
            text-align: center;
            margin-top: 25px;
            color: #666;
            font-size: 0.95rem;
        }
        
        .login-link a {
            color: #6a11cb;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s;
        }
        
        .login-link a:hover {
            color: #2575fc;
            text-decoration: underline;
        }
        
        .alert {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 25px;
            font-size: 0.95rem;
            animation: slideIn 0.3s ease-out;
        }
        
        @keyframes slideIn {
            from { opacity: 0; transform: translateX(-20px); }
            to { opacity: 1; transform: translateX(0); }
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .alert ul {
            margin: 0;
            padding-left: 20px;
        }
        
        .password-strength {
            margin-top: 5px;
            font-size: 0.85rem;
        }
        
        .strength-weak { color: #e74c3c; }
        .strength-medium { color: #f39c12; }
        .strength-strong { color: #27ae60; }
        
        .password-match {
            margin-top: 5px;
            font-size: 0.85rem;
        }
        
        .match-yes { color: #27ae60; }
        .match-no { color: #e74c3c; }
        
        .toggle-password {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #888;
            cursor: pointer;
            font-size: 1.1rem;
        }
        
        @media (max-width: 576px) {
            .register-box {
                padding: 30px 20px;
            }
            
            .register-header h1 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    
    <!-- Include Header -->
    <?php include 'components/user_header.php'; ?>
    
    <div class="container">
        <div class="register-box">
            
            <!-- Success Message -->
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> Registration successful! Redirecting to home page...
                </div>
            <?php endif; ?>
            
            <!-- Error Messages -->
            <?php if (!empty($errors)): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-triangle"></i>
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <!-- Registration Form -->
            <?php if (!$success): ?>
                <div class="register-header">
                    <h1><i class="fas fa-user-plus"></i> Create Account</h1>
                    <p>Join our cafe community today!</p>
                </div>
                
                <form method="POST" action="" onsubmit="return validateForm()">
                    <div class="form-group">
                        <label for="name"><i class="fas fa-user"></i> Full Name</label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               class="form-control" 
                               placeholder="Enter your full name"
                               value="<?php echo htmlspecialchars($name); ?>"
                               required
                               minlength="2"
                               maxlength="50">
                    </div>
                    
                    <div class="form-group">
                        <label for="email"><i class="fas fa-envelope"></i> Email Address</label>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               class="form-control" 
                               placeholder="Enter your email"
                               value="<?php echo htmlspecialchars($email); ?>"
                               required>
                    </div>
                    
                    <div class="form-group">
                        <label for="number"><i class="fas fa-phone"></i> Phone Number</label>
                        <input type="tel" 
                               id="number" 
                               name="number" 
                               class="form-control" 
                               placeholder="Enter your phone number"
                               value="<?php echo htmlspecialchars($number); ?>"
                               required
                               pattern="[0-9]{10,15}"
                               title="10-15 digits only">
                    </div>
                    
                    <div class="form-group">
                        <label for="password"><i class="fas fa-lock"></i> Password</label>
                        <div class="input-with-icon">
                            <input type="password" 
                                   id="password" 
                                   name="pass" 
                                   class="form-control" 
                                   placeholder="Enter password (min. 6 characters)"
                                   required
                                   minlength="6"
                                   maxlength="50"
                                   onkeyup="checkPasswordStrength()">
                            <button type="button" class="toggle-password" onclick="togglePassword('password')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div id="password-strength" class="password-strength"></div>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm-password"><i class="fas fa-lock"></i> Confirm Password</label>
                        <div class="input-with-icon">
                            <input type="password" 
                                   id="confirm-password" 
                                   name="cpass" 
                                   class="form-control" 
                                   placeholder="Confirm your password"
                                   required
                                   minlength="6"
                                   maxlength="50"
                                   onkeyup="checkPasswordMatch()">
                            <button type="button" class="toggle-password" onclick="togglePassword('confirm-password')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div id="password-match" class="password-match"></div>
                    </div>
                    
                    <button type="submit" name="submit" class="btn-register">
                        <i class="fas fa-user-plus"></i> Register Now
                    </button>
                    
                    <div class="login-link">
                        Already have an account? <a href="login.php">Login here</a>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Include Footer -->
    <?php include 'components/footer.php'; ?>
    
    <script>
        // Password strength checker
        function checkPasswordStrength() {
            const password = document.getElementById('password').value;
            const strengthText = document.getElementById('password-strength');
            
            if (password.length === 0) {
                strengthText.textContent = '';
                strengthText.className = 'password-strength';
                return;
            }
            
            let strength = 0;
            
            // Length check
            if (password.length >= 6) strength++;
            if (password.length >= 8) strength++;
            
            // Character diversity
            if (/[a-z]/.test(password)) strength++;
            if (/[A-Z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^A-Za-z0-9]/.test(password)) strength++;
            
            // Update display
            if (strength <= 2) {
                strengthText.textContent = 'Weak password';
                strengthText.className = 'password-strength strength-weak';
            } else if (strength <= 4) {
                strengthText.textContent = 'Medium password';
                strengthText.className = 'password-strength strength-medium';
            } else {
                strengthText.textContent = 'Strong password';
                strengthText.className = 'password-strength strength-strong';
            }
        }
        
        // Password match checker
        function checkPasswordMatch() {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm-password').value;
            const matchText = document.getElementById('password-match');
            
            if (confirmPassword.length === 0) {
                matchText.textContent = '';
                matchText.className = 'password-match';
                return;
            }
            
            if (password === confirmPassword) {
                matchText.textContent = '✓ Passwords match';
                matchText.className = 'password-match match-yes';
            } else {
                matchText.textContent = '✗ Passwords do not match';
                matchText.className = 'password-match match-no';
            }
        }
        
        // Toggle password visibility
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const icon = field.nextElementSibling.querySelector('i');
            
            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                field.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
        
        // Form validation
        function validateForm() {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm-password').value;
            const phone = document.getElementById('number').value;
            
            // Check passwords match
            if (password !== confirmPassword) {
                alert('Error: Passwords do not match!');
                return false;
            }
            
            // Check password length
            if (password.length < 6) {
                alert('Error: Password must be at least 6 characters long!');
                return false;
            }
            
            // Check phone number format
            if (!/^[0-9]{10,15}$/.test(phone)) {
                alert('Error: Phone number must be 10-15 digits!');
                return false;
            }
            
            return true;
        }
        
        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            checkPasswordStrength();
            checkPasswordMatch();
        });

        // After successful registration
$_SESSION['user_id'] = $user_id;  // Make sure this is set
$_SESSION['user_name'] = $name;
header('Location: home.php');      // Use header() for immediate redirect
exit();
    </script>


</body>
</html>