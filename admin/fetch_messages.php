<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['email']) || empty($_GET['receiver'])) exit;

$sender = $_SESSION['email'];
$receiver = $_GET['receiver'];

$sql = "SELECT * FROM messages 
        WHERE (sender_email=? AND receiver_email=?) 
           OR (sender_email=? AND receiver_email=?) 
        ORDER BY date_sent ASC";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ssss", $sender, $receiver, $receiver, $sender);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

while ($row = mysqli_fetch_assoc($result)) {
    $cls = ($row['sender_email'] == $sender) ? 'sent' : 'received';
    echo "<div class='msg $cls'>";
    if (!empty($row['message'])) echo nl2br(htmlspecialchars($row['message']));
    if (!empty($row['image'])) echo "<br><img src='../" . htmlspecialchars($row['image']) . "' style='max-width:100px;'>";
    echo "<div class='timestamp'>" . date("g:i A", strtotime($row['date_sent'])) . "</div></div>";
}
?>
