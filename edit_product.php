<?php
session_start();
include 'db.php';
include 'topbar.php';

if (!isset($_SESSION['email'])) { header("Location: login.php"); exit(); }
$email = $_SESSION['email'];

if (!isset($_GET['id'])) { echo "<div class='container'><p class='error'>Product ID missing.</p></div>"; exit(); }
$id = intval($_GET['id']);

$check = $conn->prepare("SELECT * FROM products WHERE id=?");
$check->bind_param("i",$id);
$check->execute();
$res = $check->get_result();
if ($res->num_rows == 0) { echo "<div class='container'><p class='error'>Product not found.</p></div>"; exit(); }
$product = $res->fetch_assoc();

if ($product['seller_email'] !== $email) {
  echo "<div class='container'><p class='error'>Unauthorized to edit this product.</p></div>";
  exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $name = trim($_POST['name']);
  $price = floatval($_POST['price']);
  $stock = intval($_POST['stock']);
  $brand = trim($_POST['brand']);
  $up = $conn->prepare("UPDATE products SET name=?, price=?, stock=?, brand=? WHERE id=?");
  $up->bind_param("sdssi", $name, $price, $stock, $brand, $id);
  if ($up->execute()) $success = "Product updated!";
  else $error = "Error updating product.";
  $product = $conn->query("SELECT * FROM products WHERE id=$id")->fetch_assoc();
}
?>
<!DOCTYPE html>
<html>
<head><title>Edit Product - Studex</title><link rel="stylesheet" href="style.css"></head>
<body>
<div class="container">
  <h2>Edit Product</h2>
  <?php if (isset($success)) echo "<p class='success'>$success</p>"; ?>
  <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
  <form method="POST">
    <label>Product Name:</label>
    <input type="text" name="name" value="<?= htmlspecialchars($product['name']); ?>" required>
    <label>Price (₹):</label>
    <input type="number" step="0.01" name="price" value="<?= htmlspecialchars($product['price']); ?>" required>
    <label>Stock Quantity:</label>
    <input type="number" name="stock" value="<?= htmlspecialchars($product['stock']); ?>" required>
    <label>Brand Name:</label>
    <input type="text" name="brand" value="<?= htmlspecialchars($product['brand']); ?>" required>
    <button type="submit">Update Product</button>
  </form>
  <a href="add_product.php" class="back-btn">← Back to Product List</a>
</div>
</body>
</html>
