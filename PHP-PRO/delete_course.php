<?php
session_start();
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}
require_once "database.php";

if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["course_id"])) {
    $course_id = $_GET["course_id"];

    // Prepare and execute the delete query
    $sql = "DELETE FROM courses WHERE course_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $course_id);
    mysqli_stmt_execute($stmt);

    // Check if the course was deleted successfully
    if (mysqli_stmt_affected_rows($stmt) > 0) {
        echo "<script>alert('Course deleted successfully!');</script>";
    } else {
        echo "<script>alert('Failed to delete course.');</script>";
    }

    // Close statement
    mysqli_stmt_close($stmt);

    // Redirect back to the index page
    header("Location: index.php");
    exit();
} else {
    // Redirect if course_id is not provided
    header("Location: index.php");
    exit();
}
?>
