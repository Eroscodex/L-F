<?php
session_start();
include("../config/db.php");

// Check login
if (!isset($_SESSION['email'])) {
    echo "<script>alert('Access denied! Please login first.');window.location='../auth/login.php';</script>";
    exit();
}

if (isset($_POST['report'])) {
    $name = trim($_POST['item_name']);
    $desc = trim($_POST['description']);
    $type = trim($_POST['item_type']);
    $reporter = $_SESSION['name'];
    $reporter_email = $_SESSION['email'];

    // Handle photo upload
    $photo = $_FILES['photo']['name'];
    $target_dir = "../uploads/";
    $target_file = $target_dir . basename($photo);

    // Create uploads folder if missing
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    // Validate file and move upload
    if (!empty($photo)) {
        move_uploaded_file($_FILES['photo']['tmp_name'], $target_file);
    } else {
        $photo = ""; // optional
    }

    // Insert into database with reporter_email
    $insert = mysqli_query($conn, "
        INSERT INTO items (item_name, description, photo, reporter_name, reporter_email, item_type, status, date_reported)
        VALUES ('$name', '$desc', '$photo', '$reporter', '$reporter_email', '$type', 'pending', NOW())
    ");

    if ($insert) {
        echo "<script>alert('Item reported successfully! Pending admin approval.');window.location='my_reports.php';</script>";
    } else {
        echo "<script>alert('Error reporting item: " . mysqli_error($conn) . "');window.location='report_item.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Report Item</title>
<style>
  @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500&display=swap');

  body {
      font-family: "Poppins", sans-serif;
      background: #0a0a23;
      color: #fff;
      margin: 0;
      padding: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
  }

  .report-container {
      background: rgba(255, 255, 255, 0.05);
      border: 1px solid #00ff99;
      padding: 40px;
      border-radius: 15px;
      width: 100%;
      max-width: 500px;
      box-shadow: 0 0 15px rgba(0,255,153,0.2);
  }

  h2 {
      text-align: center;
      margin-bottom: 25px;
      color: #00ff99;
      font-size: 26px;
  }

  input[type="text"], textarea, select, input[type="file"] {
      width: 95%;
      padding: 10px 12px;
      margin: 10px 0;
      border-radius: 8px;
      background: rgba(255, 255, 255, 0.08);
      border: 1px solid #00ff99;
      color: #fff;
      font-size: 15px;
      transition: 0.3s;
      resize: none;
  }

  input:focus, textarea:focus, select:focus {
      outline: none;
      box-shadow: 0 0 10px #00ff99;
      border-color: #00ff99;
  }

  textarea {
      min-height: 100px;
  }

  button {
      width: 100%;
      padding: 12px;
      margin-top: 20px;
      border: none;
      border-radius: 10px;
      background: #00ff99;
      color: #0a0a23;
      font-size: 16px;
      font-weight: 600;
      cursor: pointer;
      transition: 0.3s;
  }

  button:hover {
      background: #00cc7a;
      box-shadow: 0 0 15px #00ff99;
  }
</style>
</head>
<body>

<div class="report-container">
    <h2>Report Lost/Found Item</h2>
    <form method="POST" enctype="multipart/form-data">
        <input type="text" name="item_name" placeholder="Item Name" required>
        <textarea name="description" placeholder="Description" required></textarea>
        <select name="item_type" required>
            <option value="">Select Type</option>
            <option value="lost">Lost</option>
            <option value="found">Found</option>
        </select>
        <input type="file" name="photo" accept="image/*" required>
        <button type="submit" name="report">Submit Report</button>
    </form>
</div>

</body>
</html>
