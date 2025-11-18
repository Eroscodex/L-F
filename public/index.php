<?php
session_start();
include("../config/db.php");

// Handle sessions / roles
$is_logged_in = isset($_SESSION['email']);
$is_admin = ($is_logged_in && isset($_SESSION['role']) && $_SESSION['role'] === 'admin');

// Fetch lost/found items
$items_result = mysqli_query($conn, "SELECT * FROM items ORDER BY date_reported DESC");
if (!$items_result) { die("Items query failed: " . mysqli_error($conn)); }

// Fetch feedback
$feedback_result = mysqli_query($conn, "SELECT * FROM feedback ORDER BY date_submitted DESC");
if (!$feedback_result) { die("Feedback query failed: " . mysqli_error($conn)); }

$counter = 1;
$fb_counter = 1;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>Digital Lost & Found Management System - Legazpi City</title>
<link rel="icon" type="image/jpg" href="../images/L&F.jpg">
<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');

* {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}

/* ===== GLOBAL ===== */
body {
  font-family: "Poppins", sans-serif;
  background: #0a0a23;
  color: #ffffff;
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  overflow-x: hidden;
  transition: all 0.3s ease;
}

/* ===== HEADER / NAVBAR ===== */
header.navbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: #111133;
  padding: 14px 24px;
  flex-wrap: wrap;
  box-shadow: 0 2px 12px rgba(0, 255, 153, 0.2);
  border-bottom: 2px solid #00ff99;
  position: sticky;
  top: 0;
  z-index: 100;
}

.brand-title {
  font-weight: 600;
  font-size: 22px;
  color: #00ff99;
  text-shadow: 0 0 10px #00ff99;
  letter-spacing: 0.5px;
}

.nav-links {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  justify-content: center;
}

.nav-links a {
  color: #00ff99;
  margin: 8px 12px;
  text-decoration: none;
  font-size: 15px;
  background: transparent;
  border: 2px solid #00ff99;
  padding: 8px 15px;
  border-radius: 10px;
  transition: 0.3s;
  font-weight: 500;
  box-shadow: 0 0 8px rgba(0, 255, 153, 0.2);
}

.nav-links a:hover {
  background: #00ff99;
  color: #0a0a23;
  box-shadow: 0 0 15px #00ff99;
}

/* ===== MAIN CONTAINER ===== */
.container {
  flex: 1;
  padding: 30px 25px;
  background: #0f0f2e;
  color: #e0e0e0;
  box-shadow: inset 0 0 25px rgba(0, 255, 153, 0.1);
  border-radius: 12px 12px 0 0;
  overflow-y: auto;
}

/* ===== TITLES ===== */
h3 {
  color: #00ff99;
  border-left: 5px solid #00ff99;
  padding-left: 10px;
  font-size: 1.4rem;
  text-shadow: 0 0 6px #00ff99;
  margin-bottom: 18px;
  font-weight: 600;
}

/* ===== TABLE WRAPPER ===== */
.table-wrap {
  overflow-x: auto;
  background: #1a1a3a;
  border-radius: 10px;
  box-shadow: 0 0 25px rgba(0, 255, 153, 0.2);
  margin-bottom: 30px;
  width: 100%;
  scrollbar-width: thin;
  scrollbar-color: #00ff99 #0a0a23;
}

.table-wrap::-webkit-scrollbar {
  height: 8px;
}
.table-wrap::-webkit-scrollbar-thumb {
  background: #00ff99;
  border-radius: 4px;
}

/* ===== TABLE ===== */
table {
  width: 100%;
  border-collapse: collapse;
  font-size: 0.95rem;
  color: #fff;
  min-width: 700px;
}

th, td {
  padding: 12px 10px;
  border-bottom: 1px solid rgba(0, 255, 153, 0.2);
  text-align: left;
  vertical-align: middle;
}

th {
  background: #00ff99;
  color: #0a0a23;
  text-shadow: none;
  position: sticky;
  top: 0;
  z-index: 10;
  font-weight: 600;
}

tr:hover {
  background: rgba(0, 255, 153, 0.08);
  transition: 0.3s ease;
}

/* ===== STATUS TAGS ===== */
.status-claimed {
  background: #e53e3e;
  color: white;
  padding: 4px 10px;
  border-radius: 6px;
  font-weight: 600;
}

.status-approved {
  background: #00ff99;
  color: #0a0a23;
  padding: 4px 10px;
  border-radius: 6px;
  font-weight: 600;
}

.status-pending {
  background: #facc15;
  color: #0a0a23;
  padding: 4px 10px;
  border-radius: 6px;
  font-weight: 600;
}

/* ===== ITEM IMAGE ===== */
img.item-img {
  width: 55px;
  height: 55px;
  border-radius: 6px;
  object-fit: cover;
  border: 2px solid #00ff99;
  box-shadow: 0 0 10px rgba(0, 255, 153, 0.3);
}

/* ===== BUTTONS ===== */
.btn {
  background: #00ff99;
  color: #0a0a23;
  border: none;
  padding: 7px 14px;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 600;
  font-size: 0.9rem;
  transition: all 0.3s ease;
  box-shadow: 0 0 12px rgba(0, 255, 153, 0.5);
}

.btn:hover {
  background: transparent;
  color: #00ff99;
  border: 1px solid #00ff99;
  box-shadow: 0 0 20px #00ff99;
}

/* ===== FOOTER ===== */
.site-footer {
  text-align: center;
  padding: 15px;
  background: #111133;
  color: #00ff99;
  font-size: 14px;
  box-shadow: 0 -2px 10px rgba(0, 255, 153, 0.2);
  border-top: 2px solid #00ff99;
}

/* ===== RESPONSIVE ADJUSTMENTS ===== */

/* Tablets & small laptops */
@media (max-width: 992px) {
  .container {
    padding: 20px 15px;
  }

  h3 {
    font-size: 1.2rem;
  }

  table, th, td {
    font-size: 0.9rem;
  }

  img.item-img {
    width: 45px;
    height: 45px;
  }
}

/* Mobile devices */
@media (max-width: 768px) {
  header.navbar {
    flex-direction: column;
    align-items: flex-start;
    text-align: left;
    padding: 12px 18px;
  }

  .brand-title {
    font-size: 1.1rem;
    margin-bottom: 8px;
  }

  .nav-links {
    width: 100%;
    flex-direction: column;
    align-items: flex-start;
  }

  .nav-links a {
    margin: 6px 0;
    display: block;
    width: 100%;
  }

  .table-wrap {
    border-radius: 5px;
    overflow-x: scroll;
  }

  table {
    width: 850px; /* keeps layout consistent on small screens */
  }

  .btn {
    padding: 5px 10px;
    font-size: 0.85rem;
  }
}

/* Small mobile phones */
@media (max-width: 480px) {
  h3 {
    font-size: 1rem;
  }

  .btn {
    display: block;
    margin-bottom: 6px;
    width: 100%;
  }

  img.item-img {
    width: 40px;
    height: 40px;
  }

  .site-footer {
    font-size: 12px;
    padding: 10px;
  }
}
</style>

</head>

<body>
<header class="navbar">
  <div class="brand-title"><img src="../images/L&F.jpg" alt="logo" width="30px" height="30px" > Digital Lost &amp; Found Dashboard</div>
  <nav class="nav-links">
    <a href="../index.php">Home</a>
    <?php if($is_logged_in): ?>
      <?php if($is_admin): ?>
        <a href="../admin/dashboard.php">Admin Panel</a>
      <?php else: ?>
        <a href="../user/home.php">User Panel</a>
      <?php endif; ?>
      <a href="../public/about.php">About</a>
      <a href="../public/profile.php">Profile</a>
      <a href="../auth/logout.php">Logout</a>
    <?php else: ?>
      <a href="../public/about.php">About</a>
      
      <a href="../auth/login.php">Login</a>
      <a href="../auth/register.php">Register</a>
    <?php endif; ?>
  </nav>
</header>

<div class="container">
  <h3>📦 Lost &amp; Found Items (Verify / Manage Claims)</h3>
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Item</th>
          <th>Description</th>
          <th>Photo</th>
          <th>Type</th>
          <th>Status</th>
          <th>Date Reported</th>
          <th>Claimed By</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php while($item = mysqli_fetch_assoc($items_result)): 
          $status = strtolower($item['status'] ?? '');
          $claimed_by = ($status === 'claimed' && !empty($item['claimed_by'])) ? htmlspecialchars($item['claimed_by']) : 'None';
          $photo = !empty($item['photo']) ? "../uploads/" . htmlspecialchars($item['photo']) : "";
        ?>
        <tr>
          <td><?= $counter++ ?></td>
          <td><b><?= htmlspecialchars($item['item_name']) ?></b></td>
          <td><?= htmlspecialchars($item['description']) ?></td>
          <td>
            <?php if($photo): ?>
              <img class="item-img" src="<?= $photo ?>" alt="<?= htmlspecialchars($item['item_name']) ?>">
            <?php else: ?>
              <span style="color:gray;">N/A</span>
            <?php endif; ?>
          </td>
          <td><?= ucfirst(htmlspecialchars($item['item_type'] ?? '')) ?></td>
          <td>
            <?php
              $map = ['claimed'=>'status-claimed','approved'=>'status-approved','pending'=>'status-pending'];
              echo isset($map[$status]) ? "<span class='{$map[$status]}'>".ucfirst($status)."</span>" : ucfirst($status);
            ?>
          </td>
          <td><?= date("F j, Y", strtotime($item['date_reported'])) ?></td>
          <td><?= $claimed_by ?></td>
          <td>
            <?php if ($is_admin): ?>
              <a href="../admin/dashboard.php?id=<?= $item['id'] ?>" class="btn">Manage</a>
            <?php elseif($is_logged_in): ?>
              <form action="../user/request_claim.php" method="POST" style="display:inline">
                <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                <button type="submit" class="btn">Request Claim</button>
              </form>
            <?php else: ?>
              <a href="../auth/login.php" class="btn">Login</a>
            <?php endif; ?>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>

  <h3>💬 User Feedback</h3>
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>User</th>
          <th>Rating</th>
          <th>Comment</th>
          <th>Date Submitted</th>
        </tr>
      </thead>
      <tbody>
        <?php while($fb = mysqli_fetch_assoc($feedback_result)): ?>
        <tr>
          <td><?= $fb_counter++ ?></td>
          <td><?= htmlspecialchars($fb['user']) ?></td>
          <td><?= str_repeat("⭐", max(0, intval($fb['rating']))) ?></td>
          <td><?= nl2br(htmlspecialchars($fb['comment'])) ?></td>
          <td><?= date("F j, Y", strtotime($fb['date_submitted'])) ?></td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>

<div class="site-footer">
  &copy; <?= date("Y") ?> Digital Lost & Found — Legazpi City
</div>
</body>
</html>
