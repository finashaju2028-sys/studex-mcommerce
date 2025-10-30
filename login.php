<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $email = trim($_POST['email']);
  $password = $_POST['password'];

  $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    if (password_verify($password, $user['password'])) {
      $_SESSION['email'] = $user['email'];
      $_SESSION['username'] = $user['username'];
      session_regenerate_id(true);
      header("Location: dashboard.php");
      exit();
    } else {
      $error = "Invalid credentials!";
    }
  } else {
    $error = "Invalid credentials!";
  }
}
?>
<!DOCTYPE html>
<html>
<head><title>Login - Studex</title><link rel="stylesheet" href="style.css"></head>
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
    <h1 class="brand-title">Welcome to Studex</h1>
    <h2>Login</h2>
    <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
    <form method="POST">
      <input type="email" name="email" placeholder="College Email" required>
      <input type="password" name="password" placeholder="Password" required>
      <button type="submit">Login</button>
    </form>
    <p style="text-align:center;">Don't have an account? <a href="register.php">Register</a></p>
  </div>
</div>
</body>
</html>
