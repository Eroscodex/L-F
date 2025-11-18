<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['email'])) {
    echo "<script>alert('Please login first.');window.location='../auth/login.php';</script>";
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<script>alert('Invalid notification ID.');window.history.back();</script>";
    exit();
}

$notif_id = (int) $_GET['id'];
$action = isset($_GET['action']) ? $_GET['action'] : 'read';
$user_email = $_SESSION['email'];

// Check if it belongs to user
$check = $conn->prepare("SELECT id, recipient_email FROM notifications WHERE id=?");
$check->bind_param("i", $notif_id);
$check->execute();
$res = $check->get_result();
$row = $res->fetch_assoc();

if (!$row) {
    echo "<script>alert('Notification not found.');window.history.back();</script>";
    exit();
}

if ($row['recipient_email'] !== $user_email) {
    echo "<script>alert('You cannot modify this notification.');window.history.back();</script>";
    exit();
}

// Update status
$new_status = ($action == 'unread') ? 'unread' : 'read';
$update = $conn->prepare("UPDATE notifications SET status=? WHERE id=?");
$update->bind_param("si", $new_status, $notif_id);

if ($update->execute()) {
    echo "<script>alert('Notification marked as $new_status.');window.location='notification.php';</script>";
} else {
    echo "<script>alert('Error updating status.');window.history.back();</script>";
}
?>
