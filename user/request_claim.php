<?php
session_start();
include("../config/db.php");

// Ensure user is logged in
if (!isset($_SESSION['email']) || !isset($_SESSION['name'])) {
    echo "<script>alert('Access denied! Please login first.'); window.location='../auth/login.php';</script>";
    exit;
}

// Check if request came via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $item_id = $_POST['item_id'];
    $claimant_name = $_SESSION['name'];
    $claimant_email = $_SESSION['email'];

    // Check if item exists
    $item_check = mysqli_query($conn, "SELECT * FROM items WHERE id='$item_id'");
    $item = mysqli_fetch_assoc($item_check);

    if (!$item) {
        echo "<script>alert('Item not found.'); window.history.back();</script>";
        exit;
    }

    // Prevent claiming own item
    if (!empty($item['reporter_email']) && $item['reporter_email'] == $claimant_email) {
        echo "<script>alert('You cannot claim your own found item.'); window.history.back();</script>";
        exit;
    }

    // Check if already has pending claim
    $existing = mysqli_query($conn, "
        SELECT * FROM claims 
        WHERE item_id='$item_id' 
        AND claimant_email='$claimant_email' 
        AND status='pending'
    ");
    if (mysqli_num_rows($existing) > 0) {
        echo "<script>alert('You already submitted a claim request for this item. Please wait for admin approval.'); window.history.back();</script>";
        exit;
    }

    // Insert claim record
    $insert = mysqli_query($conn, "
        INSERT INTO claims (item_id, claimant_name, claimant_email, status, date_requested)
        VALUES ('$item_id', '$claimant_name', '$claimant_email', 'pending', NOW())
    ");

    if ($insert) {
        // Update item status
        mysqli_query($conn, "UPDATE items SET status='pending_claim' WHERE id='$item_id'");

        // Notify the reporter (if they have email)
        if (!empty($item['reporter_email'])) {
            $subject = "Claim Request for Your Reported Item";
            $message = "$claimant_name has requested to claim the item '{$item['item_name']}'.";
            mysqli_query($conn, "
                INSERT INTO notifications (recipient_email, subject, message, status, date_sent)
                VALUES ('{$item['reporter_email']}', '$subject', '$message', 'unread', NOW())
            ");
        }

        // Notify admin
        mysqli_query($conn, "
            INSERT INTO notifications (recipient_email, subject, message, status, date_sent)
            VALUES ('admin@system.com', 'New Claim Request', '$claimant_name has requested to claim item ID #$item_id.', 'unread', NOW())
        ");

        echo "<script>alert('Claim request sent successfully! Admin will review your request.'); window.location='../public/index.php';</script>";
    } else {
        echo "<script>alert('Error sending claim request. Please try again.'); window.history.back();</script>";
    }
} else {
    // Redirect if accessed directly
    header("Location: ../public/index.php");
    exit;
}
?>
