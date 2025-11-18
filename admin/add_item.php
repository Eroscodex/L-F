<?php
include("../config/db.php");
session_start();


if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    exit("<script>alert('Access denied!');window.location='../auth/login.php';</script>");
}


if (isset($_POST['submit'])) {
    $item_name = mysqli_real_escape_string($conn, $_POST['item_name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $item_type = $_POST['item_type'];
    $reporter_name = $_POST['reporter_name'];


    $photo_name = null;
    if (!empty($_FILES['photo']['name'])) {
        $photo_name = time() . '_' . $_FILES['photo']['name'];
        move_uploaded_file($_FILES['photo']['tmp_name'], "../uploads/" . $photo_name);
    }

  
    $query = "INSERT INTO items (item_name, description, photo, reporter_name, item_type, status) 
              VALUES ('$item_name', '$description', '$photo_name', '$reporter_name', '$item_type', 'pending')";
    mysqli_query($conn, $query);
    echo "<script>alert('Item added successfully!');window.location='manage_items.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add Item</title>
<style>
body { font-family: Arial,sans-serif; background:#f0f2f5; padding:20px; }
form { max-width:500px; margin:auto; background:#fff; padding:20px; border-radius:10px; box-shadow:0 5px 15px rgba(0,0,0,0.1); }
input, textarea, select, button { width:100%; padding:10px; margin-bottom:15px; border-radius:5px; border:1px solid #ccc; font-size:14px; }
button { background:#28a745; color:#fff; border:none; cursor:pointer; transition:0.3s; }
button:hover { background:#218838; }
.back-btn { display:inline-block; margin-top:10px; text-decoration:none; color:#fff; background:#e74c3c; padding:10px 20px; border-radius:8px; }
.back-btn:hover { background:#c0392b; }
</style>
</head>
<body>

<h2 style="text-align:center;">Add New Item</h2>

<form method="POST" enctype="multipart/form-data">
    <input type="text" name="item_name" placeholder="Item Name" required>
    <textarea name="description" placeholder="Description" required></textarea>
    <select name="item_type" required>
        <option value="">Select Type</option>
        <option value="lost">Lost</option>
        <option value="found">Found</option>
    </select>
    <input type="text" name="reporter_name" placeholder="Reporter Name" required>
    <input type="file" name="photo" accept="image/*">
    <button type="submit" name="submit">Add Item</button>
</form>

<div style="text-align:center;">
    <a href="manage_items.php" class="back-btn">⬅ Back</a>
</div>

</body>
</html>
