<?php
session_start();
include("../config/db.php");

// Admin-only access
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    echo "<script>alert('Access denied!');window.location='../auth/login.php';</script>";
    exit;
}

// Approve or reject claim
if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $claim_id = intval($_GET['id']);

    // Fetch claim and item info
    $fetch = mysqli_query($conn, "
        SELECT c.*, i.item_name, i.reporter_email 
        FROM claims c 
        JOIN items i ON c.item_id = i.id 
        WHERE c.id=$claim_id
    ");
    $claim = mysqli_fetch_assoc($fetch);

    if ($claim) {
        $claimant_name = mysqli_real_escape_string($conn, $claim['claimant_name']);
        $claimant_email = mysqli_real_escape_string($conn, $claim['claimant_email']);
        $item_name = mysqli_real_escape_string($conn, $claim['item_name']);
        $item_id = intval($claim['item_id']);

        if ($action == 'approve') {
            
            mysqli_query($conn, "UPDATE claims SET status='approved' WHERE id=$claim_id");
            mysqli_query($conn, "UPDATE items SET status='claimed', claimed_by='$claimant_name' WHERE id=$item_id");

            
            $subject = mysqli_real_escape_string($conn, "Claim Approved");
            $message = mysqli_real_escape_string($conn, "Hello $claimant_name, your claim for the item '$item_name' has been approved by the admin.");
            mysqli_query($conn, "INSERT INTO notifications (recipient_email, subject, message, status, date_sent)
                                 VALUES ('$claimant_email', '$subject', '$message', 'unread', NOW())");

            echo "<script>alert('Claim approved successfully!');window.location='manage_claims.php';</script>";
        } 
        elseif ($action == 'reject') {
            
            mysqli_query($conn, "UPDATE claims SET status='rejected' WHERE id=$claim_id");


            $subject = mysqli_real_escape_string($conn, "Claim Rejected");
            $message = mysqli_real_escape_string($conn, "Hello $claimant_name, your claim for the item '$item_name' has been rejected by the admin.");
            mysqli_query($conn, "INSERT INTO notifications (recipient_email, subject, message, status, date_sent)
                                 VALUES ('$claimant_email', '$subject', '$message', 'unread', NOW())");

            echo "<script>alert('Claim rejected successfully!');window.location='manage_claims.php';</script>";
        }
        exit;
    }
}
$query = "
    SELECT 
        c.id AS claim_id, 
        c.item_id, 
        c.claimant_name, 
        c.claimant_email, 
        c.status AS claim_status, 
        c.date_requested,
        i.item_name,
        i.status AS item_status
    FROM claims c
    JOIN items i ON c.item_id = i.id
    ORDER BY c.date_requested DESC
";
$result = mysqli_query($conn, $query);
if (!$result) {
    die('Query Failed: ' . mysqli_error($conn));
}

$counter = 1;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Claim Requests</title>
    <style>
body {
    font-family: Arial, sans-serif;
    padding: 20px;
    background: #0a0a23;
    color: #e0e0e0;
    min-height: 100vh;
}

h2 {
    text-align: center;
    color: #00ff99;
    text-shadow: 0 0 10px #00ff99;
    margin-bottom: 20px;
}

.top-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

table {
    width: 100%;
    border-collapse: collapse;
    background: #121233;
    box-shadow: 0 0 20px rgba(0, 255, 153, 0.2);
    border-radius: 8px;
    overflow: hidden;
}

th, td {
    padding: 12px;
    border-bottom: 1px solid rgba(0, 255, 153, 0.2);
    text-align: center;
    font-size: 14px;
    color: #e0e0e0;
}

th {
    background: #0f0f2e;
    color: #00ff99;
    text-transform: uppercase;
    letter-spacing: 1px;
    font-weight: 600;
    border-bottom: 2px solid #00ff99;
}

tr:hover {
    background: rgba(0, 255, 153, 0.08);
    transition: background 0.3s ease;
}

.btn {
    padding: 6px 12px;
    text-decoration: none;
    border-radius: 6px;
    color: #0a0a23;
    font-weight: 500;
    transition: 0.3s;
    box-shadow: 0 0 10px #00ff99;
}

.btn-approve {
    background: #00ff99;
}

.btn-approve:hover {
    background: #00cc7a;
    box-shadow: 0 0 20px #00ff99;
}

.btn-reject {
    background: #ff3b3b;
    box-shadow: 0 0 10px #ff3b3b;
}

.btn-reject:hover {
    background: #cc2e2e;
    box-shadow: 0 0 20px #ff3b3b;
}

.status-pending {
    color: #ffaa00;
    font-weight: bold;
    text-shadow: 0 0 8px #ffaa00;
}

.status-approved {
    color: #00ff99;
    font-weight: bold;
    text-shadow: 0 0 8px #00ff99;
}

.status-rejected {
    color: #ff3b3b;
    font-weight: bold;
    text-shadow: 0 0 8px #ff3b3b;
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

<div class="top-bar">
    <h2>Manage Claim Requests</h2>
</div>

<table>
    <tr>
        <th>ID</th>
        <th>Item Name</th>
        <th>Claimant Name</th>
        <th>Claimant Email</th>
        <th>Status</th>
        <th>Date Requested</th>
        <th>Action</th>
    </tr>
    <?php if (mysqli_num_rows($result) > 0): ?>
        <?php while ($row = mysqli_fetch_assoc($result)): 
            $claim_status = strtolower(trim($row['claim_status']));
        ?>
            <tr>
                <td data-label="ID"><?php echo $counter++; ?></td>
                <td><?php echo htmlspecialchars($row['item_name']); ?></td>
                <td><?php echo htmlspecialchars($row['claimant_name']); ?></td>
                <td><?php echo htmlspecialchars($row['claimant_email']); ?></td>
                <td><span class="status-<?php echo $claim_status; ?>"><?php echo ucfirst($claim_status); ?></span></td>
                <td><?php echo $row['date_requested']; ?></td>
                <td>
                    <?php if ($claim_status == 'pending'): ?>
                        <a href="manage_claims.php?action=approve&id=<?php echo $row['claim_id']; ?>" class="btn btn-approve" onclick="return confirm('Approve this claim?');">Approve</a>
                        <a href="manage_claims.php?action=reject&id=<?php echo $row['claim_id']; ?>" class="btn btn-reject" onclick="return confirm('Reject this claim?');">Reject</a>
                    <?php else: ?>
                        N/A
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr><td colspan="7">No claim requests found.</td></tr>
    <?php endif; ?>
</table>

</body>
</html>
