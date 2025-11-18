<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['email'])) {
    echo "<script>alert('Access denied. Please login first.');window.location='../auth/login.php';</script>";
    exit();
}

$user = $_SESSION['name'];
$email = $_SESSION['email'];

// Total reports by the user
$report_query = mysqli_query($conn, "SELECT COUNT(*) AS total_reports FROM items WHERE reporter_name='$user'");
$report_count = mysqli_fetch_assoc($report_query)['total_reports'] ?? 0;

// Total feedback by the user
$feedback_query = mysqli_query($conn, "SELECT COUNT(*) AS total_feedback FROM feedback WHERE user='$user'");
$feedback_count = mysqli_fetch_assoc($feedback_query)['total_feedback'] ?? 0;

// Total notifications received by the user
$notif_query = mysqli_query($conn, "SELECT COUNT(*) AS total_notif FROM notifications WHERE recipient_email='$email'");
$notif_count = mysqli_fetch_assoc($notif_query)['total_notif'] ?? 0;

// Messenger analytics (✅ fixed column names)
$msg_sent_query = mysqli_query($conn, "SELECT COUNT(*) AS sent_count FROM messages WHERE sender_email='$email'");
$msg_sent = mysqli_fetch_assoc($msg_sent_query)['sent_count'] ?? 0;

$msg_received_query = mysqli_query($conn, "SELECT COUNT(*) AS recv_count FROM messages WHERE receiver_email='$email'");
$msg_received = mysqli_fetch_assoc($msg_received_query)['recv_count'] ?? 0;

// Found vs Lost item ratio (✅ use item_type)
$found_query = mysqli_query($conn, "SELECT COUNT(*) AS found_count FROM items WHERE LOWER(TRIM(item_type))='found'");
$found_count = mysqli_fetch_assoc($found_query)['found_count'] ?? 0;

$lost_query = mysqli_query($conn, "SELECT COUNT(*) AS lost_count FROM items WHERE LOWER(TRIM(item_type))='lost'");
$lost_count = mysqli_fetch_assoc($lost_query)['lost_count'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Analytics - Digital Lost & Found</title>
<style>
 @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap');

body {
    font-family: 'Poppins', sans-serif;
    background: #0a0a23;
    margin: 0;
    padding: 30px;
    color: #fff;
}

h2 {
    text-align: center;
    color: #00ff99;
    margin-bottom: 40px;
    text-shadow: 0 0 10px rgba(0, 255, 153, 0.6);
}

.stats-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
    gap: 25px;
    justify-content: center;
    align-items: center;
    margin-bottom: 50px;
}

.card {
    background: rgba(255, 249, 249, 0.05);
    border: 1px solid rgba(0, 255, 153, 0.3);
    border-radius: 14px;
    padding: 25px;
    text-align: center;
    box-shadow: 0 0 20px rgba(0, 255, 153, 0.15);
    transition: 0.3s ease;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 0 25px rgba(0, 255, 153, 0.4);
}

.card h3 {
    color: #00ff99;
    font-size: 24px;
    margin-bottom: 10px;
    text-shadow: 0 0 10px rgba(0, 255, 153, 0.5);
}

.card p {
    font-size: 16px;
    color: #d3d3d3;
}

.chart-section {
    background: rgba(255, 255, 255, 0.05);
    border-radius: 14px;
    padding: 30px;
    border: 1px solid rgba(0, 255, 153, 0.3);
    box-shadow: 0 0 20px rgba(0, 255, 153, 0.15);
    margin-bottom: 40px;
}

canvas {
    display: block;
    max-width: 600px;
    margin: 0 auto;
    background: transparent;
}

</style>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<h2>📊 User Analytics Overview</h2>

<div class="stats-container">
    <div class="card">
        <h3><?php echo $report_count; ?></h3>
        <p>Total Reports</p>
    </div>
    <div class="card">
        <h3><?php echo $feedback_count; ?></h3>
        <p>Feedback Submitted</p>
    </div>
    <div class="card">
        <h3><?php echo $notif_count; ?></h3>
        <p>Notifications Received</p>
    </div>
    <div class="card">
        <h3><?php echo $msg_sent; ?></h3>
        <p>Messages Sent</p>
    </div>
    <div class="card">
        <h3><?php echo $found_count; ?></h3>
        <p>Found Items</p>
    </div>
    <div class="card">
        <h3><?php echo $lost_count; ?></h3>
        <p>Lost Items</p>
    </div>
</div>

<!-- Messenger chart -->
<div class="chart-section">
    <h2>💬 Messenger Activity</h2>
    <canvas id="msgChart"></canvas>
</div>

<!-- Lost vs Found chart -->
<div class="chart-section">
    <h2>📈 Lost vs Found Items</h2>
    <canvas id="reportChart"></canvas>
</div>

<script>
// Messenger Chart
const ctx2 = document.getElementById('msgChart');
new Chart(ctx2, {
    type: 'bar',
    data: {
        labels: ['Messages Sent', 'Messages Received'],
        datasets: [{
            label: 'Messages',
            data: [<?php echo $msg_sent; ?>, <?php echo $msg_received; ?>],
            backgroundColor: ['#3498db', '#2ecc71'],
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false },
        },
        scales: {
            y: { beginAtZero: true }
        }
    }
});

// Lost vs Found Chart
const ctx = document.getElementById('reportChart');
new Chart(ctx, {
    type: 'doughnut',
    data: {
        labels: ['Lost Items', 'Found Items'],
        datasets: [{
            label: 'Items Count',
            data: [<?php echo $lost_count; ?>, <?php echo $found_count; ?>],
            backgroundColor: ['#e74c3c', '#43cea2'],
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom',
                labels: { color: '#333', font: { size: 14 } }
            }
        }
    }
});
</script>

</body>
</html>
