<?php
session_start();
include("../config/db.php");

// Check if logged in
if (!isset($_SESSION['email'])) {
    echo "<script>alert('Access denied! Please login first.');window.location='../auth/login.php';</script>";
    exit();
}

$user_name = $_SESSION['name'];

// Check if report ID is provided
if (!isset($_GET['id'])) {
    echo "<script>alert('No report selected.');window.location='my_reports.php';</script>";
    exit();
}

$report_id = intval($_GET['id']);

// Fetch the report to ensure the user owns it
$result = mysqli_query($conn, "SELECT * FROM items WHERE id=$report_id AND reporter_name='$user_name'");
if (!$result || mysqli_num_rows($result) == 0) {
    echo "<script>alert('Report not found or access denied.');window.location='my_reports.php';</script>";
    exit();
}

$report = mysqli_fetch_assoc($result);

// Delete the report
$delete = mysqli_query($conn, "DELETE FROM items WHERE id=$report_id");
if ($delete) {
    // Delete the photo if exists
    if (!empty($report['photo']) && file_exists("../uploads/".$report['photo'])) {
        unlink("../uploads/".$report['photo']);
    }
    echo "<script>alert('Report deleted successfully!');window.location='my_reports.php';</script>";
} else {
    echo "<script>alert('Failed to delete report: ".mysqli_error($conn)."');window.location='my_reports.php';</script>";
}
?>
