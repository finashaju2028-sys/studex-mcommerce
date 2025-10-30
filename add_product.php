<?php
session_start();
include 'db.php';
include 'topbar.php';

if (!isset($_SESSION['email'])) {
  header("Location: login.php");
  exit();
}
$email = $_SESSION['email'];

// delete product
if (isset($_GET['delete'])) {
  $delete_id = intval($_GET['delete']);
  $check = $conn->prepare("SELECT seller_email FROM products WHERE id=?");
  $check->bind_param("i",$delete_id);
  $check->execute();
  $res = $check->get_result();
  if ($res->num_rows == 0) {
    $error = "Product not found.";
  } else {
    $row = $res->fetch_assoc();
    if ($row['seller_email'] !== $email) {
      $error = "Unauthorized to delete this product.";
    } else {
      $conn->query("DELETE FROM orders WHERE product_id=$delete_id");
      $conn->query("DELETE FROM cart WHERE product_id=$delete_id");
      $stmt = $conn->prepare("DELETE FROM products WHERE id=?");
      $stmt->bind_param("i",$delete_id);
      $stmt->execute();
      $success = "Product deleted.";
    }
  }
}

// add product
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['name'])) {
  $name = trim($_POST['name']);
  $price = floatval($_POST['price']);
  $stock = intval($_POST['stock']);
  $brand = trim($_POST['brand']);
  $ins = $conn->prepare("INSERT INTO products (name, price, stock, brand, seller_email) VALUES (?, ?, ?, ?, ?)");
  $ins->bind_param("sdiss", $name, $price, $stock, $brand, $email);
  if ($ins->execute()) $success = "Product added successfully!";
  else $error = "Error adding product: " . $conn->error;
}

$products = $conn->query("SELECT * FROM products ORDER BY id DESC");
?>
<!DOCTYPE html>
<html>
<head><title>Manage Products - Studex</title><link rel="stylesheet" href="style.css"></head>
<body>
<div class="container">
  <h2>Add New Product</h2>
  <?php if (isset($success)) echo "<p class='success'>$success</p>"; ?>
  <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>

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
      <tr><th>Name</th><th>Price</th><th>Stock</th><th>Brand</th><th>Seller</th><th>Actions</th></tr>
      <?php while ($row = $products->fetch_assoc()): ?>
        <tr>
          <td><?php echo htmlspecialchars($row['name']); ?></td>
          <td>₹<?php echo number_format($row['price'],2); ?></td>
          <td><?php echo intval($row['stock']); ?></td>
          <td><?php echo htmlspecialchars($row['brand']); ?></td>
          <td><?php echo htmlspecialchars($row['seller_email']); ?></td>
          <td>
            <?php if ($row['seller_email'] === $email): ?>
              <a href="edit_product.php?id=<?php echo $row['id']; ?>" class="back-btn">✏️ Edit</a> |
              <a href="add_product.php?delete=<?php echo $row['id']; ?>" onclick="return confirm('Delete this product?')" class="back-btn" style="color:#ff4136;">🗑️ Delete</a>
            <?php else: ?>
              <span style="color:gray;">No Access</span>
            <?php endif; ?>
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
