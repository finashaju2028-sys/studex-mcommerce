<?php
session_start();
include 'db.php';
include 'topbar.php';

if (!isset($_SESSION['email'])) { header("Location: login.php"); exit(); }
$email = $_SESSION['email'];

$q = $conn->prepare("
  SELECT o.order_id, o.quantity, o.total_price, o.payment_option, o.order_date, o.status, p.name, p.brand, p.seller_email
  FROM orders o
  JOIN products p ON o.product_id = p.id
  WHERE o.buyer_email = ?
  ORDER BY o.order_date DESC
");
$q->bind_param("s",$email);
$q->execute();
$res = $q->get_result();
?>
<!DOCTYPE html>
<html>
<head><title>My Orders - Studex</title><link rel="stylesheet" href="style.css"></head>
<body>
<div class="container">
  <h2>My Orders</h2>
  <?php if ($res->num_rows > 0): ?>
    <table>
      <tr><th>Order ID</th><th>Product</th><th>Brand</th><th>Qty</th><th>Price</th><th>Total</th><th>Payment</th><th>Date</th><th>Seller</th></tr>
      <?php while ($r = $res->fetch_assoc()): ?>
        <tr>
          <td>#<?= $r['order_id']; ?></td>
          <td><?= htmlspecialchars($r['name']); ?></td>
          <td><?= htmlspecialchars($r['brand']); ?></td>
          <td><?= intval($r['quantity']); ?></td>
          <td>₹<?= number_format($r['total_price'] / max(1,$r['quantity']),2); ?></td>
          <td>₹<?= number_format($r['total_price'],2); ?></td>
          <td><?= htmlspecialchars($r['payment_option']); ?></td>
          <td><?= $r['order_date']; ?></td>
          <td><?= htmlspecialchars($r['seller_email']); ?></td>
        </tr>
      <?php endwhile; ?>
    </table>
  <?php else: ?>
    <p class="error">You haven't placed any orders yet.</p>
  <?php endif; ?>
  <a href="dashboard.php" class="back-btn">← Back to Dashboard</a>
</div>
</body>
</html>
