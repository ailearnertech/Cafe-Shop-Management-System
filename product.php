<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'components/connect.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
};

include 'components/add_cart.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Products - Cafe Shop</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">
</head>
<body>
   
<?php include 'components/user_header.php'; ?>

<div class="heading">
   <h3>Our Products</h3>
   <p><a href="home.php">home</a> <span> / Products</span></p>
</div>

<section class="products">
   <h1 class="title">All Products</h1>

   <div class="box-container">
      <?php
      try {
         $select_products = $conn->prepare("SELECT * FROM `products` ORDER BY created_at DESC");
         $select_products->execute();
         
         if($select_products->rowCount() > 0){
            while($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)){
      ?>
      <form action="" method="post" class="box">
         <input type="hidden" name="pid" value="<?= htmlspecialchars($fetch_products['id']); ?>">
         <input type="hidden" name="name" value="<?= htmlspecialchars($fetch_products['name']); ?>">
         <input type="hidden" name="price" value="<?= htmlspecialchars($fetch_products['price']); ?>">
         <input type="hidden" name="image" value="<?= htmlspecialchars($fetch_products['image']); ?>">
         <a href="quick_view.php?pid=<?= $fetch_products['id']; ?>" class="fas fa-eye"></a>
         <button type="submit" class="fas fa-shopping-cart" name="add_to_cart"></button>
         <img src="uploaded_img/<?= htmlspecialchars($fetch_products['image']); ?>" 
              alt="<?= htmlspecialchars($fetch_products['name']); ?>"
              onerror="this.src='images/default-food.jpg'; this.onerror=null;">
         <a href="category.php?category=<?= urlencode($fetch_products['category']); ?>" class="cat">
            <?= htmlspecialchars($fetch_products['category']); ?>
         </a>
         <div class="name"><?= htmlspecialchars($fetch_products['name']); ?></div>
         <div class="flex">
            <div class="price"><span>$</span><?= htmlspecialchars($fetch_products['price']); ?></div>
            <input type="number" name="qty" class="qty" min="1" max="99" value="1" maxlength="2">
         </div>
      </form>
      <?php
            }
         } else {
            echo '<div class="empty">
                     <p>No products found!</p>
                     <p>The product database is empty.</p>
                     <p><a href="setup_products.php" style="color: #27ae60;">Click here to setup sample products</a></p>
                  </div>';
         }
      } catch (Exception $e) {
         echo '<div class="empty">
                  <p>Error: Database connection failed.</p>
                  <p>Make sure the database is set up correctly.</p>
                  <p><a href="setup_products.php" style="color: #27ae60;">Setup Database</a></p>
               </div>';
      }
      ?>
   </div>
</section>

<?php include 'components/footer.php'; ?>

<script src="js/script.js"></script>

<script>
// Simple add to cart notification
document.addEventListener('DOMContentLoaded', function() {
    const cartButtons = document.querySelectorAll('button[name="add_to_cart"]');
    
    cartButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            const form = this.closest('form');
            const productName = form.querySelector('input[name="name"]').value;
            
            // Show success message
            alert('Added ' + productName + ' to cart!');
        });
    });
});
</script>

</body>
</html>