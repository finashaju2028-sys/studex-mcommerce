<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $username = trim($_POST['username']);
  $email = trim($_POST['email']);
  $password = $_POST['password'];

  if (empty($username) || empty($email) || empty($password)) {
    $error = "Please fill all fields.";
  } else {
    $hash = password_hash($password, PASSWORD_BCRYPT);
    $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $email, $hash);
    try {
      $stmt->execute();
      header("Location: login.php");
      exit();
    } catch (mysqli_sql_exception $e) {
      $error = "Email already registered.";
    }
  }
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
    <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
    <form method="POST">
      <input type="text" name="username" placeholder="Full Name" required>
      <input type="email" name="email" placeholder="College Email" required>
      <input type="password" name="password" placeholder="Create Password" required>
      <button type="submit">Register</button>
    </form>
    <p style="text-align:center;">Already have an account? <a href="login.php">Login</a></p>
  </div>
</div>
</body>
</html>
