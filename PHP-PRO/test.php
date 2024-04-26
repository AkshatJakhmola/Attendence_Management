<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit(); // Stop further execution
}

// Include database connection
require_once "database.php";

// Check if course_id is provided in the URL
if (!isset($_GET["course_id"])) {
    // Redirect if course_id is not provided
    header("Location: index.php");
    exit(); // Stop further execution
}

// Retrieve course_id from the URL
$course_id = $_GET["course_id"];

// Query to retrieve student details for the selected course
$sql = "SELECT * FROM students WHERE course_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $course_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Fetch student details into an array
$students = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Close prepared statement
mysqli_stmt_close($stmt);

// Check if the attendance form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Check if the attendance data is set
    if (isset($_POST["attendance"])) {
        // Prepare and bind parameters for the attendance insertion
        $stmt = mysqli_prepare($conn, "INSERT INTO attendance (student_id, course_id, date, status) VALUES (?, ?, NOW(), ?)");
        mysqli_stmt_bind_param($stmt, "iis", $student_id, $course_id, $status);
        
        // Loop through the submitted attendance data
        foreach ($_POST["attendance"] as $student_id => $status) {
            // Execute the statement for each student's attendance
            mysqli_stmt_execute($stmt);
        }

        // Close prepared statement
        mysqli_stmt_close($stmt);
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Details</title>
    <!-- Add your CSS stylesheets here -->
</head>
<body>
    <h1>Student Details</h1>
    
    <!-- Display student details and attendance form -->
    <div class="student-details">
        <?php foreach ($students as $student) : ?>
            <div class="student">
                <h3><?php echo $student['student_name']; ?></h3>
                <!-- Attendance form -->
                <form action="display_student_details.php?course_id=<?php echo $course_id; ?>" method="post">
                    <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">
                    <input type="hidden" name="student_id" value="<?php echo $student['student_id']; ?>">
                    <label>
                        <input type="radio" name="attendance[<?php echo $student['student_id']; ?>]" value="Present"> Present
                    </label>
                    <label>
                        <input type="radio" name="attendance[<?php echo $student['student_id']; ?>]" value="Absent"> Absent
                    </label>
                    <input type="submit" value="Submit Attendance">
                </form>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Display attendance data -->
    <h2>Attendance Data</h2>
    <table>
        <thead>
            <tr>
                <th>Student Name</th>
                <th>Date</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Query to retrieve attendance data for the selected course
            $sql = "SELECT s.student_name, a.date, a.status FROM attendance a INNER JOIN students s ON a.student_id = s.student_id WHERE a.course_id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "i", $course_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            // Fetch attendance data and display it in a table
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>" . $row['student_name'] . "</td>";
                echo "<td>" . $row['date'] . "</td>";
                echo "<td>" . $row['status'] . "</td>";
                echo "</tr>";
            }

            // Close prepared statement
            mysqli_stmt_close($stmt);
            ?>
        </tbody>
    </table>
</body>
</html>
