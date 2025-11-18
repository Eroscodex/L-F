<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['email']) || empty($_POST['selected_user'])) exit;

$sender = $_SESSION['email'];
$receiver = $_POST['selected_user'];
$message = trim($_POST['message']);
$imagePath = null;

$uploadDir = "../uploads/messages/";
if (!file_exists($uploadDir)) mkdir($uploadDir, 0777, true);

if (!empty($_FILES['image']['name'])) {
    $fileName = time() . "_" . basename($_FILES["image"]["name"]);
    $targetFile = $uploadDir . $fileName;
    $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    $allowed = ['jpg','jpeg','png','gif'];
    if (in_array($fileType, $allowed)) {
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
            $imagePath = "uploads/messages/" . $fileName;
        }
    }
}

if (!empty($message) || $imagePath) {
    $sql = "INSERT INTO messages (sender_email, receiver_email, message, image) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssss", $sender, $receiver, $message, $imagePath);
    mysqli_stmt_execute($stmt);
}
?>
