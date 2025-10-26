<?php
include 'db.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $email = $_POST['email'];
  $password = $_POST['password'];
  $stmt = $conn->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
  $stmt->bind_param("ss", $email, $password);
  $stmt->execute();
  header("Location: login.php");
}
?>
<!DOCTYPE html>
<html>
<head><title>Register - Studex</title><link rel="stylesheet" href="style.css"></head>
<body>
<div class="topbar auth-bar">
  <div class="logo">Studex</div>
  <div class="navbar-links">
    <a href="login.php">Login</a>
    <a href="register.php">Register</a>
  </div>
</div>
<div class="centered-form">
  <div class="form-box">
    <h1 class="brand-title">Join Studex</h1>
    <h2>Register</h2>
    <form method="POST">
      <input type="email" name="email" placeholder="College Email" required>
      <input type="password" name="password" placeholder="Create Password" required>
      <button type="submit">Register</button>
    </form>
    <p style="text-align:center;">Already have an account? <a href="login.php">Login</a></p>
  </div>
</div>
</body>
</html>
