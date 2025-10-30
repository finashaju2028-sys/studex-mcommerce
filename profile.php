<?php
session_start();
include 'db.php';
include 'topbar.php';
if (!isset($_SESSION['email'])) { header("Location: login.php"); exit(); }
$email = $_SESSION['email'];

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['username'])) {
  $username = trim($_POST['username']);
  $u = $conn->prepare("UPDATE users SET username = ? WHERE email = ?");
  $u->bind_param("ss", $username, $email);
  $u->execute();
  $_SESSION['username'] = $username;
  $success = "✅ Profile updated.";
}

$user = $conn->prepare("SELECT username, email FROM users WHERE email = ?");
$user->bind_param("s",$email);
$user->execute();
$urow = $user->get_result()->fetch_assoc();
?>
<!DOCTYPE html>
<html>
<head><title>Profile - Studex</title><link rel="stylesheet" href="style.css"></head>
<body>
<div class="container">
  <h2>My Profile</h2>
  <?php if (isset($success)) echo "<p class='success'>$success</p>"; ?>
  <form method="POST">
    <label>Email:</label>
    <input type="text" value="<?= htmlspecialchars($urow['email']); ?>" disabled>
    <label>Username:</label>
    <input type="text" name="username" value="<?= htmlspecialchars($urow['username']); ?>" required>
    <button type="submit">Update</button>
  </form>
  <a href="dashboard.php" class="back-btn">← Back</a>
</div>
</body>
</html>
