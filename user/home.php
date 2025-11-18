<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['email'])) {
    echo "<script>alert('Please login first.');window.location='../auth/login.php';</script>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>User Dashboard - Digital Lost and Found Management System</title>
<link rel="icon" type="image/jpg" href="../images/L&F.jpg">
<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');

* { box-sizing: border-box; margin: 0; padding: 0; }

body {
  font-family: "Poppins", sans-serif;
  background: #0a0a23;
  color: white;
  display: flex;
  min-height: 100vh;
  flex-direction: row;
  transition: all 0.3s ease;
}

/* ===== SIDEBAR ===== */
.sidebar {
  width: 250px;
  background: #1e1e2f;
  padding: 20px;
  display: flex;
  flex-direction: column;
  gap: 15px;
  box-shadow: 3px 0 10px rgba(0,0,0,0.3);
  position: fixed;
  top: 0;
  left: 0;
  height: 100vh;
  overflow-y: auto;
  transition: all 0.3s ease;
  z-index: 1000;
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
.logout-btn:hover { background: #c0392b; color: #fff; }

.back-btn {
  background: #3498db;
}
.back-btn:hover { background: #2980b9; color: #fff; }

/* ===== CONTENT ===== */
.content {
  flex: 1;
  background: #f4f4f9;
  color: #000;
  padding: 10px;
  overflow-y: auto;
  transition: all 0.3s ease;
  margin-left: 250px;
  min-height: 100vh;
}

iframe {
  width: 100%;
  height: calc(100vh - 20px);
  border: none;
  border-radius: 10px;
  background: white;
}

/* ===== TOGGLE BUTTON ===== */
#menu-toggle {
  display: none;
  position: fixed;
  top: 20px;
  left: 20px;
  z-index: 1100;
  padding: 8px 12px;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  background: #43cea2;
  color: #000;
  font-weight: 600;
  box-shadow: 0 0 10px rgba(67, 206, 162, 0.4);
}

/* ===== RESPONSIVE ===== */
@media (max-width: 768px) {
  body {
    flex-direction: column;
    overflow-x: hidden;
  }

  /* Sidebar hidden by default on mobile */
  .sidebar {
    position: fixed;
    top: 0;
    left: -260px;
    width: 250px;
    height: 100vh;
    background: #1e1e2f;
    z-index: 999;
    padding: 20px;
    transition: all 0.3s ease;
  }

  .sidebar.active {
    left: 0;
  }

  /* Mobile menu button */
  #menu-toggle {
    display: block;
    position: fixed;
    top: 15px;
    left: 15px;
    z-index: 1100;
    padding: 10px 15px;
    border: none;
    border-radius: 5px;
    background: #43cea2;
    color: #000;
    font-weight: 600;
    box-shadow: 0 0 10px rgba(67, 206, 162, 0.5);
  }

  /* Content adjustment */
  .content {
    margin-left: 0;
    width: 100%;
    padding: 0; /* full width */
    min-height: 100vh;
    position: relative;
  }

  .content.dimmed::after {
    content: '';
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.4);
    z-index: 900;
  }

  iframe {
    width: 100%;
    height: 100vh; /* full viewport height */
    min-height: 100vh;
    border: none;
    border-radius: 0; /* better UX for mobile */
  }
}

</style>
</head>
<body>

<!-- Mobile Menu Toggle -->
<button id="menu-toggle">☰ Menu</button>

<div class="sidebar">
  <h2><img src="../images/L&F.jpg" alt="logo" width="15" height="15"> User Dashboard</h2>
  <button onclick="loadPage('report_item.php')">📝 Report Lost/Found</button>
  <button onclick="loadPage('my_reports.php')">📂 My Reports</button>
  <button onclick="loadPage('feedback.php')">💬 Feedback</button>
  <button onclick="loadPage('notification.php')">🔔 Notifications</button>
  <button onclick="loadPage('messenger.php')">💬 Messenger</button>
  <button onclick="loadPage('analytics.php')">📊 Analytics</button>
  <button class="back-btn" onclick="window.location='../public/index.php'">🏠 Back</button>
  <button class="logout-btn" onclick="window.location='../auth/logout.php'">🚪 Logout</button>
</div>

<div class="content">
  <iframe id="contentFrame" src="report_item.php"></iframe>
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
