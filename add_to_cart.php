<?php
session_start();
include 'db.php';

if (!isset($_SESSION['email'])) {
  header("Location: login.php");
  exit();
}
$email = $_SESSION['email'];

if (isset($_GET['id'])) {
  $product_id = intval($_GET['id']);
  // check product exists
  $p = $conn->prepare("SELECT stock FROM products WHERE id=?");
  $p->bind_param("i",$product_id);
  $p->execute();
  $res = $p->get_result();
  if ($res->num_rows == 0) { echo "<div class='container'><p class='error'>Product not found.</p></div>"; exit(); }
  $prod = $res->fetch_assoc();
  if ($prod['stock'] < 1) { echo "<div class='container'><p class='error'>Out of stock.</p></div>"; exit(); }

  // already in cart?
  $c = $conn->prepare("SELECT id, quantity FROM cart WHERE user_email=? AND product_id=?");
  $c->bind_param("si",$email,$product_id);
  $c->execute();
  $cres = $c->get_result();

  if ($cres->num_rows > 0) {
    $crow = $cres->fetch_assoc();
    $up = $conn->prepare("UPDATE cart SET quantity = quantity + 1 WHERE id=?");
    $up->bind_param("i",$crow['id']);
    $up->execute();
  } else {
    $ins = $conn->prepare("INSERT INTO cart (user_email, product_id, quantity) VALUES (?, ?, 1)");
    $ins->bind_param("si",$email,$product_id);
    $ins->execute();
  }

  header("Location: cart.php");
  exit();
} else {
  header("Location: dashboard.php");
  exit();
}
?>
