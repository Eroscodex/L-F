<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    echo "<script>alert('Admins only!');window.location='../auth/login.php';</script>";
    exit();
}

$email = $_SESSION['email'] ?? '';

$total_items = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM items"))['total'] ?? 0;
$total_users = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM users"))['total'] ?? 0;
$total_feedback = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM feedback"))['total'] ?? 0;
$total_claims = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM claims"))['total'] ?? 0;

$msg_sent = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM messages"))['total'] ?? 0;
$msg_received = $msg_sent;

$found_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM items WHERE LOWER(TRIM(item_type))='found'"))['total'] ?? 0;
$lost_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM items WHERE LOWER(TRIM(item_type))='lost'"))['total'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Analytics - Digital Lost & Found</title>
<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap');

body {
  font-family: 'Poppins', sans-serif;
  background: #0a0a23;
  margin: 0;
  padding: 20px;
  color: #ffffff;
  overflow-y: auto;
  min-height: 100vh;
  box-sizing: border-box;
}

h2 {
  text-align: center;
  color: #00ff99;
  margin-bottom: 30px;
  text-shadow: 0 0 10px rgba(0, 255, 153, 0.6);
}

.stats-container {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
  gap: 25px;
  margin-bottom: 40px;
}

.card {
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(0, 255, 153, 0.4);
  border-radius: 14px;
  padding: 20px;
  text-align: center;
  box-shadow: 0 0 15px rgba(0, 255, 153, 0.1);
  transition: 0.3s ease;
}

.card:hover {
  transform: translateY(-5px);
  box-shadow: 0 0 20px rgba(0, 255, 153, 0.4);
}

.card h3 {
  color: #00ff99;
  font-size: 26px;
  margin-bottom: 8px;
}

.card p {
  font-size: 15px;
  color: #cfcfcf;
}

.chart-section {
  background: rgba(255, 255, 255, 0.05);
  border-radius: 14px;
  padding: 20px;
  border: 1px solid rgba(0, 255, 153, 0.3);
  box-shadow: 0 0 15px rgba(0, 255, 153, 0.15);
  margin-bottom: 40px;
  text-align: center;
}

canvas {
  display: block;
  max-width: 100%;
  height: auto !important;
  margin: 0 auto;
}

hr {
  border: none;
  height: 2px;
  background: linear-gradient(90deg, transparent, #00ff99, transparent);
  margin: 30px auto;
  width: 80%;
}

/* Responsive adjustments */
@media (max-width: 768px) {
  body { padding: 15px; }
  .card h3 { font-size: 22px; }
  .chart-section { padding: 15px; }
}
</style>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<h2>📊 Admin Analytics Overview</h2>

<div class="stats-container">
  <div class="card">
      <h3><?php echo $total_items; ?></h3>
      <p>Total Items Reported</p>
  </div>
  <div class="card">
      <h3><?php echo $total_users; ?></h3>
      <p>Registered Users</p>
  </div>
  <div class="card">
      <h3><?php echo $total_claims; ?></h3>
      <p>Total Claims</p>
  </div>
  <div class="card">
      <h3><?php echo $total_feedback; ?></h3>
      <p>Feedback Submitted</p>
  </div>
  <div class="card">
      <h3><?php echo $msg_sent; ?></h3>
      <p>Total Messages</p>
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

<hr>

<div class="chart-section">
  <h2>💬 Messenger Activity</h2>
  <canvas id="msgChart"></canvas>
</div>

<div class="chart-section">
  <h2>📈 Lost vs Found Item Ratio</h2>
  <canvas id="adminChart"></canvas>
</div>

<script>
  
const ctx2 = document.getElementById('msgChart');
new Chart(ctx2, {
  type: 'bar',
  data: {
    labels: ['Messages Sent', 'Messages Received'],
    datasets: [{
      label: 'Messages',
      data: [<?php echo $msg_sent; ?>, <?php echo $msg_received; ?>],
      backgroundColor: ['#0084ffff', '#00cc7a'],
      borderColor: '#00ff99',
      borderWidth: 1,
      borderRadius: 8
    }]
  },
  options: {
    responsive: true,
    plugins: {
      legend: { display: false },
      tooltip: {
        backgroundColor: '#111',
        titleColor: '#00ff99',
        bodyColor: '#fff'
      }
    },
    scales: {
      y: {
        beginAtZero: true,
        ticks: { color: '#00ff99' },
        grid: { color: 'rgba(0,255,153,0.2)' }
      },
      x: {
        ticks: { color: '#00ff99' },
        grid: { color: 'rgba(0,255,153,0.1)' }
      }
    }
  }
});

const ctx = document.getElementById('adminChart');
new Chart(ctx, {
  type: 'doughnut',
  data: {
    labels: ['Lost Items', 'Found Items'],
    datasets: [{
      data: [<?php echo $lost_count; ?>, <?php echo $found_count; ?>],
      backgroundColor: ['#ff4c4c', '#00ff99'],
      borderColor: '#0a0a23',
      borderWidth: 2
    }]
  },
  options: {
    responsive: true,
    cutout: '70%',
    plugins: {
      legend: {
        position: 'bottom',
        labels: { color: '#00ff99', font: { size: 14 } }
      }
    }
  }
});
</script>

</body>
</html>
