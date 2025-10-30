<?php
session_start();
include 'db.php';
include 'topbar.php';

if (!isset($_SESSION['email'])) { header("Location: login.php"); exit(); }
$email = $_SESSION['email'];

// remove item if requested
if (isset($_GET['remove'])) {
  $rid = intval($_GET['remove']);
  $d = $conn->prepare("DELETE FROM cart WHERE id=? AND user_email=?");
  $d->bind_param("is",$rid,$email);
  $d->execute();
  header("Location: cart.php");
  exit();
}

// place order from cart
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['place_order'])) {
  $payment = $_POST['payment'];
  $address = trim($_POST['delivery_address']);

  // fetch cart items
  $ci = $conn->prepare("SELECT c.id, c.product_id, c.quantity, p.price, p.stock FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_email = ?");
  $ci->bind_param("s",$email);
  $ci->execute();
  $cres = $ci->get_result();

  if ($cres->num_rows == 0) {
    $error = "Your cart is empty.";
  } else {
    $conn->begin_transaction();
    $ok = true;
    while ($item = $cres->fetch_assoc()) {
      if ($item['stock'] < $item['quantity']) { $ok = false; $error = "Not enough stock for product id " . $item['product_id']; break; }
      $total_price = $item['price'] * $item['quantity'];
      $ins = $conn->prepare("INSERT INTO orders (buyer_email, product_id, quantity, total_price, payment_option, order_date, status) VALUES (?, ?, ?, ?, ?, NOW(), 'Pending')");
      $ins->bind_param("siids",$email, $item['product_id'], $item['quantity'], $total_price, $payment);
      if (!$ins->execute()) { $ok = false; break; }
      $upd = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
      $upd->bind_param("ii",$item['quantity'],$item['product_id']);
      if (!$upd->execute()) { $ok = false; break; }
    }
    if ($ok) {
      $del = $conn->prepare("DELETE FROM cart WHERE user_email = ?");
      $del->bind_param("s",$email);
      $del->execute();
      $conn->commit();
      $success = "✅ Order placed successfully!";
    } else {
      $conn->rollback();
      if (!isset($error)) $error = "Failed to place order.";
    }
  }
}

// fetch cart items
$cart = $conn->prepare("SELECT c.id AS cid, p.name, p.brand, p.price, c.quantity FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_email = ?");
$cart->bind_param("s",$email);
$cart->execute();
$cartRes = $cart->get_result();
$total = 0;
?>
<!DOCTYPE html>
<html>
<head><title>My Cart - Studex</title><link rel="stylesheet" href="style.css"></head>
<body>
<div class="container">
  <h2>My Cart</h2>
  <?php if (isset($success)) echo "<p class='success'>$success</p>"; ?>
  <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>

  <?php if ($cartRes->num_rows > 0): ?>
    <table>
      <tr><th>Product</th><th>Brand</th><th>Price</th><th>Qty</th><th>Subtotal</th><th>Action</th></tr>
      <?php while ($r = $cartRes->fetch_assoc()): $subtotal = $r['price'] * $r['quantity']; $total += $subtotal; ?>
      <tr>
        <td><?= htmlspecialchars($r['name']); ?></td>
        <td><?= htmlspecialchars($r['brand']); ?></td>
        <td>₹<?= number_format($r['price'],2); ?></td>
        <td><?= intval($r['quantity']); ?></td>
        <td>₹<?= number_format($subtotal,2); ?></td>
        <td><a href="cart.php?remove=<?= $r['cid']; ?>" onclick="return confirm('Remove this item?')" class="back-btn" style="color:#ff4136;">🗑️ Remove</a></td>
      </tr>
      <?php endwhile; ?>
    </table>
    <h3>Total: ₹<?= number_format($total,2); ?></h3>

    <form method="POST">
      <label>Payment Method:</label>
      <select name="payment" required>
        <option value="">-- Select Payment --</option>
        <option value="Cash on Delivery">🪙 Cash on Delivery</option>
        <option value="UPI">💳 UPI</option>
        <option value="Card Payment">📦 Card Payment</option>
      </select>
      <label>Delivery Address:</label>
      <textarea name="delivery_address" rows="3" required placeholder="Enter full delivery address..."></textarea>
      <button type="submit" name="place_order">Confirm Order</button>
    </form>
  <?php else: ?>
    <p class="error">Your cart is empty.</p>
  <?php endif; ?>

  <a href="dashboard.php" class="back-btn">← Back to Dashboard</a>
</div>
</body>
</html>
