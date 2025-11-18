<?php
$conn = mysqli_connect("localhost", "root", "", "digital_lost_found");

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
?>
