<?php
session_start();
include 'db.php';
include 'topbar.php';
if (!isset($_SESSION['email'])) { header("Location: login.php"); exit(); }
$email = $_SESSION['email'];

if (!isset($_GET['id'])) { header("Location: index.php"); exit(); }
$pid = intval($_GET['id']);
$pq = $conn->prepare("SELECT * FROM products WHERE id = ?");
$pq->bind_param("i",$pid);
$pq->execute();
$presult = $pq->get_result();
if ($presult->num_rows == 0) { echo "<div class='container'><p class='error'>Product not found.</p></div>"; exit(); }
$product = $presult->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $payment = $_POST['payment'];
  $address = trim($_POST['delivery_address']);
  if ($product['stock'] < 1) { $error = "Out of stock."; }
  else {
    $conn->begin_transaction();
    $ins = $conn->prepare("INSERT INTO orders (user_email, product_id, quantity, payment, delivery_address) VALUES (?, ?, 1, ?, ?)");
    $ins->bind_param("siss", $email, $pid, $payment, $address);
    if ($ins->execute()) {
      $upd = $conn->prepare("UPDATE products SET stock = stock - 1 WHERE id = ?");
      $upd->bind_param("i", $pid);
      $upd->execute();
      $conn->commit();
      $success = "✅ Order placed successfully!";
    } else {
      $conn->rollback();
      $error = "Failed to place order.";
    }
  }
}
?>
<!DOCTYPE html>
<html>
<head><title>Buy Now - Studex</title><link rel="stylesheet" href="style.css"></head>
<body>
<div class="container">
  <h2>Buy: <?= htmlspecialchars($product['name']); ?></h2>
  <p>Price: ₹<?= number_format($product['price'],2); ?></p>
  <?php if (isset($success)) echo "<p class='success'>$success</p>"; ?>
  <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
  <form method="POST">
    <label>Payment Method:</label>
    <select name="payment" required>
      <option value="">-- Select --</option>
      <option value="Cash on Delivery">🪙 Cash on Delivery</option>
      <option value="UPI">💳 UPI</option>
      <option value="Card Payment">📦 Card Payment</option>
    </select>
    <label>Delivery Address:</label>
    <textarea name="delivery_address" rows="3" required></textarea>
    <button type="submit">Confirm Purchase</button>
  </form>
  <a href="index.php" class="back-btn">← Cancel</a>
</div>
</body>
</html>
