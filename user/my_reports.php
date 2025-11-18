<?php
include("../config/db.php");
session_start();

// Check login
if (!isset($_SESSION['email'])) {
    echo "<script>alert('Access denied! Please login first.');window.location='../auth/login.php';</script>";
    exit();
}

$user_email = $_SESSION['email'];
$user_name = $_SESSION['name'];

// Fetch all reports by current user
$query = "SELECT * FROM items WHERE reporter_email='$user_email' ORDER BY id DESC";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Error fetching reports: " . mysqli_error($conn));
}

$counter = 1;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Reports - Digital Lost & Found</title>
<style>
@import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap');
* { box-sizing: border-box; }

body {
    margin: 0;
    font-family: 'Roboto', sans-serif;
    background: #0a0a23;
    color: #fff;
    min-height: 100vh;
    padding: 20px;
}

h2 {
    text-align: center;
    color: #00ff99;
    margin-bottom: 30px;
    text-shadow: 0 0 10px #00ff99;
}

table {
    width: 100%;
    border-collapse: collapse;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 0 15px rgba(0, 255, 153, 0.2);
    backdrop-filter: blur(6px);
}

th, td {
    padding: 12px 15px;
    text-align: center;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    font-size: 14px;
    color: #fff;
}

th {
    background: #00ff99;
    color: #0a0a23;
    text-transform: uppercase;
    font-weight: 600;
    letter-spacing: 0.5px;
}

tr:hover { 
    background: rgba(0, 255, 153, 0.1);
    transition: 0.3s;
}

img {
    border-radius: 8px;
    max-width: 80px;
    border: 1px solid rgba(0,255,153,0.3);
}

/* Buttons */
a.action-btn {
    display: inline-block;
    padding: 6px 12px;
    border-radius: 8px;
    text-decoration: none;
    color: #0a0a23;
    font-weight: 500;
    margin: 2px;
    transition: 0.3s;
    box-shadow: 0 0 10px rgba(0,255,153,0.3);
}

a.edit { background: #1e90ff; color: #fff; }
a.edit:hover { background: #63a4ff; box-shadow: 0 0 15px #1e90ff; }

a.delete { background: #ff0040; color: #fff; }
a.delete:hover { background: #ff3366; box-shadow: 0 0 15px #ff0040; }

a.view { background: #00e0ff; color: #0a0a23; }
a.view:hover { background: #33ecff; box-shadow: 0 0 15px #00e0ff; }

/* Responsive Styles */
@media screen and (max-width: 768px) {
    table, thead, tbody, th, td, tr { display: block; }
    thead { display: none; }
    tr {
        margin-bottom: 15px;
        border: 1px solid rgba(255,255,255,0.1);
        border-radius: 10px;
        padding: 10px;
        background-color: rgba(255,255,255,0.05);
        box-shadow: 0 0 10px rgba(0,255,153,0.1);
    }
    td {
        text-align: left;
        padding: 10px 10px 10px 40%;
        position: relative;
    }
    td::before {
        content: attr(data-label);
        position: absolute;
        left: 10px;
        top: 10px;
        font-weight: bold;
        color: #00ff99;
        width: 35%;
        white-space: nowrap;
    }
    td img { max-width: 100%; height: auto; }
    a.action-btn {
        width: 100%;
        text-align: center;
        margin: 5px 0;
    }
}
</style>
</head>
<body>

<h2>📋 My Reported Items</h2>

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Item Name</th>
            <th>Description</th>
            <th>Photo</th>
            <th>Type</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if (mysqli_num_rows($result) > 0) { 
            while ($row = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <td data-label="#"><?php echo $counter++; ?></td>
                <td data-label="Item Name"><?php echo htmlspecialchars($row['item_name']); ?></td>
                <td data-label="Description"><?php echo htmlspecialchars($row['description']); ?></td>
                <td data-label="Photo">
                    <?php if (!empty($row['photo']) && file_exists("../uploads/" . $row['photo'])) { ?>
                        <img src="../uploads/<?php echo $row['photo']; ?>" alt="Item Photo">
                    <?php } else { echo "No Image"; } ?>
                </td>
                <td data-label="Type"><?php echo ucfirst($row['item_type']); ?></td>
                <td data-label="Status"><?php echo ucfirst($row['status']); ?></td>
                <td data-label="Actions">
                    <a href="edit_report.php?id=<?php echo $row['id']; ?>" class="action-btn edit">Edit</a>
                    <a href="delete_report.php?id=<?php echo $row['id']; ?>" 
                       onclick="return confirm('Are you sure you want to delete this report?');" 
                       class="action-btn delete">Delete</a>
                </td>
            </tr>
        <?php } } else { ?>
            <tr><td colspan="7">No reports found.</td></tr>
        <?php } ?>
    </tbody>
</table>

</body>
</html>
