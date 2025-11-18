// Approve item
if (isset($_GET['approve'])) {
    $id = intval($_GET['approve']);

    // Fetch reporter email
    $item = mysqli_fetch_assoc(mysqli_query($conn, "SELECT reporter_email, item_name FROM items WHERE id=$id"));
    $recipient_email = $item['reporter_email'];
    $item_name = $item['item_name'];

    // Update item status
    mysqli_query($conn, "UPDATE items SET status='approved' WHERE id=$id");

    // Send notification if reporter email exists
    if (!empty($recipient_email)) {
        $subject = "Your item has been approved!";
        $message = "Your reported item '$item_name' has been approved by the admin.";
        mysqli_query($conn, "INSERT INTO notifications (recipient_email, subject, message, status, date_sent)
                             VALUES ('$recipient_email', '$subject', '$message', 'unread', NOW())");
    }

    echo "<script>alert('Item approved successfully! Notification sent.');window.location='manage_items.php';</script>";
    exit;
}
