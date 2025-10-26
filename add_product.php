<?php
session_start();
include 'db.php';
include 'topbar.php';

if (!isset($_SESSION['email'])) {
  header("Location: login.php");
  exit();
}

$email = $_SESSION['email'];

// ✅ Handle product deletion safely
if (isset($_GET['delete'])) {
  $delete_id = $_GET['delete'];

  // Remove related rows first to avoid foreign key constraint error
  $conn->query("DELETE FROM orders WHERE product_id=$delete_id");
  $conn->query("DELETE FROM cart WHERE product_id=$delete_id");

  // Now delete the product
  $conn->query("DELETE FROM products WHERE id=$delete_id");

  header("Location: add_product.php");
  exit();
}

// ✅ Handle product addition
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['name'])) {
  $name = $_POST['name'];
  $price = $_POST['price'];
  $stock = $_POST['stock'];
  $brand = $_POST['brand'];

  $stmt = $conn->prepare("INSERT INTO products (name, price, stock, brand, seller_email) VALUES (?, ?, ?, ?, ?)");
  $stmt->bind_param("sdiss", $name, $price, $stock, $brand, $email);
  $stmt->execute();
  $success = "✅ Product added successfully!";
}

// ✅ Fetch all products (admin-level view)
$products = $conn->query("SELECT * FROM products");
?>
<!DOCTYPE html>
<html>
<head><title>Manage Products - Studex</title><link rel="stylesheet" href="style.css"></head>
<body>
<div class="container">
  <h2>Add New Product</h2>
  <?php if (isset($success)) echo "<p class='success'>$success</p>"; ?>
  <form method="POST">
    <label>Product Name:</label>
    <input type="text" name="name" required>
    <label>Price (₹):</label>
    <input type="number" step="0.01" name="price" required>
    <label>Stock Quantity:</label>
    <input type="number" name="stock" required>
    <label>Brand Name:</label>
    <input type="text" name="brand" required>
    <button type="submit">Add Product</button>
  </form>

  <h3>All Products</h3>
  <?php if ($products->num_rows > 0): ?>
    <table>
      <tr>
        <th>Name</th>
        <th>Price</th>
        <th>Stock</th>
        <th>Brand</th>
        <th>Seller</th>
        <th>Actions</th>
      </tr>
      <?php while ($row = $products->fetch_assoc()): ?>
      <tr>
        <td><?php echo $row['name']; ?></td>
        <td>₹<?php echo $row['price']; ?></td>
        <td><?php echo $row['stock']; ?></td>
        <td><?php echo $row['brand']; ?></td>
        <td><?php echo $row['seller_email']; ?></td>
        <td>
          <a href="edit_product.php?id=<?php echo $row['id']; ?>" class="back-btn">✏️ Edit</a>
          |
          <a href="add_product.php?delete=<?php echo $row['id']; ?>" onclick="return confirm('Delete this product?')" class="back-btn" style="color:#ff4136;">🗑️ Delete</a>
        </td>
      </tr>
      <?php endwhile; ?>
    </table>
  <?php else: ?>
    <p class="error">No products found.</p>
  <?php endif; ?>
</div>
</body>
</html>
