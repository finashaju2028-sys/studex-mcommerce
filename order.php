<?php
session_start();
include 'db.php';
include 'topbar.php';

if (!isset($_SESSION['email'])) {
  header("Location: login.php");
  exit();
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Order Management - Studex</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
  <h2>Order Management</h2>
  <p>This page is reserved for future admin features like managing all orders across the platform.</p>
  <a href="dashboard.php" class="back-btn">← Back to Dashboard</a>
</div>
</body>
</html>
