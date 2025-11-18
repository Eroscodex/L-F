<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    echo "<script>alert('Access denied!');window.location='../auth/login.php';</script>";
    exit();
}

$id = $_GET['id'];
mysqli_query($conn, "DELETE FROM items WHERE id=$id");
echo "<script>alert('Item deleted successfully!');window.location='items.php';</script>";
?>
