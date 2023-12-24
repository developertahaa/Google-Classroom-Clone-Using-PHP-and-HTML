<?php
// Connect to MySQL
$conn = mysqli_connect("localhost", "root", "", "classroom");

// Check connection
if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}