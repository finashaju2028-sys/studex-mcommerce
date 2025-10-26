<?php
session_start();
include 'db.php';
include 'topbar.php';

if (!isset($_SESSION['email'])) {
  header("Location: login.php");
  exit();
}

$email = $_SESSION['email'];

// Handle Buy Now
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['product_id'])) {
  $product_id = $_POST['product_id'];
  $quantity = $_POST['quantity'];
  $payment = $_POST['payment'];
  $address = $_POST['delivery_address'];

  $stock_result = $conn->query("SELECT stock FROM products WHERE id=$product_id");
  if ($stock_result && $stock_result->num_rows > 0) {
    $stock = $stock_result->fetch_assoc()['stock'];
    if ($stock >= $quantity) {
      $stmt = $conn->prepare("INSERT INTO orders (email, product_id, quantity, payment, delivery_address) VALUES (?, ?, ?, ?, ?)");
      $stmt->bind_param("siiss", $email, $product_id, $quantity, $payment, $address);
      $stmt->execute();
      $conn->query("UPDATE products SET stock = stock - $quantity WHERE id = $product_id");
      $success = "✅ Order placed!";
    } else {
      $error = "❌ Not enough stock!";
    }
  } else {
    $error = "❌ Product not found!";
  }
}

// Handle Add to Cart
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_to_cart'])) {
  $product_id = $_POST['add_to_cart'];
  $exists = $conn->query("SELECT * FROM cart WHERE email='$email' AND product_id=$product_id");
  if ($exists->num_rows == 0) {
    $conn->query("INSERT INTO cart (email, product_id) VALUES ('$email', $product_id)");
    $success = "✅ Item added to cart!";
  } else {
    $error = "⚠️ Item already in cart!";
  }
}

$products = $conn->query("SELECT * FROM products");
?>
<!DOCTYPE html>
<html>
<head><title>Dashboard - Studex</title><link rel="stylesheet" href="style.css"></head>
<body>
<div class="container">
  <h2>Welcome, <?php echo $email; ?>!</h2>
  <h3>Available Products</h3>
  <?php if (isset($success)) echo "<p class='success'>$success</p>"; ?>
  <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
  <div class="product-grid">
    <?php while ($row = $products->fetch_assoc()): ?>
      <div class="product-card">
        <h4><?php echo $row['name']; ?></h4>
        <p>₹<?php echo $row['price']; ?></p>
        <p>Stock: <?php echo $row['stock']; ?></p>
        <p>Brand: <?php echo $row['brand']; ?></p>

        <!-- Buy Now Form -->
        <form method="POST">
          <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
          <label>Quantity:</label>
          <select name="quantity">
            <?php for ($i = 1; $i <= min(5, $row['stock']); $i++): ?>
              <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
            <?php endfor; ?>
          </select>
          <label>Payment Method:</label>
          <select name="payment">
            <option value="Cash on Delivery">Cash on Delivery</option>
            <option value="UPI">UPI</option>
            <option value="Debit Card">Debit Card</option>
            <option value="Credit Card">Credit Card</option>
            <option value="Net Banking">Net Banking</option>
          </select>
          <label>Delivery Address:</label>
          <textarea name="delivery_address" required></textarea>
          <button type="submit">Buy Now</button>
        </form>

        <!-- Add to Cart Form -->
        <form method="POST">
          <input type="hidden" name="add_to_cart" value="<?php echo $row['id']; ?>">
          <button type="submit">Add to Cart</button>
        </form>
      </div>
    <?php endwhile; ?>
  </div>
</div>
</body>
</html>
