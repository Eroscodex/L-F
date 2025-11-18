<?php
session_start();
include("../config/db.php");

// Check login
if (!isset($_SESSION['email'])) {
    echo "<script>alert('Access denied! Please log in first.');window.location='../auth/login.php';</script>";
    exit;
}

$user = $_SESSION['email'];

// 🔹 Define admin(s)
$isAdmin = ($user === 'admin@gmail.com'); // <-- change this to your real admin email

// Handle Delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);

    if ($isAdmin) {
        // Admin can delete any feedback
        $delete_query = "DELETE FROM feedback WHERE id=?";
        $stmt = mysqli_prepare($conn, $delete_query);
        mysqli_stmt_bind_param($stmt, "i", $id);
    } else {
        // Normal user can only delete their own
        $delete_query = "DELETE FROM feedback WHERE id=? AND user=?";
        $stmt = mysqli_prepare($conn, $delete_query);
        mysqli_stmt_bind_param($stmt, "is", $id, $user);
    }

    mysqli_stmt_execute($stmt);
    echo "<script>alert('Feedback deleted successfully!');window.location='feedback.php';</script>";
    exit;
}

// Handle Edit (Load data)
$editData = null;
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);

    if ($isAdmin) {
        $edit_query = "SELECT * FROM feedback WHERE id=?";
        $stmt = mysqli_prepare($conn, $edit_query);
        mysqli_stmt_bind_param($stmt, "i", $id);
    } else {
        $edit_query = "SELECT * FROM feedback WHERE id=? AND user=?";
        $stmt = mysqli_prepare($conn, $edit_query);
        mysqli_stmt_bind_param($stmt, "is", $id, $user);
    }

    mysqli_stmt_execute($stmt);
    $result_edit = mysqli_stmt_get_result($stmt);
    $editData = mysqli_fetch_assoc($result_edit);
}

// Handle Form Submission (Create or Update)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $rating = intval($_POST['rating']);
    $comment = trim($_POST['comment']);

    if ($rating < 1 || $rating > 5 || empty($comment)) {
        echo "<script>alert('Please provide a valid rating (1–5) and comment.');</script>";
    } else {
        if (isset($_POST['update_id']) && !empty($_POST['update_id'])) {
            // Update existing feedback
            $id = intval($_POST['update_id']);
            if ($isAdmin) {
                $query = "UPDATE feedback SET rating=?, comment=? WHERE id=?";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, "isi", $rating, $comment, $id);
            } else {
                $query = "UPDATE feedback SET rating=?, comment=? WHERE id=? AND user=?";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, "isis", $rating, $comment, $id, $user);
            }

            if (mysqli_stmt_execute($stmt)) {
                echo "<script>alert('Feedback updated successfully!');window.location='feedback.php';</script>";
            } else {
                echo "<script>alert('Error updating feedback.');</script>";
            }
        } else {
            // Insert new feedback
            $query = "INSERT INTO feedback (user, rating, comment) VALUES (?, ?, ?)";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "sis", $user, $rating, $comment);
            if (mysqli_stmt_execute($stmt)) {
                echo "<script>alert('Thank you for your feedback!');window.location='feedback.php';</script>";
            } else {
                echo "<script>alert('Error submitting feedback.');</script>";
            }
        }
    }
}

$result = mysqli_query($conn, "SELECT * FROM feedback ORDER BY date_submitted DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Feedback Management</title>
    <link rel="icon" type="image/jpg" href="/images/L&F.jpg">
<style>
body {
    margin: 0;
    font-family: 'Poppins', sans-serif;
    background: #0a0a23;
    color: #e0e0e0;
    min-height: 100vh;
}

/* Headings */
h2, h3 {
    text-align: center;
    color: #00ff99;
    text-shadow: 0 0 12px #00ff99;
    margin-bottom: 20px;
}

/* Form Container */
form {
    width: 60%;
    margin: 30px auto;
    background: rgba(17, 17, 51, 0.9);
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 0 25px rgba(0, 255, 153, 0.2);
    backdrop-filter: blur(8px);
}

/* Inputs & Textareas */
input, textarea, select, button {
    width: 96%;
    padding: 10px;
    margin: 10px 0;
    background: #0f0f2b;
    color: #00ff99;
    border: 1px solid #00ff99;
    border-radius: 6px;
    outline: none;
    font-size: 14px;
    transition: all 0.3s ease;
}

input:focus, textarea:focus, select:focus {
    border-color: #00ffcc;
    box-shadow: 0 0 10px #00ff99;
}

input::placeholder, textarea::placeholder {
    color: #00ff9980;
}

/* Buttons */
button {
    width: 100%;
    background: #00ff99;
    color: #0a0a23;
    font-weight: bold;
    cursor: pointer;
    border: none;
    border-radius: 6px;
    transition: 0.3s;
    box-shadow: 0 0 15px #00ff99;
}
button:hover {
    background: transparent;
    color: #00ff99;
    box-shadow: 0 0 20px #00ff99;
}

/* Table Container */
table {
    width: 85%;
    margin: 40px auto;
    border-collapse: collapse;
    background: rgba(20, 20, 58, 0.95);
    color: #e0e0e0;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 0 25px rgba(0, 255, 153, 0.2);
}

/* Table Header & Cells */
th, td {
    padding: 12px 15px;
    text-align: center;
    font-size: 13px;
    border-bottom: 1px solid rgba(0, 255, 153, 0.1);
}

th {
    background: rgba(0, 255, 153, 0.15);
    color: #00ff99;
    text-transform: uppercase;
    letter-spacing: 0.8px;
}

tr:nth-child(even) {
    background: rgba(255, 255, 255, 0.05);
}

tr:hover {
    background: rgba(0, 255, 153, 0.08);
    transition: 0.3s ease;
}

/* Buttons in Actions */
.action-btn {
    display: inline-block;
    padding: 6px 10px;
    border-radius: 5px;
    text-decoration: none;
    color: #0a0a23;
    font-weight: 600;
    transition: 0.3s ease;
    font-size: 12px;
    cursor: pointer;
}

.edit-btn {
    background: #00ff99;
    box-shadow: 0 0 10px #00ff99;
}
.edit-btn:hover {
    background: transparent;
    color: #00ff99;
    box-shadow: 0 0 15px #00ff99;
}

.delete-btn {
    background: #ff0055;
    color: #fff;
    box-shadow: 0 0 10px #ff0055;
}
.delete-btn:hover {
    background: transparent;
    color: #ff0055;
    box-shadow: 0 0 20px #ff0055;
}

/* Responsive */
@media screen and (max-width: 900px) {
    form {
        width: 90%;
    }

    table {
        width: 95%;
        font-size: 12px;
    }

    th, td {
        padding: 10px 8px;
    }

    button, input, textarea, select {
        font-size: 13px;
    }
}
</style>

</head>
<body>


<h2><?php echo $editData ? "Edit Feedback" : "Submit Your Feedback"; ?></h2>

<form method="POST" action="">
    <input type="hidden" name="update_id" value="<?php echo $editData['id'] ?? ''; ?>">
    
    <label for="rating">Rating (1-5):</label>
    <select name="rating" id="rating" required>
        <option value="">--Select--</option>
        <?php for ($i=1; $i<=5; $i++): ?>
            <option value="<?php echo $i; ?>" <?php if (isset($editData['rating']) && $editData['rating']==$i) echo 'selected'; ?>>
                <?php echo $i; ?> - <?php echo ["Very Poor","Poor","Average","Good","Excellent"][$i-1]; ?>
            </option>
        <?php endfor; ?>
    </select>

    <label for="comment">Your Comment:</label>
    <textarea name="comment" id="comment" rows="5" required><?php echo htmlspecialchars($editData['comment'] ?? ''); ?></textarea>

    <button type="submit"><?php echo $editData ? "Update Feedback" : "Submit Feedback"; ?></button>
</form>

<h3>Previous Feedback</h3>

<table>
    <tr>
        <th>No.</th>
        <th>User</th>
        <th>Rating</th>
        <th>Comment</th>
        <th>Date Submitted</th>
        <th>Actions</th>
    </tr>
    <?php 
    $i = 1;
    if (mysqli_num_rows($result) > 0):
        while ($row = mysqli_fetch_assoc($result)): 
    ?>
    <tr>
        <td><?php echo $i++; ?></td>
        <td><?php echo htmlspecialchars($row['user']); ?></td>
        <td><?php echo $row['rating']; ?>/5</td>
        <td><?php echo nl2br(htmlspecialchars($row['comment'])); ?></td>
        <td><?php echo date("F j, Y H:i", strtotime($row['date_submitted'])); ?></td>
        <td>
            <?php if ($row['user'] == $user || $isAdmin): ?>
                <a href="?edit=<?php echo $row['id']; ?>" class="action-btn edit-btn">Edit</a>
                <a href="?delete=<?php echo $row['id']; ?>" class="action-btn delete-btn" onclick="return confirm('Are you sure you want to delete this feedback?');">Delete</a>
            <?php else: ?>
                <em>N/A</em>
            <?php endif; ?>
        </td>
    </tr>
    <?php 
        endwhile;
    else:
    ?>
    <tr><td colspan="6" style="text-align:center;">No feedback yet.</td></tr>
    <?php endif; ?>
</table>

</body>
</html>
