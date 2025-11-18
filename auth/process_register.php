<?php
include("../config/db.php");

if (isset($_POST['register'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = md5($_POST['password']);
    $role = $_POST['role'];

    $check = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
    if (mysqli_num_rows($check) > 0) {
        echo "<script>alert('Email already exists!');window.location='register.php';</script>";
    } else {
        mysqli_query($conn, "INSERT INTO users (name, email, password, role) VALUES ('$name','$email','$password','$role')");
        echo "<script>alert('Registration successful!');window.location='login.php';</script>";
    }
}
?>
