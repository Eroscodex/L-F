<?php
session_start();
include("../config/db.php");

// Check if logged in
if (!isset($_SESSION['email'])) {
    echo "<script>alert('Access denied! Please login first.');window.location='../auth/login.php';</script>";
    exit();
}

$user_name = $_SESSION['name'];

// Check if report ID is provided
if (!isset($_GET['id'])) {
    echo "<script>alert('No report selected.');window.location='my_reports.php';</script>";
    exit();
}

$report_id = intval($_GET['id']);

// Fetch the report
$result = mysqli_query($conn, "SELECT * FROM items WHERE id=$report_id AND reporter_name='$user_name'");
if (!$result || mysqli_num_rows($result) == 0) {
    echo "<script>alert('Report not found.');window.location='my_reports.php';</script>";
    exit();
}

$report = mysqli_fetch_assoc($result);
$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item_name = mysqli_real_escape_string($conn, trim($_POST['item_name']));
    $description = mysqli_real_escape_string($conn, trim($_POST['description']));
    $item_type = mysqli_real_escape_string($conn, $_POST['item_type']);
    
    if (empty($item_name) || empty($description) || empty($item_type)) {
        $error = "Please fill in all required fields.";
    } else {
        $photo = $report['photo'];
        if (!empty($_FILES['photo']['name'])) {
            $target_dir = "../uploads/";
            $filename = time() . "_" . basename($_FILES["photo"]["name"]);
            $target_file = $target_dir . $filename;
            
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
            $check = getimagesize($_FILES["photo"]["tmp_name"]);
            
            if($check === false) {
                $error = "File is not an image.";
            } elseif ($_FILES["photo"]["size"] > 2 * 1024 * 1024) {
                $error = "Image size should not exceed 2MB.";
            } elseif (!in_array($imageFileType, ['jpg','jpeg','png','gif'])) {
                $error = "Only JPG, JPEG, PNG, and GIF files are allowed.";
            } else {
                if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
                    if (!empty($report['photo']) && file_exists($target_dir . $report['photo'])) {
                        unlink($target_dir . $report['photo']);
                    }
                    $photo = $filename;
                } else {
                    $error = "Failed to upload image.";
                }
            }
        }

        if ($error === '') {
            $update = mysqli_query($conn, "
                UPDATE items 
                SET item_name='$item_name', description='$description', item_type='$item_type', photo='$photo'
                WHERE id=$report_id
            ");
            if ($update) {
                $success = "Report updated successfully!";
                $result = mysqli_query($conn, "SELECT * FROM items WHERE id=$report_id AND reporter_name='$user_name'");
                $report = mysqli_fetch_assoc($result);
            } else {
                $error = "Failed to update report: " . mysqli_error($conn);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Report - Digital Lost & Found</title>
<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap');

* {
    box-sizing: border-box;
}

body {
    font-family: 'Poppins', sans-serif;
    background: #0a0a23;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    padding: 30px 15px;
    color: #fff;
}

h2 {
    text-align: center;
    color: #00ff99;
    margin-bottom: 25px;
    font-weight: 600;
    font-size: 24px;
    text-shadow: 0 0 10px rgba(0, 255, 153, 0.7);
}

form {
    background: #141438;
    padding: 25px 30px;
    border-radius: 16px;
    width: 100%;
    max-width: 400px;
    box-shadow: 0 0 25px rgba(0, 255, 153, 0.15);
}

label {
    display: block;
    margin: 12px 0 6px;
    font-weight: 500;
    color: #00ff99;
    text-shadow: 0 0 6px rgba(0, 255, 153, 0.4);
}

input[type="text"], textarea, select {
    width: 100%;
    padding: 10px;
    border-radius: 8px;
    border: 1px solid #00ff99;
    background: #0a0a23;
    color: #fff;
    font-size: 14px;
    outline: none;
    transition: 0.3s;
}

input[type="text"]::placeholder, textarea::placeholder {
    color: rgba(255, 255, 255, 0.6);
}

input[type="text"]:focus, textarea:focus, select:focus {
    border-color: #00ff99;
    box-shadow: 0 0 10px rgba(0, 255, 153, 0.6);
}

textarea {
    resize: vertical;
}

input[type="file"] {
    margin-top: 5px;
    color: #fff;
}

button {
    width: 100%;
    margin-top: 20px;
    padding: 12px;
    background: #00ff99;
    border: none;
    color: #0a0a23;
    font-weight: 600;
    border-radius: 8px;
    cursor: pointer;
    transition: 0.3s;
    font-size: 15px;
    box-shadow: 0 0 15px rgba(0, 255, 153, 0.5);
}

button:hover {
    background: #0a0a23;
    color: #00ff99;
    border: 1px solid #00ff99;
    box-shadow: 0 0 20px rgba(0, 255, 153, 0.8);
}

.error {
    color: #ff4d4d;
    background: rgba(255, 0, 0, 0.1);
    border-left: 4px solid #ff4d4d;
    padding: 8px;
    margin-bottom: 10px;
    border-radius: 6px;
}

.success {
    color: #00ff99;
    background: rgba(0, 255, 153, 0.1);
    border-left: 4px solid #00ff99;
    padding: 8px;
    margin-bottom: 10px;
    border-radius: 6px;
}

img {
    max-width: 150px;
    border-radius: 10px;
    margin-top: 10px;
    box-shadow: 0 0 10px rgba(0, 255, 153, 0.3);
}

/* Responsive Design */
@media (max-width: 480px) {
    form {
        padding: 20px;
        border-radius: 12px;
    }
    h2 {
        font-size: 20px;
    }
    button {
        font-size: 14px;
    }
}
</style>
</head>
<body>

<h2>✏️ Edit My Report</h2>

<form method="POST" enctype="multipart/form-data">
    <?php if($error) echo "<div class='error'>$error</div>"; ?>
    <?php if($success) echo "<div class='success'>$success</div>"; ?>

    <label for="item_name">Item Name</label>
    <input type="text" name="item_name" id="item_name" value="<?php echo htmlspecialchars($report['item_name']); ?>" required>

    <label for="description">Description</label>
    <textarea name="description" id="description" rows="4" required><?php echo htmlspecialchars($report['description']); ?></textarea>

    <label for="item_type">Item Type</label>
    <select name="item_type" id="item_type" required>
        <option value="lost" <?php if($report['item_type']=='lost') echo 'selected'; ?>>Lost</option>
        <option value="found" <?php if($report['item_type']=='found') echo 'selected'; ?>>Found</option>
    </select>

    <label for="photo">Photo (optional)</label>
    <input type="file" name="photo" id="photo" accept="image/*">
    <?php if(!empty($report['photo']) && file_exists("../uploads/".$report['photo'])): ?>
        <img src="../uploads/<?php echo $report['photo']; ?>" alt="Current Photo">
    <?php endif; ?>

    <button type="submit">💾 Update Report</button>
</form>

</body>
</html>
