<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'components/connect.php';

session_start();

if (isset($_SESSION['user_id'])) {
   $user_id = $_SESSION['user_id'];
} else {
   $user_id = '';
}

include 'components/add_cart.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Menu - Cafe Shop</title>

   <link rel="stylesheet" href="https://unpkg.com/swiper@8/swiper-bundle.min.css" />
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">

   <style>
      /* Reset and base styles */
      * {
         margin: 0;
         padding: 0;
         box-sizing: border-box;
      }
      
      body {
         font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
         line-height: 1.6;
         color: #333;
         background: #f9f5f0;
      }
      
      /* Heading styles */
      .heading {
         background: linear-gradient(135deg, #6d4c41 0%, #8d6e63 100%);
         padding: 4rem 2rem;
         text-align: center;
         color: white;
         margin-bottom: 3rem;
         position: relative;
         overflow: hidden;
      }
      
      .heading::before {
         content: '';
         position: absolute;
         top: 0;
         left: 0;
         right: 0;
         bottom: 0;
         background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" preserveAspectRatio="none"><path d="M0,0 L100,0 L100,100 Z" fill="rgba(255,255,255,0.1)"/></svg>');
         background-size: cover;
      }
      
      .heading h3 {
         font-size: 3.5rem;
         margin-bottom: 1rem;
         text-transform: uppercase;
         letter-spacing: 3px;
         position: relative;
         z-index: 1;
         text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
      }
      
      .heading p {
         font-size: 1.3rem;
         position: relative;
         z-index: 1;
      }
      
      .heading p a {
         color: #ffd54f;
         text-decoration: none;
         font-weight: 600;
         transition: color 0.3s;
      }
      
      .heading p a:hover {
         color: #ffca28;
         text-decoration: underline;
      }
      
      .heading p span {
         color: #e0e0e0;
      }
      
      /* Reviews/Sections */
      .reviews {
         padding: 4rem 0;
         position: relative;
      }
      
      .reviews:nth-child(odd) {
         background: #fff;
      }
      
      .reviews:nth-child(even) {
         background: #f5f0eb;
      }
      
      .reviews .title {
         text-align: center;
         font-size: 2.8rem;
         color: #5d4037;
         margin-bottom: 3rem;
         position: relative;
         padding-bottom: 15px;
      }
      
      .reviews .title::after {
         content: '';
         position: absolute;
         bottom: 0;
         left: 50%;
         transform: translateX(-50%);
         width: 150px;
         height: 4px;
         background: linear-gradient(to right, #6d4c41, #d7ccc8);
         border-radius: 2px;
      }
      
      /* Swiper Container Fix */
      .reviews-slider {
         width: 100%;
         height: 420px; /* Fixed height */
         padding: 20px 0 60px !important; /* Space for pagination */
         position: relative;
         overflow: hidden;
      }
      
      .swiper-wrapper {
         height: 100% !important;
      }
      
      .swiper-slide {
         height: calc(100% - 40px) !important;
         background: white;
         border-radius: 15px;
         box-shadow: 0 8px 25px rgba(109, 76, 65, 0.1);
         overflow: hidden;
         transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
         padding: 25px;
         display: flex;
         flex-direction: column;
         position: relative;
         border: 1px solid #e0e0e0;
      }
      
      .swiper-slide:hover {
         transform: translateY(-15px) scale(1.02);
         box-shadow: 0 15px 35px rgba(109, 76, 65, 0.2);
         border-color: #d7ccc8;
      }
      
      .swiper-slide img {
         width: 100%;
         height: 180px;
         object-fit: cover;
         border-radius: 10px;
         margin-bottom: 20px;
         transition: transform 0.3s;
      }
      
      .swiper-slide:hover img {
         transform: scale(1.05);
      }
      
      .swiper-slide h3 {
         font-size: 1.4rem;
         color: #333;
         margin-bottom: 15px;
         font-weight: 600;
         line-height: 1.4;
         flex-grow: 1;
         text-align: center;
      }
      
      .swiper-slide .flex {
         display: flex;
         justify-content: space-between;
         align-items: center;
         margin-top: auto;
         padding-top: 20px;
         border-top: 2px solid #f5f0eb;
      }
      
      .swiper-slide .price {
         color: #2e7d32;
         font-size: 1.8rem;
         font-weight: 700;
         display: flex;
         align-items: baseline;
      }
      
      .swiper-slide .price span {
         font-size: 1.2rem;
         margin-right: 3px;
         color: #388e3c;
      }
      
      .swiper-slide .qty {
         width: 70px;
         padding: 8px 12px;
         text-align: center;
         border: 2px solid #6d4c41;
         border-radius: 8px;
         font-size: 1.1rem;
         font-weight: 600;
         color: #5d4037;
         background: #fff;
         transition: all 0.3s;
      }
      
      .swiper-slide .qty:focus {
         outline: none;
         border-color: #d7ccc8;
         box-shadow: 0 0 0 3px rgba(109, 76, 65, 0.1);
      }
      
      .swiper-slide button {
         background: linear-gradient(135deg, #6d4c41 0%, #8d6e63 100%);
         color: white;
         border: none;
         width: 45px;
         height: 45px;
         border-radius: 50%;
         cursor: pointer;
         transition: all 0.3s;
         display: flex;
         align-items: center;
         justify-content: center;
         font-size: 1.3rem;
         box-shadow: 0 4px 15px rgba(109, 76, 65, 0.3);
      }
      
      .swiper-slide button:hover {
         background: linear-gradient(135deg, #5d4037 0%, #795548 100%);
         transform: scale(1.1);
         box-shadow: 0 6px 20px rgba(109, 76, 65, 0.4);
      }
      
      .swiper-slide button:active {
         transform: scale(0.95);
      }
      
      /* Empty state */
      .empty {
         text-align: center;
         padding: 4rem 2rem;
         background: linear-gradient(135deg, #f5f0eb 0%, #efebe9 100%);
         border-radius: 15px;
         margin: 2rem auto;
         max-width: 600px;
         border: 2px dashed #d7ccc8;
      }
      
      .empty p {
         font-size: 1.5rem;
         color: #795548;
         margin-bottom: 1rem;
         font-weight: 500;
      }
      
      .empty a {
         display: inline-block;
         padding: 12px 30px;
         background: linear-gradient(135deg, #6d4c41 0%, #8d6e63 100%);
         color: white;
         text-decoration: none;
         border-radius: 25px;
         font-weight: 600;
         transition: all 0.3s;
         margin-top: 1rem;
      }
      
      .empty a:hover {
         transform: translateY(-3px);
         box-shadow: 0 8px 20px rgba(109, 76, 65, 0.3);
      }
      
      /* Pagination */
      .swiper-pagination {
         bottom: 20px !important;
      }
      
      .swiper-pagination-bullet {
         width: 12px !important;
         height: 12px !important;
         background: #d7ccc8 !important;
         opacity: 1 !important;
         margin: 0 8px !important;
         transition: all 0.3s !important;
      }
      
      .swiper-pagination-bullet-active {
         background: #6d4c41 !important;
         transform: scale(1.3);
      }
      
      /* Category navigation */
      .category-nav {
         display: flex;
         justify-content: center;
         gap: 10px;
         margin: 30px 0;
         flex-wrap: wrap;
         padding: 0 20px;
      }
      
      .category-nav a {
         padding: 12px 25px;
         background: white;
         border-radius: 25px;
         text-decoration: none;
         color: #5d4037;
         font-weight: 600;
         border: 2px solid #d7ccc8;
         transition: all 0.3s;
      }
      
      .category-nav a:hover {
         background: #6d4c41;
         color: white;
         border-color: #6d4c41;
         transform: translateY(-3px);
      }
      
      /* Container for content */
      .container {
         max-width: 1200px;
         margin: 0 auto;
         padding: 0 20px;
      }
      
      /* Responsive */
      @media (max-width: 768px) {
         .heading h3 {
            font-size: 2.5rem;
         }
         
         .reviews .title {
            font-size: 2.2rem;
         }
         
         .reviews-slider {
            height: 380px;
         }
         
         .swiper-slide {
            padding: 20px;
         }
         
         .swiper-slide h3 {
            font-size: 1.2rem;
         }
         
         .category-nav {
            flex-direction: column;
            align-items: center;
         }
         
         .category-nav a {
            width: 100%;
            max-width: 300px;
            text-align: center;
         }
      }
      
      @media (max-width: 480px) {
         .heading {
            padding: 3rem 1rem;
         }
         
         .heading h3 {
            font-size: 2rem;
         }
         
         .reviews {
            padding: 2rem 0;
         }
         
         .reviews-slider {
            height: 350px;
         }
      }
   </style>
</head>
<body>

   <!-- header section starts  -->
   <?php include 'components/user_header.php'; ?>
   <!-- header section ends -->

   <div class="heading">
      <h3>Our Menu</h3>
      <p><a href="home.php">Home</a> <span> / Menu</span></p>
   </div>

   <!-- Category Navigation -->
   <div class="category-nav">
      <a href="#coffee">☕ Coffee</a>
      <a href="#main-dishes">🍽️ Main Dishes</a>
      <a href="#drinks">🥤 Drinks</a>
      <a href="#desserts">🍰 Desserts</a>
   </div>

   <!-- Coffee Section -->
   <section class="reviews" id="coffee">
      <div class="container">
         <h1 class="title">☕ Coffee</h1>
         <div class="swiper reviews-slider">
            <div class="swiper-wrapper">
               <?php
               try {
                  $select_products = $conn->prepare("SELECT * FROM `products` WHERE LOWER(category) = 'coffee'");
                  $select_products->execute();
                  
                  if ($select_products->rowCount() > 0) {
                     while ($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)) {
               ?>
                        <div class="swiper-slide slide">
                           <form action="" method="post" class="box">
                              <input type="hidden" name="pid" value="<?= htmlspecialchars($fetch_products['id']); ?>">
                              <input type="hidden" name="name" value="<?= htmlspecialchars($fetch_products['name']); ?>">
                              <input type="hidden" name="price" value="<?= htmlspecialchars($fetch_products['price']); ?>">
                              <input type="hidden" name="image" value="<?= htmlspecialchars($fetch_products['image']); ?>">
                              <img src="uploaded_img/<?= htmlspecialchars($fetch_products['image']); ?>" 
                                   alt="<?= htmlspecialchars($fetch_products['name']); ?>" 
                                   onerror="this.src='images/default-food.jpg'">
                              <h3><?= htmlspecialchars($fetch_products['name']); ?></h3>
                              <div class="flex">
                                 <div class="price"><span>$</span><?= htmlspecialchars($fetch_products['price']); ?></div>
                                 <input type="number" name="qty" class="qty" min="1" max="99" value="1" maxlength="2">
                                 <button type="submit" class="fas fa-shopping-cart" name="add_to_cart" title="Add to Cart"></button>
                              </div>
                           </form>
                        </div>
               <?php
                     }
                  } else {
                     echo '<div class="swiper-slide slide">
                           <div class="empty">
                              <p>No coffee items available yet.</p>
                              <p>Check back soon!</p>
                           </div>
                        </div>';
                  }
               } catch (Exception $e) {
                  echo '<div class="swiper-slide slide">
                        <div class="empty">
                           <p>Error loading coffee items.</p>
                           <p><a href="setup_menu.php">Run setup</a></p>
                        </div>
                     </div>';
               }
               ?>
            </div>
            <div class="swiper-pagination"></div>
         </div>
      </div>
   </section>

   <!-- Main Dishes Section -->
   <section class="reviews" id="main-dishes">
      <div class="container">
         <h1 class="title">🍽️ Main Dishes</h1>
         <div class="swiper reviews-slider">
            <div class="swiper-wrapper">
               <?php
               try {
                  $select_products = $conn->prepare("SELECT * FROM `products` WHERE LOWER(category) = 'main dish'");
                  $select_products->execute();
                  
                  if ($select_products->rowCount() > 0) {
                     while ($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)) {
               ?>
                        <div class="swiper-slide slide">
                           <form action="" method="post" class="box">
                              <input type="hidden" name="pid" value="<?= htmlspecialchars($fetch_products['id']); ?>">
                              <input type="hidden" name="name" value="<?= htmlspecialchars($fetch_products['name']); ?>">
                              <input type="hidden" name="price" value="<?= htmlspecialchars($fetch_products['price']); ?>">
                              <input type="hidden" name="image" value="<?= htmlspecialchars($fetch_products['image']); ?>">
                              <img src="uploaded_img/<?= htmlspecialchars($fetch_products['image']); ?>" 
                                   alt="<?= htmlspecialchars($fetch_products['name']); ?>"
                                   onerror="this.src='images/default-food.jpg'">
                              <h3><?= htmlspecialchars($fetch_products['name']); ?></h3>
                              <div class="flex">
                                 <div class="price"><span>$</span><?= htmlspecialchars($fetch_products['price']); ?></div>
                                 <input type="number" name="qty" class="qty" min="1" max="99" value="1" maxlength="2">
                                 <button type="submit" class="fas fa-shopping-cart" name="add_to_cart" title="Add to Cart"></button>
                              </div>
                           </form>
                        </div>
               <?php
                     }
                  } else {
                     echo '<div class="swiper-slide slide">
                           <div class="empty">
                              <p>No main dishes available yet.</p>
                              <p>Check back soon!</p>
                           </div>
                        </div>';
                  }
               } catch (Exception $e) {
                  echo '<div class="swiper-slide slide">
                        <div class="empty">
                           <p>Error loading main dishes.</p>
                        </div>
                     </div>';
               }
               ?>
            </div>
            <div class="swiper-pagination"></div>
         </div>
      </div>
   </section>

   <!-- Drinks Section -->
   <section class="reviews" id="drinks">
      <div class="container">
         <h1 class="title">🥤 Drinks</h1>
         <div class="swiper reviews-slider">
            <div class="swiper-wrapper">
               <?php
               try {
                  $select_products = $conn->prepare("SELECT * FROM `products` WHERE LOWER(category) = 'drinks'");
                  $select_products->execute();
                  
                  if ($select_products->rowCount() > 0) {
                     while ($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)) {
               ?>
                        <div class="swiper-slide slide">
                           <form action="" method="post" class="box">
                              <input type="hidden" name="pid" value="<?= htmlspecialchars($fetch_products['id']); ?>">
                              <input type="hidden" name="name" value="<?= htmlspecialchars($fetch_products['name']); ?>">
                              <input type="hidden" name="price" value="<?= htmlspecialchars($fetch_products['price']); ?>">
                              <input type="hidden" name="image" value="<?= htmlspecialchars($fetch_products['image']); ?>">
                              <img src="uploaded_img/<?= htmlspecialchars($fetch_products['image']); ?>" 
                                   alt="<?= htmlspecialchars($fetch_products['name']); ?>"
                                   onerror="this.src='images/default-food.jpg'">
                              <h3><?= htmlspecialchars($fetch_products['name']); ?></h3>
                              <div class="flex">
                                 <div class="price"><span>$</span><?= htmlspecialchars($fetch_products['price']); ?></div>
                                 <input type="number" name="qty" class="qty" min="1" max="99" value="1" maxlength="2">
                                 <button type="submit" class="fas fa-shopping-cart" name="add_to_cart" title="Add to Cart"></button>
                              </div>
                           </form>
                        </div>
               <?php
                     }
                  } else {
                     echo '<div class="swiper-slide slide">
                           <div class="empty">
                              <p>No drinks available yet.</p>
                              <p>Check back soon!</p>
                           </div>
                        </div>';
                  }
               } catch (Exception $e) {
                  echo '<div class="swiper-slide slide">
                        <div class="empty">
                           <p>Error loading drinks.</p>
                        </div>
                     </div>';
               }
               ?>
            </div>
            <div class="swiper-pagination"></div>
         </div>
      </div>
   </section>

   <!-- Desserts Section -->
   <section class="reviews" id="desserts">
      <div class="container">
         <h1 class="title">🍰 Desserts</h1>
         <div class="swiper reviews-slider">
            <div class="swiper-wrapper">
               <?php
               try {
                  $select_products = $conn->prepare("SELECT * FROM `products` WHERE LOWER(category) = 'desserts'");
                  $select_products->execute();
                  
                  if ($select_products->rowCount() > 0) {
                     while ($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)) {
               ?>
                        <div class="swiper-slide slide">
                           <form action="" method="post" class="box">
                              <input type="hidden" name="pid" value="<?= htmlspecialchars($fetch_products['id']); ?>">
                              <input type="hidden" name="name" value="<?= htmlspecialchars($fetch_products['name']); ?>">
                              <input type="hidden" name="price" value="<?= htmlspecialchars($fetch_products['price']); ?>">
                              <input type="hidden" name="image" value="<?= htmlspecialchars($fetch_products['image']); ?>">
                              <img src="uploaded_img/<?= htmlspecialchars($fetch_products['image']); ?>" 
                                   alt="<?= htmlspecialchars($fetch_products['name']); ?>"
                                   onerror="this.src='images/default-food.jpg'">
                              <h3><?= htmlspecialchars($fetch_products['name']); ?></h3>
                              <div class="flex">
                                 <div class="price"><span>$</span><?= htmlspecialchars($fetch_products['price']); ?></div>
                                 <input type="number" name="qty" class="qty" min="1" max="99" value="1" maxlength="2">
                                 <button type="submit" class="fas fa-shopping-cart" name="add_to_cart" title="Add to Cart"></button>
                              </div>
                           </form>
                        </div>
               <?php
                     }
                  } else {
                     echo '<div class="swiper-slide slide">
                           <div class="empty">
                              <p>No desserts available yet.</p>
                              <p>Check back soon!</p>
                           </div>
                        </div>';
                  }
               } catch (Exception $e) {
                  echo '<div class="swiper-slide slide">
                        <div class="empty">
                           <p>Error loading desserts.</p>
                        </div>
                     </div>';
               }
               ?>
            </div>
            <div class="swiper-pagination"></div>
         </div>
      </div>
   </section>

   <!-- footer section starts  -->
   <?php include 'components/footer.php'; ?>
   <!-- footer section ends -->

   <!-- custom js file link  -->
   <script src="js/script.js"></script>
   <script src="https://unpkg.com/swiper@8/swiper-bundle.min.js"></script>

   <script>
      // Initialize all sliders with proper configuration
      document.addEventListener('DOMContentLoaded', function() {
         const sliders = document.querySelectorAll('.reviews-slider');
         
         sliders.forEach((slider, index) => {
            new Swiper(slider, {
               loop: true,
               grabCursor: true,
               spaceBetween: 30,
               slidesPerView: 1,
               centeredSlides: false,
               pagination: {
                  el: slider.querySelector('.swiper-pagination'),
                  clickable: true,
                  dynamicBullets: true,
               },
               breakpoints: {
                  320: {
                     slidesPerView: 1,
                     spaceBetween: 20
                  },
                  640: {
                     slidesPerView: 2,
                     spaceBetween: 25
                  },
                  1024: {
                     slidesPerView: 3,
                     spaceBetween: 30
                  },
                  1200: {
                     slidesPerView: 4,
                     spaceBetween: 30
                  }
               },
               autoplay: {
                  delay: 3000,
                  disableOnInteraction: false,
               },
               speed: 800,
               effect: 'slide',
               preventClicks: false,
               preventClicksPropagation: false,
            });
         });
         
         // Add smooth scrolling for category navigation
         document.querySelectorAll('.category-nav a').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
               e.preventDefault();
               const targetId = this.getAttribute('href');
               const targetElement = document.querySelector(targetId);
               
               if (targetElement) {
                  window.scrollTo({
                     top: targetElement.offsetTop - 80,
                     behavior: 'smooth'
                  });
               }
            });
         });
         
         // Add to cart functionality with better feedback
         const cartButtons = document.querySelectorAll('button[name="add_to_cart"]');
         cartButtons.forEach(button => {
            button.addEventListener('click', function(e) {
               e.preventDefault();
               const form = this.closest('form');
               const productName = form.querySelector('input[name="name"]').value;
               
               // Create custom alert
               const alertBox = document.createElement('div');
               alertBox.style.cssText = `
                  position: fixed;
                  top: 20px;
                  right: 20px;
                  background: linear-gradient(135deg, #4CAF50, #2E7D32);
                  color: white;
                  padding: 15px 25px;
                  border-radius: 10px;
                  box-shadow: 0 5px 15px rgba(0,0,0,0.3);
                  z-index: 10000;
                  animation: slideIn 0.3s ease-out;
                  display: flex;
                  align-items: center;
                  gap: 10px;
                  font-weight: 600;
               `;
               alertBox.innerHTML = `
                  <i class="fas fa-check-circle" style="font-size: 1.2em;"></i>
                  <span>Added "${productName}" to cart!</span>
               `;
               
               document.body.appendChild(alertBox);
               
               // Remove alert after 3 seconds
               setTimeout(() => {
                  alertBox.style.animation = 'slideOut 0.3s ease-out';
                  setTimeout(() => {
                     document.body.removeChild(alertBox);
                  }, 300);
               }, 3000);
               
               // Submit the form
               setTimeout(() => {
                  form.submit();
               }, 100);
            });
         });
         
         // Add CSS animations
         const style = document.createElement('style');
         style.textContent = `
            @keyframes slideIn {
               from {
                  transform: translateX(100%);
                  opacity: 0;
               }
               to {
                  transform: translateX(0);
                  opacity: 1;
               }
            }
            
            @keyframes slideOut {
               from {
                  transform: translateX(0);
                  opacity: 1;
               }
               to {
                  transform: translateX(100%);
                  opacity: 0;
               }
            }
            
            /* Fix for Swiper slides */
            .swiper-slide {
               height: auto !important;
               display: flex !important;
            }
            
            .swiper-slide .box {
               flex: 1;
               display: flex;
               flex-direction: column;
            }
         `;
         document.head.appendChild(style);
      });
   </script>

</body>
</html>