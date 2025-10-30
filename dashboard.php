<?php
session_start();
include 'db.php';
include 'topbar.php';

if (!isset($_SESSION['email'])) {
  header("Location: login.php");
  exit();
}

$email = $_SESSION['email'];

// Counts (use consistent column names)
$product_count = $conn->query("SELECT COUNT(*) AS total FROM products WHERE seller_email='$email'")->fetch_assoc()['total'];
$order_count = $conn->query("SELECT COUNT(*) AS total FROM orders WHERE buyer_email='$email'")->fetch_assoc()['total'];
$cart_count = $conn->query("SELECT COUNT(*) AS total FROM cart WHERE user_email='$email'")->fetch_assoc()['total'];

$products = $conn->query("SELECT * FROM products ORDER BY id DESC");
?>
<!DOCTYPE html>
<html>
<head><title>Dashboard - Studex</title><link rel="stylesheet" href="style.css"></head>
<body>
<div class="container">
  <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?> 👋</h2>

  <div style="display:flex;gap:20px;margin-bottom:20px;">
    <div style="padding:12px;border-radius:8px;background:#f0f8ff;">🛍️ My Products: <strong><?php echo $product_count; ?></strong></div>
    <div style="padding:12px;border-radius:8px;background:#f0f8ff;">🧾 My Orders: <strong><?php echo $order_count; ?></strong></div>
    <div style="padding:12px;border-radius:8px;background:#f0f8ff;">🛒 My Cart: <strong><?php echo $cart_count; ?></strong></div>
  </div>

  <h3>Available Products</h3>
  <div class="product-grid">
    <?php if ($products->num_rows > 0): ?>
      <?php while ($p = $products->fetch_assoc()): ?>
      <div class="product-card">
        <h4><?php echo htmlspecialchars($p['name']); ?></h4>
        <p>Brand: <?php echo htmlspecialchars($p['brand']); ?></p>
        <p>₹<?php echo number_format($p['price'],2); ?></p>
        <p>Stock: <?php echo intval($p['stock']); ?></p>

        <?php if (intval($p['stock']) > 0): ?>
          <a href="add_to_cart.php?id=<?php echo $p['id']; ?>" class="back-btn">🛒 Add to Cart</a>
        <?php else: ?>
          <button disabled style="background:gray; cursor:not-allowed;">Out of Stock</button>
        <?php endif; ?>

        <?php if ($p['seller_email'] === $email): ?>
          <div style="margin-top:8px;">
            <a href="edit_product.php?id=<?php echo $p['id']; ?>" class="back-btn">✏️ Edit</a> |
            <a href="add_product.php?delete=<?php echo $p['id']; ?>" onclick="return confirm('Delete this product?')" class="back-btn" style="color:#ff4136;">🗑️ Delete</a>
          </div>
        <?php endif; ?>
      </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p class="error">No products available.</p>
    <?php endif; ?>
  </div>
</div>
</body>
</html>
