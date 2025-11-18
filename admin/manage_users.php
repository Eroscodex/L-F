<?php
session_start();
include("../config/db.php");

// Admin-only access
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    echo "<script>alert('Access denied!');window.location='../auth/login.php';</script>";
    exit;
}

// Approve user
if (isset($_GET['approve'])) {
    $id = intval($_GET['approve']);
    mysqli_query($conn, "UPDATE users SET status='active' WHERE id=$id");
    echo "<script>alert('User approved successfully!');window.location='manage_users.php';</script>";
    exit;
}

// Delete user
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    mysqli_query($conn, "DELETE FROM users WHERE id=$id");
    echo "<script>alert('User deleted successfully!');window.location='manage_users.php';</script>";
    exit;
}

// Fetch users after handling actions
$result = mysqli_query($conn, "SELECT * FROM users ORDER BY id DESC");
$counter = 1;
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Users</title>
<style>
@import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap');

body {
    margin: 0;
    font-family: 'Roboto', sans-serif;
    background: #0a0a23;
    min-height: 100vh;
    padding: 30px;
    color: #e0e0e0;
}

h2 {
    text-align: center;
    color: #00ff99;
    margin-bottom: 30px;
    text-transform: uppercase;
    letter-spacing: 1px;
    text-shadow: 0 0 10px #00ff99;
}

table {
    width: 100%;
    border-collapse: collapse;
    background: #121233;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 0 20px rgba(0, 255, 153, 0.2);
}

th, td {
    padding: 12px 15px;
    text-align: center;
    border-bottom: 1px solid rgba(0, 255, 153, 0.2);
    font-size: 14px;
    color: #e0e0e0;
}

th {
    background: #0f0f2e;
    color: #00ff99;
    text-transform: uppercase;
    font-weight: 500;
    border-bottom: 2px solid #00ff99;
}

tr:hover {
    background: rgba(0, 255, 153, 0.05);
    transition: background 0.3s;
}

a.action-btn {
    display: inline-block;
    padding: 6px 12px;
    border-radius: 8px;
    text-decoration: none;
    color: #0a0a23;
    font-weight: 500;
    transition: 0.3s;
    margin: 2px;
    background: #00ff99;
    box-shadow: 0 0 10px #00ff99;
}

a.action-btn:hover {
    background: #00cc7a;
    box-shadow: 0 0 20px #00ff99;
}

a.approve {
    background: #00ff99;
}

a.delete {
    background: #ff3b3b;
    color: #fff;
    box-shadow: 0 0 10px #ff3b3b;
}

a.delete:hover {
    background: #cc2e2e;
    box-shadow: 0 0 20px #ff3b3b;
}


@media screen and (max-width: 768px) {
    table, thead, tbody, th, td, tr {
        display: block;
    }

    th, td {
        text-align: right;
        padding-left: 50%;
        position: relative;
    }

    th::before, td::before {
        position: absolute;
        left: 15px;
        top: 12px;
        font-weight: bold;
        white-space: nowrap;
        color: #00ff99;
    }

    th::before { content: attr(data-label); }
    td::before { content: attr(data-label); }
}

</style>
</head>
<body>

<h2>Manage Users</h2>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Password</th>
            <th>Role</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while($r = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <td data-label="ID"><?php echo $counter++; ?></td>
                <td data-label="Name"><?php echo htmlspecialchars($r['name']); ?></td>
                <td data-label="Email"><?php echo htmlspecialchars($r['email']); ?></td>
                <td data-label="Password"><?php echo htmlspecialchars($r['password']); ?></td>
                <td data-label="Role"><?php echo ucfirst($r['role']); ?></td>
                <td data-label="Status"><?php echo ucfirst($r['status']); ?></td>
                <td data-label="Actions">
                    <?php if($r['status'] != 'active') { ?>
                        <a href="manage_users.php?approve=<?php echo $r['id']; ?>" class="action-btn approve" onclick="return confirm('Approve this user?');">Approve</a>
                    <?php } ?>
                    <a href="manage_users.php?delete=<?php echo $r['id']; ?>" class="action-btn delete" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                </td>
            </tr>
            <?php } ?>
        <?php else: ?>
            <tr><td colspan="7">No users found.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

</body>
</html>
