<?php
session_start();
include("../config/db.php");

// Admin-only access
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    echo "<script>alert('Access denied!');window.location='../auth/login.php';</script>";
    exit;
}

// Validate query parameters
if (!isset($_GET['action']) || !isset($_GET['id'])) {
    echo "<script>alert('Invalid request!');window.location='manage_claims.php';</script>";
    exit;
}

$action = $_GET['action'];
$claim_id = intval($_GET['id']);

// Fetch claim and related item info
$query = "
    SELECT 
        c.id AS claim_id, 
        c.item_id, 
        c.claimant_name, 
        c.claimant_email,
        i.item_name,
        i.reporter_name,
        i.reporter_email
    FROM claims c
    JOIN items i ON c.item_id = i.id
    WHERE c.id = $claim_id
";
$res = mysqli_query($conn, $query);

if (!$res || mysqli_num_rows($res) == 0) {
    echo "<script>alert('Claim not found!');window.location='manage_claims.php';</script>";
    exit;
}

$data = mysqli_fetch_assoc($res);

// ❌ Prevent user from claiming their own posted item
if ($data['claimant_email'] === $data['reporter_email']) {
    echo "<script>alert('You cannot claim your own posted item!');window.location='manage_claims.php';</script>";
    exit;
}

// ✅ Approve Claim
if ($action === 'approve') {
    // Update claim and item status
    mysqli_query($conn, "UPDATE claims SET status='approved' WHERE id=$claim_id");
    mysqli_query($conn, "UPDATE items SET status='claimed', claimed_by='" . mysqli_real_escape_string($conn, $data['claimant_name']) . "' WHERE id=" . intval($data['item_id']));

    // Notify claimant
    $subject1 = "Claim Approved";
    $message1 = "Hi {$data['claimant_name']}, your claim for '{$data['item_name']}' has been approved. Please coordinate with the finder for pickup.";
    mysqli_query($conn, "INSERT INTO notifications (recipient_email, subject, message, status, date_sent)
                         VALUES ('" . mysqli_real_escape_string($conn, $data['claimant_email']) . "', '$subject1', '$message1', 'unread', NOW())");

    // Notify finder/reporter
    $subject2 = "Item Claimed";
    $message2 = "Hi {$data['reporter_name']}, your reported item '{$data['item_name']}' has been claimed by {$data['claimant_name']}.";
    mysqli_query($conn, "INSERT INTO notifications (recipient_email, subject, message, status, date_sent)
                         VALUES ('" . mysqli_real_escape_string($conn, $data['reporter_email']) . "', '$subject2', '$message2', 'unread', NOW())");

    echo "<script>alert('Claim approved and both parties have been notified.');window.location='manage_claims.php';</script>";
    exit;
}

// 🚫 Reject Claim
elseif ($action === 'reject') {
    mysqli_query($conn, "UPDATE claims SET status='rejected' WHERE id=$claim_id");

    // Notify claimant only
    $subject = "Claim Rejected";
    $message = "Hi {$data['claimant_name']}, your claim for '{$data['item_name']}' has been rejected by the admin.";
    mysqli_query($conn, "INSERT INTO notifications (recipient_email, subject, message, status, date_sent)
                         VALUES ('" . mysqli_real_escape_string($conn, $data['claimant_email']) . "', '$subject', '$message', 'unread', NOW())");

    echo "<script>alert('Claim rejected and claimant has been notified.');window.location='manage_claims.php';</script>";
    exit;
}

// Invalid Action
else {
    echo "<script>alert('Invalid action!');window.location='manage_claims.php';</script>";
    exit;
}
?>
