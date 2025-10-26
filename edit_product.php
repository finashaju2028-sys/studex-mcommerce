<?php
session_start();
include 'db.php';
include 'topbar.php';

if (!isset($_SESSION['email'])) {
  header("Location: login.php");
  exit();
}

if (!isset($_GET['id'])) {
  echo "<p class='error'>Product ID missing.</p>";
  exit();
}

$product_id = $_GET['id'];

// Fetch product by ID
$result = $conn->query("SELECT * FROM products WHERE id=$product_id");
if ($result->num_rows == 0) {
  echo "<p class='error'>Product not found or unauthorized.</p>";
  exit();
}

$product = $result->fetch_assoc();

// Handle update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $name = $_POST['name'];
  $price = $_POST['price'];
  $stock = $_POST['stock'];
  $brand = $_POST['brand'];

  $stmt = $conn->prepare("UPDATE products SET name=?, price=?, stock=?, brand=? WHERE id=?");
  $stmt->bind_param("sdisi", $name, $price, $stock, $brand, $product_id);
  $stmt->execute();
  $success = "✅ Product updated successfully!";
  // Refresh product data
  $product = $conn->query("SELECT * FROM products WHERE id=$product_id")->fetch_assoc();
}
?>
<!DOCTYPE html>
<html>
<head><title>Edit Product - Studex</title><link rel="stylesheet" href="style.css"></head>
<body>
<div class="container">
  <h2>Edit Product</h2>
  <?php if (isset($success)) echo "<p class='success'>$success</p>"; ?>
  <form method="POST">
    <label>Product Name:</label>
    <input type="text" name="name" value="<?php echo $product['name']; ?>" required>
    <label>Price (₹):</label>
    <input type="number" step="0.01" name="price" value="<?php echo $product['price']; ?>" required>
    <label>Stock Quantity:</label>
    <input type="number" name="stock" value="<?php echo $product['stock']; ?>" required>
    <label>Brand Name:</label>
    <input type="text" name="brand" value="<?php echo $product['brand']; ?>" required>
    <button type="submit">Update Product</button>
  </form>
  <a href="add_product.php" class="back-btn">← Back to Product List</a>
</div>
</body>
</html>
