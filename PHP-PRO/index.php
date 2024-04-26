<?php
session_start();
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit(); // Stop further execution
}
require_once "database.php";

// Retrieve courses from the database
$sql = "SELECT * FROM courses";
$result = mysqli_query($conn, $sql);
$courses = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="w_con">
        <h2>Attendance Sheet</h2>
        <div class="sess">
            <h3>Session</h3>
        </div>
        <a href="add_student.php" style="margin-left:65%;" class="input-box button">Add Student</a>
        <a href="add_course.php" class="input-box button">Add Course</a><br><br>

        <h2>Your Courses</h2>
        <div class="main">
            <?php foreach ($courses as $course) : ?>
                <div class="main_con">
                    <div class="course-box">
                        <a href="c_d.php?course_code=<?php echo $course['course_code']; ?>"><?php echo $course['course_name']; ?></a>
                        <!-- Add delete link for each course -->
                        <a href="delete_course.php?course_id=<?php echo $course['course_id']; ?>" onclick="return confirm('Are you sure you want to delete this course?')" class="btn btn-danger">Delete</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <a href="logout.php" style="margin-left:90%;" class="input-box button">Logout</a>
    </div>
</body>
</html>
