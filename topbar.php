<?php
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<?php if (isset($_SESSION['email'])): ?>
<div class="topbar logged-in-bar">
  <div class="dropdown">
    <button onclick="toggleDropdown()" class="dropbtn">⋮</button>
    <div id="dropdown-content" class="dropdown-content">
      <form method="POST" action="logout.php">
        <button type="submit" class="logout-btn">Logout</button>
      </form>
    </div>
  </div>
  <div class="navbar-center">
    <a href="index.php">Home</a>
    <a href="myorders.php">My Orders</a>
    <a href="contact.php">Contact</a>
    <a href="add_product.php">Add Product</a>
    <a href="cart.php">Cart</a>
  </div>
  <div class="navbar-links">
    👤 <?php echo htmlspecialchars($_SESSION['username']); ?> ▾
  </div>
</div>
<?php else: ?>
<div class="topbar auth-bar">
  <div class="logo">Studex</div>
  <div class="navbar-links">
    <a href="login.php">Login</a>
    <a href="register.php">Register</a>
  </div>
</div>
<?php endif; ?>

<script>
function toggleDropdown() {
  const menu = document.getElementById("dropdown-content");
  menu.style.display = menu.style.display === "block" ? "none" : "block";
}
window.onclick = function(event) {
  if (!event.target.matches('.dropbtn')) {
    const dropdowns = document.getElementsByClassName("dropdown-content");
    for (let i = 0; i < dropdowns.length; i++) {
      dropdowns[i].style.display = "none";
    }
  }
}
</script>
