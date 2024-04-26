<?php
session_start();

if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

require_once "database.php"; // Include the database connection script

if (isset($_GET['course_code'])) {
    $courseCode = $_GET['course_code'];

    // Fetch students for the selected course from the database
    $sql = "SELECT * FROM stu_dtls WHERE course_code = ?";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt === false) {
        // Handle preparation error
        die("Preparation of statement failed: " . mysqli_error($conn));
    }
    mysqli_stmt_bind_param($stmt, "s", $courseCode);
    $success = mysqli_stmt_execute($stmt);
    if (!$success) {
        // Handle execution error
        die("Execution of statement failed: " . mysqli_stmt_error($stmt));
    }
    $result = mysqli_stmt_get_result($stmt);
    $students = mysqli_fetch_all($result, MYSQLI_ASSOC);
} else {
    // Redirect back to index.php if course code is not provided
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Retrieve date and attendance status from the form submission
    $date = $_POST['date'];
    $course_id = $_POST['course_code'];
    $attendance = $_POST['attendance']; // Array of student IDs and attendance status

    // Prepare and execute the query to save attendance status for each student
    $sql = "INSERT INTO attendance (student_id, course_id, date, status) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    
    print_r($_POST);
    
    if ($stmt === false) {
        // Handle preparation error
        die("Preparation of statement failed: " . mysqli_error($conn));
    }
    foreach ($attendance as $student_id => $status) {
        mysqli_stmt_bind_param($stmt, "iiss", $student_id, $course_id, $date, $status);
        $success = mysqli_stmt_execute($stmt);
        if (!$success) {
            // Handle execution error
            die("Execution of statement failed: " . mysqli_stmt_error($stmt));
        }
    }

    // Redirect back to the same page to avoid form resubmission
    header("Location: ".$_SERVER['PHP_SELF']."?course_code=$courseCode");
    exit();
}

// Function to calculate attendance percentage
function calculateAttendancePercentage($totalRecords, $presentRecords) {
    if ($totalRecords > 0) {
        return round(($presentRecords / $totalRecords) * 100, 2);
    } else {
        return 0;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Attendance</title>
    <link rel="stylesheet" href="sstyle.css">
</head>
<body>
    <div class="wrapper">
        <h2>Students in Course <?php echo $courseCode; ?></h2>
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']."?course_code=$courseCode"); ?>" method="post">
            <label for="date">Date:</label>
            <input type="date" id="date" name="date" required>
            <input type="hidden" name="course_code" value="<?php echo $courseCode; ?>">
            <ol>
                <?php foreach ($students as $student) : ?>
                    <li>
                        <?php echo $student['student_name']; ?> (Roll Number: <?php echo $student['roll_number'];
                        ?>)
                        <?php
                            // Query to fetch attendance records for the student
                            $query = "SELECT COUNT(*) AS total_records, SUM(IF(status='Present', 1, 0)) AS present_records FROM attendance WHERE student_id = ?";
                            $stmt = mysqli_prepare($conn, $query);
                            mysqli_stmt_bind_param($stmt, "i", $student['student_id']);
                            mysqli_stmt_execute($stmt);
                            $result = mysqli_stmt_get_result($stmt);
                            $row = mysqli_fetch_assoc($result);
                            // Calculate attendance percentage
                            $attendancePercentage = calculateAttendancePercentage($row['total_records'], $row['present_records']);
                        ?>
                        <span>Attendance: <?php echo $attendancePercentage; ?>%</span>
                        <label><br>
                            <?php echo $student['student_name']; ?>:
                            <input type="radio" name="attendance[<?php echo $student['student_id']; ?>]" value="Present" required> Present
                            <input type="radio" name="attendance[<?php echo $student['student_id']; ?>]" value="Absent"> Absent
                        </label>
                    </li>
                <?php endforeach; ?>
            </ol>
            <div class="input-box button">
                <input type="submit" value="Submit Attendance">
            </div>
        </form>
        <a href="index.php">Back to Dashboard</a>
    </div>
</body>
</html>
