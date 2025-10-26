<?php
session_start();
include 'db.php';
include 'topbar.php';

if (!isset($_SESSION['email'])) {
  header("Location: login.php");
  exit();
}

$email = $_SESSION['email'];
$orders = $conn->query("
  SELECT o.id, p.name, o.quantity, o.payment, o.delivery_address, o.timestamp
  FROM orders o
  JOIN products p ON o.product_id = p.id
  WHERE o.email = '$email'
  ORDER BY o.timestamp DESC
");
?>
<!DOCTYPE html>
<html>
<head>
  <title>My Orders - Studex</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
  <h2>My Orders</h2>
  <?php if ($orders->num_rows > 0): ?>
    <table>
      <tr>
        <th>Product</th>
        <th>Quantity</th>
        <th>Payment</th>
        <th>Delivery Address</th>
        <th>Date</th>
      </tr>
      <?php while ($row = $orders->fetch_assoc()): ?>
      <tr>
        <td><?php echo $row['name']; ?></td>
        <td><?php echo $row['quantity']; ?></td>
        <td><?php echo $row['payment']; ?></td>
        <td><?php echo $row['delivery_address']; ?></td>
        <td><?php echo date("d M Y, h:i A", strtotime($row['timestamp'])); ?></td>
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
