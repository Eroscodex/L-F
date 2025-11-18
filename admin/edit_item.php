<?php
include("../config/db.php");
session_start();

// Only admin access
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    exit("<script>alert('Access denied!');window.location='../auth/login.php';</script>");
}

// Get item ID
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$item_result = mysqli_query($conn, "SELECT * FROM items WHERE id=$id");
$item = mysqli_fetch_assoc($item_result);

if (!$item) {
    exit("<script>alert('Item not found!');window.location='manage_items.php';</script>");
}

// Handle form submission
if (isset($_POST['submit'])) {
    $item_name = mysqli_real_escape_string($conn, $_POST['item_name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $item_type = $_POST['item_type'];
    $reporter_name = $_POST['reporter_name'];

    // Handle photo upload
    $photo_name = $item['photo']; // keep old photo
    if (!empty($_FILES['photo']['name'])) {
        $photo_name = time() . '_' . $_FILES['photo']['name'];
        move_uploaded_file($_FILES['photo']['tmp_name'], "../uploads/" . $photo_name);
    }

    // Update database
    $query = "UPDATE items SET 
                item_name='$item_name', 
                description='$description', 
                photo='$photo_name', 
                reporter_name='$reporter_name', 
                item_type='$item_type'
              WHERE id=$id";
    
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Item updated successfully!');window.location='manage_items.php';</script>";
    } else {
        echo "<script>alert('Update failed: " . mysqli_error($conn) . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Item</title>
<style>
body {
    font-family: 'Segoe UI', Arial, sans-serif;
    background: #0a0a23;
    color: #e6f5ef;
    padding: 30px;
    min-height: 100vh;
}


form {
    max-width: 500px;
    margin: auto;
    background: rgba(15, 15, 40, 0.95);
    padding: 25px 30px;
    border-radius: 10px;
    box-shadow: 0 0 25px rgba(0, 255, 153, 0.2);
    border: 1px solid rgba(0, 255, 153, 0.3);
}

input, textarea, select {
    width: 95%;
    padding: 10px 12px;
    margin-bottom: 15px;
    border-radius: 5px;
    border: 1px solid #00ff99;
    background: #111132;
    color: #00ff99;
    font-size: 14px;
    outline: none;
    transition: all 0.3s ease;
}
input:focus, textarea:focus, select:focus {
    box-shadow: 0 0 10px #00ff99;
}

button {
    width: 100%;
    padding: 12px;
    border-radius: 6px;
    background: #00ff99;
    color: #0a0a23;
    font-weight: 700;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
    text-transform: uppercase;
}
button:hover {
    background: #00cc7a;
    box-shadow: 0 0 15px #00ff99;
}

.back-btn {
    width: 90%;
    display: inline-block;
    margin-top: 20px;
    text-decoration: none;
    color: #0a0a23;
    background: #00ff99;
    padding: 10px 25px;
    border-radius: 6px;
    font-weight: 600;
    transition: all 0.3s ease;
    text-align: center; 
}
.back-btn:hover {
    background: #00cc7a;
    box-shadow: 0 0 15px #00ff99;
    color: #000;
}

img {
    max-width: 120px;
    margin-bottom: 10px;
    border-radius: 8px;
    border: 2px solid #00ff99;
    box-shadow: 0 0 10px rgba(0, 255, 153, 0.3);
}

h2 {
    text-align: center;
    color: #00ff99;
    text-shadow: 0 0 8px #00ff99;
    margin-bottom: 20px;
}
</style>
</head>
<body>

<h2>Edit Item</h2>

<form method="POST" enctype="multipart/form-data">
    <input type="text" name="item_name" placeholder="Item Name" value="<?php echo htmlspecialchars($item['item_name']); ?>" required>
    <textarea name="description" placeholder="Description" required><?php echo htmlspecialchars($item['description']); ?></textarea>
    
    <select name="item_type" required>
        <option value="lost" <?php if($item['item_type']=='lost') echo 'selected'; ?>>Lost</option>
        <option value="found" <?php if($item['item_type']=='found') echo 'selected'; ?>>Found</option>
    </select>

    <input type="text" name="reporter_name" placeholder="Reporter Name" value="<?php echo htmlspecialchars($item['reporter_name']); ?>" required>

    <?php if(!empty($item['photo'])): ?>
        <img src="../uploads/<?php echo $item['photo']; ?>" alt="Current Photo">
    <?php endif; ?>
    <input type="file" name="photo" accept="image/*">

    <button type="submit" name="submit">Update Item</button>
    <a href="manage_items.php" class="back-btn">⬅ Back</a>
</form>
</body>
</html>
