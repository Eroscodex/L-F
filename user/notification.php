<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['email'])) {
    echo "<script>alert('Access denied! Please login first.');window.location='../auth/login.php';</script>";
    exit;
}

$user_email = $_SESSION['email'];
$user_name = $_SESSION['name'];

$query = "SELECT * FROM notifications WHERE recipient_email='$user_email' ORDER BY date_sent DESC";
$result = mysqli_query($conn, $query);
$counter = 1;
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Notifications</title>
<style>
@import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap');

body {
    font-family: 'Roboto', sans-serif;
    background: #0a0a23;
    color: #fff;
    margin: 0;
    padding: 10px; /* smaller padding for mobile */
    display: flex;
    justify-content: center;
    align-items: flex-start;
    min-height: 100vh;
}

.container {
    background: rgba(10, 10, 35, 0.95);
    margin-top: 20px;
    padding: 20px; /* reduced for mobile */
    border-radius: 12px;
    width: 100%;
    max-width: 100%; /* full width on mobile */
    box-shadow: 0 0 25px rgba(0, 255, 153, 0.2);
    border: 1px solid rgba(0, 255, 153, 0.3);
    overflow-x: auto; /* allow horizontal scroll if table is wide */
}

h2 {
    text-align: center;
    color: #00ff99;
    text-shadow: 0 0 10px #00ff99;
    margin-bottom: 15px;
    font-size: 1.5rem;
}

table {
    width: 100%;
    border-collapse: collapse;
    background: rgba(255,255,255,0.05);
    min-width: 300px; /* prevent table from collapsing */
}

th, td {
    padding: 10px 8px;
    border-bottom: 1px solid rgba(255,255,255,0.1);
    text-align: left;
    font-size: 13px;
    white-space: nowrap; /* prevent text wrapping */
}

th {
    background: #00ff99;
    color: #0a0a23;
    text-transform: uppercase;
}

.unread {
    background-color: rgba(255, 255, 0, 0.1);
    font-weight: bold;
}

.status-read {
    color: #00ff99;
    font-weight: 600;
}

.status-unread {
    color: #ffcc00;
    font-weight: 600;
}

.btn {
    padding: 8px 16px;
    border-radius: 8px;
    text-decoration: none;
    font-size: 13px;
    font-weight: 600;
    display: inline-block;
    transition: 0.3s;
}

.btn-read {
    background-color: #00ff99;
    color: #0a0a23;
    border: 1px solid #00ff99;
}

.btn-unread {
    background-color: #ff4d4d;
    color: white;
    border: 1px solid #ff4d4d;
}

.btn:hover {
    opacity: 0.9;
    transform: scale(1.05);
}

.no-msg {
    text-align: center;
    color: #bbb;
    font-size: 14px;
    margin-top: 10px;
}

/* ===== MOBILE RESPONSIVE ===== */
@media (max-width: 768px) {
    body {
        padding: 5px;
    }

    .container {
        padding: 15px;
        margin-top: 15px;
    }

    h2 {
        font-size: 1.3rem;
    }

    th, td {
        font-size: 12px;
        padding: 8px 5px;
    }

    .btn {
        font-size: 12px;
        padding: 6px 12px;
    }

    table {
        font-size: 12px;
    }
}

</style>
</head>
<body>

<div class="container">
    <h2>📩 Notifications for <?php echo htmlspecialchars($user_name); ?></h2>

    <?php if (mysqli_num_rows($result) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Subject</th>
                    <th>Message</th>
                    <th>Status</th>
                    <th>Date Sent</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)):
                $status = strtolower($row['status']);
                if ($status == 'pending') $status = 'unread'; // convert old data
            ?>
                <tr class="<?php echo ($status == 'unread') ? 'unread' : ''; ?>">
                    <td><?php echo $counter++; ?></td>
                    <td><?php echo htmlspecialchars($row['subject']); ?></td>
                    <td><?php echo nl2br(htmlspecialchars($row['message'])); ?></td>
                    <td class="status-<?php echo $status; ?>"><?php echo ucfirst($status); ?></td>
                    <td><?php echo date("M d, Y h:i A", strtotime($row['date_sent'])); ?></td>
                    <td>
                        <?php if ($status == 'unread') { ?>
                            <a href="mark_notification.php?id=<?php echo $row['id']; ?>&action=read"
                               class="btn btn-read"
                               onclick="return confirm('Mark as read?');">Mark as Read</a>
                        <?php } else { ?>
                            <a href="mark_notification.php?id=<?php echo $row['id']; ?>&action=unread"
                               class="btn btn-unread"
                               onclick="return confirm('Mark as unread?');">Mark as Unread</a>
                        <?php } ?>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="no-msg">No notifications found.</p>
    <?php endif; ?>
</div>

</body>
</html>
