<?php
session_start();
session_destroy();
echo "<script>alert('You have logged out successfully!');window.location='login.php';</script>";
?>
