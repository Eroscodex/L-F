<?php
session_start();
include("../config/db.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>About Us - Digital Lost & Found Management System</title>
  <link rel="icon" type="image/jpg" href="../images/L&F.jpg">
  <style>
    body {
      font-family: "Poppins", sans-serif;
      background: #0a0a23;
      color: #fff;
      margin: 0;
      padding: 0;
    }

    header {
      background: #1e1e2f;
      padding: 15px 30px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      border: 2px solid #00ff99;
    }

    header h1 {
      color: #00ff99;
      font-size: 24px;
    }

    a.back-btn {
      color: #00ff99;
      text-decoration: none;
      font-size: 16px;
      background: transparent;
      border: 2px solid #00ff99;
      padding: 8px 15px;
      border-radius: 10px;
      transition: 0.3s;
    }

    a.back-btn:hover {
      background: #00ff99;
      color: #0a0a23;
    }

    .container {
      max-width: 1000px;
      margin: 50px auto;
      text-align: center;
    }

    h2 {
      color: #00ff99;
      font-size: 28px;
      margin-bottom: 10px;
    }

    p {
      font-size: 16px;
      line-height: 1.6;
      color: #ccc;
      margin-bottom: 40px;
    }

    .members {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 30px;
    }

    .member-card {
      background: rgba(255, 255, 255, 0.08);
      border: 1px solid #00ff99;
      border-radius: 15px;
      width: 220px;
      padding: 20px;
      transition: 0.3s;
    }

    .member-card:hover {
      transform: scale(1.05);
      box-shadow: 0 0 15px #00ff99;
    }

    .member-card img {
      width: 100px;
      height: 100px;
      border-radius: 50%;
      object-fit: cover;
      border: 3px solid #00ff99;
    }

    .member-card h3 {
      margin: 15px 0 5px;
      color: #fff;
    }

    .member-card p {
      margin: 0;
      color: #bbb;
      font-size: 14px;
    }
    /* ===== FOOTER ===== */
    .site-footer {
      text-align: center;
      padding: 15px;
      background: #111133;
      color: #00ff99;
      font-size: 14px;
      box-shadow: 0 -2px 10px rgba(0, 255, 153, 0.2);
      border: 2px solid #00ff99;
    }

  </style>
</head>
<body>

  <header>
    <h1><img src="../images/L&F.jpg" alt="logo" width="30px" height="30px" > Digital Lost & Found About Us</h1>
    <a href="../public/index.php" class="back-btn">← Back to Dashboard</a>
  </header>

  <div class="container">
    <h2>About Our System</h2>
    <p>
      The Digital Lost and Found Management System for Legazpi City helps citizens and institutions efficiently manage 
      and track lost or found items. This system provides an organized way to report, verify, and claim items, 
      enhancing community trust and reducing loss incidents.
    </p>

    <h2>Meet Our Team</h2>
    <div class="members">
      <div class="member-card">
        <a href="https://github.com/siekok"><img src="../images/lorence.png" alt="Member 1"></a>
        <h3>Andrie Vibar</h3>
        <p>Frontend Developer</p>
      </div>

      <div class="member-card">
        <a href="https://github.com/Eroscodex"><img src="../images/karl.jpg" alt="Member 2"></a>
        <h3>Karl Nicko L. Alondra</h3>
        <p>Backend Developer</p>
      </div>

      <div class="member-card">
        <a href="https://github.com/siekok"><img src="../images/lorence.png" alt="Member 3"></a>
        <h3>Lorence Bania</h3>
        <p>UI/UX Designer</p>
      </div>

      <div class="member-card">
        <a href="https://github.com/siekok"><img src="../images/lorence.png" alt="Member 4"></a>
        <h3>Ivan Cris Mandane</h3>
        <p>Database Manager</p>
      </div>
    </div>
  </div>

  <div class="site-footer">
  &copy; <?= date("Y") ?> Digital Lost & Found — Legazpi City
</div>
</body>
</html>
