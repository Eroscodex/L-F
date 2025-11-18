<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['name'])) {
    echo "<script>alert('Please login to submit feedback.');window.location='../auth/login.php';</script>";
    exit();
}

$user = mysqli_real_escape_string($conn, $_SESSION['name']);

// ADD FEEDBACK
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_feedback'])) {
    $rating = intval($_POST['rating']);
    $comment = mysqli_real_escape_string($conn, $_POST['comment']);

    $insert = mysqli_query($conn, "
        INSERT INTO feedback (user, rating, comment, date_submitted)
        VALUES ('$user', $rating, '$comment', NOW())
    ");

    if ($insert) {
        echo "<script>alert('Thank you for your feedback!');window.location='feedback.php';</script>";
    } else {
        echo "<script>alert('Error submitting feedback.');window.location='feedback.php';</script>";
    }
}

// DELETE FEEDBACK
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    mysqli_query($conn, "DELETE FROM feedback WHERE id=$id AND user='$user'");
    echo "<script>alert('Feedback deleted successfully.');window.location='feedback.php';</script>";
}

// EDIT FEEDBACK
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_feedback'])) {
    $id = intval($_POST['feedback_id']);
    $rating = intval($_POST['rating']);
    $comment = mysqli_real_escape_string($conn, $_POST['comment']);

    mysqli_query($conn, "
        UPDATE feedback SET rating=$rating, comment='$comment', date_submitted=NOW()
        WHERE id=$id AND user='$user'
    ");

    echo "<script>alert('Feedback updated successfully!');window.location='feedback.php';</script>";
}

// Fetch user feedbacks
$feedbacks = mysqli_query($conn, "SELECT * FROM feedback WHERE user='$user' ORDER BY date_submitted DESC");

// Fetch feedback for editing (if any)
$edit_feedback = null;
if (isset($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    $edit_query = mysqli_query($conn, "SELECT * FROM feedback WHERE id=$edit_id AND user='$user'");
    $edit_feedback = mysqli_fetch_assoc($edit_query);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Feedback | Lost & Found</title>
<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap');

body {
    margin: 0;
    font-family: 'Poppins', sans-serif;
    background: #0a0a23;
    color: #fff;
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: flex-start;
    padding: 20px; /* smaller padding for mobile */
}

/* Feedback container */
.feedback-container {
    background: rgba(10, 10, 35, 0.95);
    padding: 30px; /* reduce padding for mobile */
    border-radius: 16px;
    box-shadow: 0 0 20px rgba(0,255,153,0.2);
    width: 100%;
    max-width: 100%; /* full width on mobile */
    position: relative;
    border: 1px solid rgba(0,255,153,0.3);
}

/* Top bar and heading */
.top-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

h3 {
    text-align: center;
    margin: 10px 0 20px;
    color: #00ff99;
    font-weight: 600;
    font-size: 1.4rem;
    text-shadow: 0 0 10px #00ff99;
}

/* Labels and Inputs */
label { 
    font-weight: 500; 
    color: #00ff99; 
    text-shadow: 0 0 5px rgba(0,255,153,0.5);
}

input[type="number"], textarea {
    width: 100%; /* full width on mobile */
    padding: 10px;
    margin: 8px 0 12px;
    border-radius: 10px;
    border: 1px solid rgba(0,255,153,0.3);
    font-size: 14px;
    outline: none;
    transition: 0.3s;
    resize: none;
    background: rgba(255,255,255,0.05);
    color: #fff;
}

input[type="number"]:focus, textarea:focus {
    border-color: #00ff99;
    box-shadow: 0 0 10px #00ff99;
}

/* Buttons */
button {
    width: 100%;
    padding: 12px;
    margin-top: 10px;
    border: none;
    border-radius: 10px;
    background: #00ff99;
    color: #0a0a23;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: 0.3s;
    box-shadow: 0 0 15px rgba(0,255,153,0.3);
}

button:hover {
    transform: translateY(-2px);
    background: #0fff8f;
    box-shadow: 0 0 20px #00ff99;
}

/* Table */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    border-radius: 8px;
    overflow: hidden;
    background: rgba(255,255,255,0.05);
    box-shadow: 0 0 15px rgba(0,255,153,0.15);
    backdrop-filter: blur(6px);
    font-size: 14px; /* smaller font on mobile */
}

th, td {
    padding: 10px 5px;
    border-bottom: 1px solid rgba(255,255,255,0.1);
    text-align: center;
    color: #fff;
}

th {
    background: #00ff99;
    color: #0a0a23;
    font-weight: 600;
    text-transform: uppercase;
}

tr:hover {
    background: rgba(0,255,153,0.1);
    transition: 0.3s;
}

/* Action buttons */
a.action-btn {
    padding: 5px 8px;
    border-radius: 6px;
    text-decoration: none;
    color: #0a0a23;
    margin-right: 3px;
    font-size: 13px;
    display: inline-block;
    transition: 0.2s;
    font-weight: 500;
}

.edit { background: #00ff99; }
.edit:hover { background: #0fff8f; box-shadow: 0 0 12px #00ff99; }

.delete { background: #ff0040; color: #fff; }
.delete:hover { background: #ff3366; box-shadow: 0 0 12px #ff0040; }

/* Cancel button */
.cancel {
    display: inline-block;
    width: 100%;
    text-align: center;
    margin-top: 10px;
    padding: 10px;
    background: rgba(255,255,255,0.1);
    color: #00ff99;
    border: 1px solid rgba(0,255,153,0.4);
    border-radius: 8px;
    text-decoration: none;
    transition: 0.3s;
    font-weight: 500;
}

.cancel:hover {
    background: rgba(0,255,153,0.15);
    color: #fff;
    box-shadow: 0 0 10px #00ff99;
}

/* ===== MOBILE ADJUSTMENTS ===== */
@media (max-width: 768px) {
    body {
        padding: 15px;
    }

    .feedback-container {
        padding: 20px;
        border-radius: 12px;
    }

    h3 { font-size: 1.2rem; }

    input[type="number"], textarea { font-size: 13px; padding: 8px; }

    button { font-size: 15px; padding: 10px; }

    table th, table td { padding: 8px 4px; font-size: 12px; }

    a.action-btn { font-size: 12px; padding: 4px 6px; }

    .top-bar { flex-direction: column; gap: 10px; }
}

</style>
<script>
function confirmDelete(id) {
    if (confirm("Are you sure you want to delete this feedback?")) {
        window.location = "feedback.php?delete=" + id;
    }
}
</script>
</head>
<body>

<div class="feedback-container">
    <div class="top-bar">
        <h3><?php echo $edit_feedback ? "✏️ Edit Feedback" : "⭐ Submit Feedback"; ?></h3>
    </div>

    <form method="POST">
        <input type="hidden" name="feedback_id" value="<?php echo $edit_feedback['id'] ?? ''; ?>">
        <label>Rating (1–5):</label>
        <input type="number" name="rating" min="1" max="5" required value="<?php echo $edit_feedback['rating'] ?? ''; ?>">

        <label>Comment:</label>
        <textarea name="comment" placeholder="Write your feedback here..." required><?php echo $edit_feedback['comment'] ?? ''; ?></textarea>

        <?php if ($edit_feedback): ?>
            <button type="submit" name="edit_feedback">Update Feedback</button>
            <a href="feedback.php" class="cancel">Cancel</a>
        <?php else: ?>
            <button type="submit" name="add_feedback">Submit Feedback</button>
        <?php endif; ?>
    </form>

    <h3 style="margin-top:40px;">📝 My Feedbacks</h3>
    <?php if (mysqli_num_rows($feedbacks) > 0): ?>
    <table>
        <tr>
            <th>#</th>
            <th>Rating</th>
            <th>Comment</th>
            <th>Date</th>
            <th>Actions</th>
        </tr>
        <?php $i=1; while($row = mysqli_fetch_assoc($feedbacks)): ?>
        <tr>
            <td><?php echo $i++; ?></td>
            <td><?php echo $row['rating']; ?>/5</td>
            <td style="text-align:left;"><?php echo htmlspecialchars($row['comment']); ?></td>
            <td><?php echo date("M d, Y h:i A", strtotime($row['date_submitted'])); ?></td>
            <td>
                <a href="feedback.php?edit=<?php echo $row['id']; ?>" class="action-btn edit">Edit</a>
                <a href="javascript:void(0);" onclick="confirmDelete(<?php echo $row['id']; ?>)" class="action-btn delete">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
    <?php else: ?>
        <p style="text-align:center;">No feedback yet.</p>
    <?php endif; ?>
</div>


</body>
</html>
