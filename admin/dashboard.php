<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    echo "<script>alert('Admins only!');window.location='../auth/login.php';</script>";
    exit();
}

$total_items = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM items"))['total'];
$total_users = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM users"))['total'];
$total_claims = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM claims"))['total'];
$total_feedback = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM feedback"))['total'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard - Digital Lost & Found Management System</title>
<link rel="icon" type="image/jpg" href="../images/L&F.jpg">
<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');

* { box-sizing: border-box; margin:0; padding:0; }

body {
  font-family: "Poppins", sans-serif;
  background: #0a0a23;
  color: white;
  display: flex;
  min-height: 100vh;
  flex-direction: row;
  overflow: hidden;
  transition: all 0.3s ease;
}

/* Sidebar */
.sidebar {
  width: 250px;
  background: #1e1e2f;
  padding: 20px;
  display: flex;
  flex-direction: column;
  gap: 15px;
  box-shadow: 3px 0 10px rgba(0,0,0,0.3);
  position: sticky;
  top: 0;
  height: 100vh;
  transition: all 0.3s ease;
}

.sidebar h2 {
  color: #43cea2;
  text-align: center;
  margin-bottom: 25px;
  font-size: 22px;
}

.sidebar button {
  background: #2d2d44;
  color: #fff;
  border: none;
  padding: 12px;
  border-radius: 8px;
  text-align: left;
  cursor: pointer;
  transition: all 0.3s ease;
  font-size: 15px;
}

.sidebar button:hover {
  background: #43cea2;
  color: #000;
  font-weight: 600;
  transform: scale(1.02);
}

.logout-btn {
  margin-top: auto;
  background: #e74c3c;
}
.logout-btn:hover { background: #c0392b; }

.back-btn {
  background: #3498db;
}
.back-btn:hover { background: #2980b9; }

/* Content */
.content {
  flex: 1;
  background: #f4f4f9;
  color: black;
  padding: 10px;
  overflow-y: auto;
  transition: all 0.3s ease;
}

iframe {
  width: 100%;
  height: calc(100vh - 20px);
  border: none;
  border-radius: 10px;
  background: white;
}

/* Mobile toggle button */
#menu-toggle {
  display: none;
  position: fixed;
  top: 20px;
  left: 20px;
  z-index: 1000;
  padding: 8px 12px;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  background: #43cea2;
  color: #000;
}

/* Responsive */
@media (max-width: 768px) {
  body { flex-direction: column; }

  /* Sidebar hidden by default on mobile */
  .sidebar {
    position: fixed;
    top: 0;
    left: -260px;
    width: 250px;
    height: 100vh;
    z-index: 999;
    flex-direction: column;
    padding: 20px;
  }
  .sidebar.active { left: 0; }

  #menu-toggle { display: block; }

  .content.dimmed::after {
    content: '';
    position: fixed;
    top:0;
    left:0;
    width:100%;
    height:100%;
    background: rgba(0,0,0,0.4);
    z-index: 900;
  }
}

/* Tablet / small laptop */
@media (max-width: 992px) {
  .sidebar { width: 200px; }
  iframe { height: calc(100vh - 20px); }
}

/* Laptop / desktop */
@media (max-width: 1200px) {
  .sidebar { width: 220px; }
  .sidebar h2 { font-size: 20px; }
  .sidebar button { font-size: 14px; padding: 10px; }
}
</style>
</head>
<body>

<!-- Mobile Menu Toggle -->
<button id="menu-toggle">☰ Menu</button>

<div class="sidebar">
  <h2><img src="../images/L&F.jpg" alt="logo" width="20" height="20"> Admin Dashboard</h2>
  <button onclick="loadPage('analytics.php')">📊 Dashboard Overview</button>
  <button onclick="loadPage('manage_items.php')">🗂 Manage Items</button>
  <button onclick="loadPage('manage_users.php')">👥 Manage Users</button>
  <button onclick="loadPage('manage_claims.php')">📑 Manage Claims</button>
  <button onclick="loadPage('manage_feedback.php')">💬Manage Feedback</button>
  <button onclick="loadPage('messenger.php')">💬 Messenger</button>
  <button onclick="window.location='../public/index.php'" class="back-btn">🏠 Back</button>
  <button class="logout-btn" onclick="window.location='../auth/logout.php'">🚪 Logout</button>
</div>

<div class="content">
  <iframe id="contentFrame" src="analytics.php"></iframe>
</div>

<script>
function loadPage(page) {
  document.getElementById('contentFrame').src = page;
}

// Sidebar toggle for mobile
const menuToggle = document.getElementById('menu-toggle');
const sidebar = document.querySelector('.sidebar');
const content = document.querySelector('.content');

menuToggle.addEventListener('click', () => {
  sidebar.classList.toggle('active');
  content.classList.toggle('dimmed');
});

// Hide sidebar when clicking outside
content.addEventListener('click', () => {
  if(sidebar.classList.contains('active')){
    sidebar.classList.remove('active');
    content.classList.remove('dimmed');
  }
});
</script>
</body>
</html>
