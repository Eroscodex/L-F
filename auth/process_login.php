<?php
include("../config/db.php");
session_start();

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = md5($_POST['password']);

    $query = mysqli_query($conn, "SELECT * FROM users WHERE email='$email' AND password='$password'");
    $user = mysqli_fetch_assoc($query);

    if ($user) {
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['name'] = $user['name'];

        if ($user['role'] == 'admin') {
            echo "<script>alert('Welcome Admin!');window.location='../admin/dashboard.php';</script>";
        } else {
            echo "<script>alert('Login successful!');window.location='../user/home.php';</script>";
        }
    } else {
        echo "<script>alert('Invalid email or password!');window.location='login.php';</script>";
    }
}
?>
