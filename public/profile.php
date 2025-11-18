<?php
session_start();
include("../config/db.php");

// Ensure logged in
if (!isset($_SESSION['email'])) {
    echo "<script>alert('Please login first!');window.location='../auth/login.php';</script>";
    exit;
}

$user_email = $_SESSION['email'];

// Fetch user info
$query = "SELECT * FROM users WHERE email='$user_email' LIMIT 1";
$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) == 0) {
    echo "<script>alert('User not found!');window.location='../auth/login.php';</script>";
    exit;
}

$user = mysqli_fetch_assoc($result);

// Update profile
if (isset($_POST['update_profile'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    if ($_SESSION['role'] == 'admin') {
        $status = mysqli_real_escape_string($conn, $_POST['status']);
        mysqli_query($conn, "UPDATE users SET name='$name', status='$status' WHERE email='$user_email'");
    } else {
        mysqli_query($conn, "UPDATE users SET name='$name' WHERE email='$user_email'");
    }
    $_SESSION['name'] = $name;
    echo "<script>alert('Profile updated successfully!');window.location='profile.php';</script>";
    exit;
}

// Change password
if (isset($_POST['change_password'])) {
    $current = md5($_POST['current_password']);
    $new = md5($_POST['new_password']);
    $confirm = md5($_POST['confirm_password']);

    if ($current !== $user['password']) {
        echo "<script>alert('Current password is incorrect!');window.location='profile.php';</script>";
        exit;
    }

    if ($new !== $confirm) {
        echo "<script>alert('New passwords do not match!');window.location='profile.php';</script>";
        exit;
    }

    mysqli_query($conn, "UPDATE users SET password='$new' WHERE email='$user_email'");
    echo "<script>alert('Password changed successfully!');window.location='profile.php';</script>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Profile Dashboard</title>
<link rel="icon" type="image/jpg" href="../images/L&F.jpg">
<style>
body {
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(135deg, #0a0a23, #05051a);
    color: #fff;
    margin: 0;
    padding: 0;
    min-height: 100vh;
}

/* Container */
.container {
    max-width: 550px;
    margin: 60px auto;
    background: rgba(10, 10, 35, 0.95);
    padding: 35px 40px;
    border-radius: 14px;
    box-shadow: 0 0 25px rgba(0, 255, 153, 0.25);
    border: 1px solid rgba(0, 255, 153, 0.25);
    backdrop-filter: blur(6px);
}

/* Header */
h2 {
    text-align: center;
    color: #00ff99;
    text-shadow: 0 0 15px #00ff99;
    margin-bottom: 25px;
    font-weight: 600;
    letter-spacing: 1px;
}

/* Form */
form {
    margin-bottom: 25px;
}

/* Labels */
label {
    display: block;
    margin-bottom: 6px;
    color: #00ff99;
    font-weight: 500;
    text-shadow: 0 0 6px rgba(0, 255, 153, 0.6);
}

/* Inputs */
input[type="text"], 
input[type="email"], 
input[type="password"] {
    width: 90%;
    padding: 12px 14px;
    margin-bottom: 15px;
    border-radius: 8px;
    border: 1px solid rgba(0, 255, 153, 0.2);
    background: rgba(255, 255, 255, 0.05);
    color: #fff;
    font-size: 14px;
    transition: all 0.3s ease;
    outline: none;
}

input[type="text"]:focus, 
input[type="email"]:focus, 
input[type="password"]:focus {
    border-color: #00ff99;
    box-shadow: 0 0 12px rgba(0, 255, 153, 0.5);
}

input[disabled] {
    background: rgba(255, 255, 255, 0.1);
    color: #aaa;
    cursor: not-allowed;
}

/* Buttons */
button {
    padding: 10px 22px;
    border: none;
    border-radius: 8px;
    background: #00ff99;
    color: #0a0a23;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    margin-top: 10px;
    box-shadow: 0 0 15px rgba(0, 255, 153, 0.4);
}

button:hover {
    background: transparent;
    color: #00ff99;
    border: 1px solid #00ff99;
    box-shadow: 0 0 20px #00ff99;
}

/* Back Button */
.back-btn {
    display: inline-block;
    margin-bottom: 20px;
    background: #ff3b3b;
    color: #fff;
    padding: 9px 18px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    box-shadow: 0 0 12px rgba(255, 59, 59, 0.5);
    transition: all 0.3s ease;
}

.back-btn:hover {
    background: transparent;
    color: #ff3b3b;
    border: 1px solid #ff3b3b;
    box-shadow: 0 0 20px #ff3b3b;
}

/* Divider */
hr {
    border: none;
    border-top: 1px solid rgba(0, 255, 153, 0.4);
    margin: 25px 0;
}

/* Responsive */
@media screen and (max-width: 600px) {
    .container {
        width: 90%;
        padding: 25px 20px;
    }

    input, button {
        font-size: 13px;
    }
}
</style>

</head>
<body>
<div class="container">
    
    <h2><?php echo ucfirst($user['role']); ?> Profile</h2> 

    <!-- Profile Update Form -->
    <form method="POST">
        <input type="hidden" name="update_profile" value="1">
        <label>Name:</label>
        <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>

        <label>Email:</label>
        <input type="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>

        <?php if ($_SESSION['role'] == 'admin'): ?>
            <label>Status:</label>
            <input type="text" name="status" value="<?php echo htmlspecialchars($user['status']); ?>" required>
        <?php else: ?>
            <label>Status:</label>
            <input type="text" value="<?php echo htmlspecialchars($user['status']); ?>" disabled>
        <?php endif; ?>

        <button type="submit">Update Profile</button>
    </form>

    <hr>

    <!-- Change Password Form -->
    <form method="POST">
        <input type="hidden" name="change_password" value="1">
        <label>Current Password:</label>
        <input type="password" name="current_password" required>

        <label>New Password:</label>
        <input type="password" name="new_password" required>

        <label>Confirm New Password:</label>
        <input type="password" name="confirm_password" required>

        <button type="submit">Change Password</button><br><br>
        <div><a href="../public/index.php" class="back-btn">← Back to Dashboard</a></div>
    </form>
</div>
</body>
</html>
