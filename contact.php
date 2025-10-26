<?php
session_start();
include 'db.php';
include 'topbar.php';

if (!isset($_SESSION['email'])) {
  header("Location: login.php");
  exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $email = $_SESSION['email'];
  $subject = $_POST['subject'];
  $message = $_POST['message'];

  $stmt = $conn->prepare("INSERT INTO messages (email, subject, message) VALUES (?, ?, ?)");
  $stmt->bind_param("sss", $email, $subject, $message);
  $stmt->execute();
  $success = "✅ Message sent successfully!";
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Contact - Studex</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
  <h2>Contact Us</h2>
  <?php if (isset($success)) echo "<p class='success'>$success</p>"; ?>
  <form method="POST">
    <label>Subject:</label>
    <input type="text" name="subject" required>
    <label>Message:</label>
    <textarea name="message" rows="5" required></textarea>
    <button type="submit">Send Message</button>
  </form>
  <a href="dashboard.php" class="back-btn">← Back to Dashboard</a>
</div>
</body>
</html>
