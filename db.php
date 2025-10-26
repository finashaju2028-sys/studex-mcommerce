<?php
$conn = new mysqli("localhost", "root", "", "campusmarket_place");
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
?>
