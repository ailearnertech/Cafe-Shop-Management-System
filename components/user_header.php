<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
include 'components/connect.php';

// Initialize user_id
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : '';

// Display messages if any
if (isset($message)) {
   foreach ($message as $message) {
      echo '
      <div class="message">
         <span>' . $message . '</span>
         <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
      </div>
      ';
   }
}
?>

<header class="header">

   <section class="flex">

      <a href="home.php" class="logo">
         <img src="images/logo.png" alt="Cafe Shop 😋">
      </a>

      <nav class="navbar">
         <a href="home.php">HOME</a>
         <a href="about.php">ABOUT</a>
         <a href="product.php">PRODUCT</a>
         <a href="menu.php">MENU</a>
         <a href="orders.php">ORDER</a>
         <a href="contact.php">CONTACT</a>
      </nav>

      <div class="icons">
         <?php
         $total_cart_items = 0;
         
         // Only query cart if user is logged in
         if (!empty($user_id)) {
            try {
               $count_cart_items = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
               $count_cart_items->execute([$user_id]);
               $total_cart_items = $count_cart_items->rowCount();
            } catch (Exception $e) {
               // If table doesn't exist or error occurs, show 0
               $total_cart_items = 0;
            }
         }
         ?>
         <a href="search.php"><i class="fas fa-search"></i></a>
         <a href="cart.php"><i class="fas fa-shopping-cart"></i><span>(<?= $total_cart_items; ?>)</span></a>
         <div id="user-btn" class="fas fa-user"></div>
         <div id="menu-btn" class="fas fa-bars"></div>
      </div>

      <div class="profile">
         <?php
         if (!empty($user_id)) {
            try {
               $select_profile = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
               $select_profile->execute([$user_id]);
               
               if ($select_profile->rowCount() > 0) {
                  $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
         ?>
            <p class="name"><?= htmlspecialchars($fetch_profile['name']); ?></p>
            <div class="flex">
               <a style="margin-left: 10px;" href="profile.php" class="btn">Profile</a>
               <a href="components/user_logout.php" onclick="return confirm('Logout from this website?');" class="delete-btn">Logout</a>
            </div>
         <?php
               } else {
                  // User ID exists in session but not in database
                  echo '<p class="name">Session error!</p>
                        <a href="components/user_logout.php" class="btn">Logout</a>';
               }
            } catch (Exception $e) {
               echo '<p class="name">Database error!</p>
                     <a href="login.php" class="btn">Login</a>';
            }
         } else {
         ?>
            <p class="name">Please login first!</p>
            <a href="login.php" class="btn">Login</a>
         <?php
         }
         ?>
      </div>

   </section>

</header>

<script>
// Toggle profile dropdown
document.addEventListener('DOMContentLoaded', function() {
    const userBtn = document.getElementById('user-btn');
    const profile = document.querySelector('.profile');
    
    if (userBtn && profile) {
        userBtn.addEventListener('click', function() {
            profile.classList.toggle('active');
        });
        
        // Close profile when clicking outside
        document.addEventListener('click', function(event) {
            if (!profile.contains(event.target) && !userBtn.contains(event.target)) {
                profile.classList.remove('active');
            }
        });
    }
    
    // Toggle mobile menu
    const menuBtn = document.getElementById('menu-btn');
    const navbar = document.querySelector('.navbar');
    
    if (menuBtn && navbar) {
        menuBtn.addEventListener('click', function() {
            navbar.classList.toggle('active');
            menuBtn.classList.toggle('fa-times');
        });
    }
});
</script>

<style>
/* Add some styling for the profile dropdown if needed */
.profile {
    display: none;
    position: absolute;
    top: 100%;
    right: 2rem;
    background: white;
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    padding: 20px;
    min-width: 200px;
    z-index: 1000;
}

.profile.active {
    display: block;
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

.profile .name {
    font-size: 1.2rem;
    font-weight: 600;
    margin-bottom: 15px;
    color: #333;
    text-align: center;
}

.profile .flex {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.profile .btn, .profile .delete-btn {
    display: block;
    text-align: center;
    padding: 10px;
    border-radius: 5px;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s;
}

.profile .btn {
    background: #27ae60;
    color: white;
}

.profile .btn:hover {
    background: #219653;
}

.profile .delete-btn {
    background: #e74c3c;
    color: white;
}

.profile .delete-btn:hover {
    background: #c0392b;
}

/* Mobile menu styles */
@media (max-width: 768px) {
    .navbar {
        position: fixed;
        top: 0;
        right: -100%;
        width: 300px;
        height: 100vh;
        background: white;
        padding: 2rem;
        box-shadow: 0 0 20px rgba(0,0,0,0.1);
        transition: right 0.3s ease;
        z-index: 1001;
        display: flex;
        flex-direction: column;
        gap: 20px;
    }
    
    .navbar.active {
        right: 0;
    }
    
    .navbar a {
        font-size: 1.2rem;
        padding: 15px;
        border-bottom: 1px solid #eee;
    }
}
</style>