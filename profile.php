<?php
session_start();
include 'db.php';
include 'topbar.php';

if (!isset($_SESSION['email'])) {
  header("Location: login.php");
  exit();
}

$email = $_SESSION['email'];
?>
<!DOCTYPE html>
<html>
<head>
  <title>My Profile - Studex</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
  <h2>My Profile</h2>
  <p><strong>Email:</strong> <?php echo $email; ?></p>
  <p>This page is reserved for future features like updating your password or profile details.</p>
  <a href="dashboard.php" class="back-btn">← Back to Dashboard</a>
</div>
</body>
</html>
