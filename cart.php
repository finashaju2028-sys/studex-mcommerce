<?php
session_start();
include 'db.php';
include 'topbar.php';

if (!isset($_SESSION['email'])) {
  header("Location: login.php");
  exit();
}

$email = $_SESSION['email'];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['confirm_cart'])) {
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
      $conn->query("DELETE FROM cart WHERE email='$email' AND product_id=$product_id");
      $success = "✅ Order placed with $payment!";
    } else {
      $error = "❌ Not enough stock available!";
    }
  } else {
    $error = "❌ Product not found!";
  }
}

$cart = $conn->query("
  SELECT c.product_id, p.name, p.price, p.stock, p.brand
  FROM cart c
  JOIN products p ON c.product_id = p.id
  WHERE c.email = '$email'
");
?>
<!DOCTYPE html>
<html>
<head>
  <title>My Cart - Studex</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
  <h2>My Cart</h2>
  <?php if (isset($success)) echo "<p class='success'>$success</p>"; ?>
  <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
  <?php if ($cart->num_rows > 0): ?>
    <div class="product-grid">
      <?php while ($row = $cart->fetch_assoc()): ?>
        <div class="product-card">
          <h4><?php echo $row['name']; ?></h4>
          <p>₹<?php echo $row['price']; ?></p>
          <p>Stock: <?php echo $row['stock']; ?></p>
          <p>Brand: <?php echo $row['brand']; ?></p>
          <form method="POST">
            <input type="hidden" name="product_id" value="<?php echo $row['product_id']; ?>">
            <label>Quantity:</label>
            <select name="quantity">
              <?php for ($i = 1; $i <= min(5, $row['stock']); $i++): ?>
                <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
              <?php endfor; ?>
            </select>
            <label>Payment Method:</label>
            <select name="payment" required>
              <option value="Cash on Delivery">Cash on Delivery</option>
              <option value="UPI">UPI</option>
              <option value="Debit Card">Debit Card</option>
              <option value="Credit Card">Credit Card</option>
              <option value="Net Banking">Net Banking</option>
            </select>
            <label>Delivery Address:</label>
            <textarea name="delivery_address" placeholder="Enter full delivery address..." rows="3" required></textarea>
            <button type="submit" name="confirm_cart">Confirm Order</button>
          </form>
        </div>
      <?php endwhile; ?>
    </div>
  <?php else: ?>
    <p class="error">Your cart is empty.</p>
  <?php endif; ?>
  <a href="dashboard.php" class="back-btn">← Back to Dashboard</a>
</div>
</body>
</html>
