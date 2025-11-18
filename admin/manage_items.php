<?php
session_start();
include("../config/db.php");

// Admin-only access
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    echo "<script>alert('Access denied!');window.location='../auth/login.php';</script>";
    exit;
}

// APPROVE ITEM
if (isset($_GET['approve'])) {
    $id = intval($_GET['approve']);

    // Fetch reporter info
    $query = mysqli_query($conn, "SELECT reporter_name, item_name, reporter_email FROM items WHERE id=$id");
    $item = mysqli_fetch_assoc($query);

    if ($item) {
        // Escape strings to prevent SQL syntax errors
        $reporter_name = mysqli_real_escape_string($conn, $item['reporter_name']);
        $reporter_email = mysqli_real_escape_string($conn, $item['reporter_email']);
        $item_name = mysqli_real_escape_string($conn, $item['item_name']);

        // Update item status
        mysqli_query($conn, "UPDATE items SET status='approved' WHERE id=$id");

        // Prepare notification message
        $subject = mysqli_real_escape_string($conn, "Item Approved");
        $message = mysqli_real_escape_string($conn, "Hello $reporter_name, your reported item \"$item_name\" has been approved by the admin.");

        // Insert into notifications
        $insert_query = "INSERT INTO notifications (recipient_email, subject, message, status, date_sent)
                         VALUES ('$reporter_email', '$subject', '$message', 'unread', NOW())";
        mysqli_query($conn, $insert_query);

        echo "<script>alert('Item approved successfully!');window.location='manage_items.php';</script>";
    }
    exit;
}

// REJECT ITEM
if (isset($_GET['reject'])) {
    $id = intval($_GET['reject']);

    $query = mysqli_query($conn, "SELECT reporter_name, item_name, reporter_email FROM items WHERE id=$id");
    $item = mysqli_fetch_assoc($query);

    if ($item) {
        $reporter_name = mysqli_real_escape_string($conn, $item['reporter_name']);
        $reporter_email = mysqli_real_escape_string($conn, $item['reporter_email']);
        $item_name = mysqli_real_escape_string($conn, $item['item_name']);

        mysqli_query($conn, "UPDATE items SET status='rejected' WHERE id=$id");

        $subject = mysqli_real_escape_string($conn, "Item Rejected");
        $message = mysqli_real_escape_string($conn, "Hello $reporter_name, your reported item \"$item_name\" has been rejected by the admin.");

        $insert_query = "INSERT INTO notifications (recipient_email, subject, message, status, date_sent)
                         VALUES ('$reporter_email', '$subject', '$message', 'unread', NOW())";
        mysqli_query($conn, $insert_query);

        echo "<script>alert('Item rejected successfully!');window.location='manage_items.php';</script>";
    }
    exit;
}

// DELETE ITEM
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);

    $photo_query = mysqli_query($conn, "SELECT photo FROM items WHERE id=$id");
    if ($photo_query && mysqli_num_rows($photo_query) > 0) {
        $photo = mysqli_fetch_assoc($photo_query)['photo'];
        if (!empty($photo) && file_exists("../uploads/$photo")) {
            unlink("../uploads/$photo");
        }
    }

    mysqli_query($conn, "DELETE FROM items WHERE id=$id");
    echo "<script>alert('Item deleted successfully!');window.location='manage_items.php';</script>";
    exit;
}

// FETCH ITEMS
$result = mysqli_query($conn, "SELECT * FROM items ORDER BY id DESC");
$counter = 1;
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Items - Admin</title>
<style>
body {
    margin: 0;
    font-family: 'Poppins', sans-serif;
    background: #0a0a23;
    color: #e0e0e0;
    min-height: 100vh;
    overflow-y: auto; /* Ensures scroll inside iframe */
    padding: 20px;
    box-sizing: border-box;
}

/* Sidebar is not inside iframe, so remove its fixed layout here */
.sidebar {
    display: none; /* Sidebar is handled by main dashboard */
}

/* Main content */
.main-content {
    width: 100%;
    max-width: 1200px;
    margin: 0 auto;
}

/* Header */
h2 {
    text-align: center;
    color: #00ff99;
    margin-bottom: 25px;
    font-weight: 600;
    text-shadow: 0 0 12px #00ff99;
    font-size: 24px;
}

/* Container */
.container {
    width: 100%;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 12px;
    box-shadow: 0 0 25px rgba(0, 255, 153, 0.15);
    padding: 20px;
    backdrop-filter: blur(8px);
    overflow-x: auto; /* Enables horizontal scroll for small devices */
}

/* Table */
table {
    width: 100%;
    border-collapse: collapse;
    border-radius: 10px;
    overflow: hidden;
    background: rgba(255, 255, 255, 0.06);
    min-width: 700px; /* keeps columns aligned on small screens */
}

th, td {
    padding: 12px 10px;
    text-align: center;
    font-size: 13px;
    border-bottom: 1px solid rgba(0, 255, 153, 0.12);
}

th {
    background: rgba(0, 255, 153, 0.15);
    color: #00ff99;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    text-shadow: 0 0 6px #00ff99;
}

td {
    color: #d8d8d8;
}

tr:hover {
    background-color: rgba(0, 255, 153, 0.08);
    transition: 0.3s ease;
}

/* Image inside table */
img {
    border-radius: 6px;
    max-width: 70px;
    height: auto;
    box-shadow: 0 0 8px rgba(0, 255, 153, 0.4);
}

/* Action Buttons */
.action-btn {
    display: inline-block;
    padding: 6px 10px;
    border-radius: 6px;
    text-decoration: none;
    color: #0a0a23;
    font-weight: 600;
    transition: all 0.3s ease;
    margin: 3px;
    font-size: 12px;
    box-shadow: 0 0 8px rgba(0, 255, 153, 0.4);
    cursor: pointer;
    text-align: center;
}

.approve {
    background: #00ff99;
}
.approve:hover {
    background: #00cc7a;
    box-shadow: 0 0 15px #00ff99;
}

.reject, .delete {
    background: #ff3b3b;
    color: #fff;
}
.reject:hover, .delete:hover {
    background: #c92a2a;
    box-shadow: 0 0 15px #ff3b3b;
}

.edit {
    background: #ffaa00;
    color: #0a0a23;
}
.edit:hover {
    background: #e69900;
    box-shadow: 0 0 15px #ffaa00;
}

/* Back Button */
.back-btn {
    display: block;
    width: fit-content;
    margin: 25px auto 0;
    padding: 10px 22px;
    border-radius: 10px;
    background: #00ff99;
    color: #0a0a23;
    text-decoration: none;
    font-weight: 600;
    box-shadow: 0 0 15px #00ff99;
    transition: all 0.3s ease;
    text-align: center;
}
.back-btn:hover {
    background: #00cc7a;
    box-shadow: 0 0 25px #00ff99;
}

/* Responsive Fixes */
@media screen and (max-width: 900px) {
    body {
        padding: 10px;
    }
    table, th, td {
        font-size: 11px;
    }
    img {
        max-width: 50px;
    }
    .action-btn {
        font-size: 10px;
        padding: 5px 8px;
    }
}
</style>


</head>
<body>

<div class="container">
    <h2>Manage Lost & Found Items</h2>

    <table>
        <thead>
            <tr>
                <th>No.</th>
                <th>Item</th>
                <th>Description</th>
                <th>Photo</th>
                <th>Type</th>
                <th>Status</th>
                <th>Reporter</th>
                <th>Email</th>
                <th>Claimed By</th>
                <th>Date Reported</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($r = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <td><?php echo $counter++; ?></td>
                <td><?php echo htmlspecialchars($r['item_name']); ?></td>
                <td><?php echo htmlspecialchars($r['description']); ?></td>
                <td>
                    <?php if (!empty($r['photo']) && file_exists("../uploads/".$r['photo'])): ?>
                        <img src="../uploads/<?php echo $r['photo']; ?>" alt="Item Image">
                    <?php else: ?>
                        No Image
                    <?php endif; ?>
                </td>
                <td><?php echo ucfirst($r['item_type']); ?></td>
                <td>
                    <?php if ($r['status'] == 'approved'): ?>
                        <span class="status-approved">Approved</span>
                    <?php elseif ($r['status'] == 'rejected'): ?>
                        <span class="status-rejected">Rejected</span>
                    <?php else: ?>
                        <span class="status-pending"><?php echo ucfirst($r['status']); ?></span>
                    <?php endif; ?>
                </td>
                <td><?php echo htmlspecialchars($r['reporter_name']); ?></td>
                <td><?php echo htmlspecialchars($r['reporter_email']); ?></td>
                <td><?php echo $r['claimed_by'] ? htmlspecialchars($r['claimed_by']) : '—'; ?></td>
                <td><?php echo date('Y-m-d', strtotime($r['date_reported'])); ?></td>
                <td>
                    <?php if ($r['status'] == 'pending') { ?>
                        <a href="manage_items.php?approve=<?php echo $r['id']; ?>" class="action-btn approve">Approve</a>
                        <a href="manage_items.php?reject=<?php echo $r['id']; ?>" class="action-btn reject" onclick="return confirm('Reject this item?');">Reject</a>
                    <?php } elseif ($r['status'] == 'approved') { ?>
                        <span class="status-approved">Approved</span>
                    <?php } elseif ($r['status'] == 'rejected') { ?>
                        <span class="status-rejected">Rejected</span>
                    <?php } ?>

                    <a href="edit_item.php?id=<?php echo $r['id']; ?>" class="action-btn edit">Edit</a>
                    <a href="manage_items.php?delete=<?php echo $r['id']; ?>" class="action-btn delete" onclick="return confirm('Are you sure you want to delete this item?');">Delete</a>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>

</body>
</html>
