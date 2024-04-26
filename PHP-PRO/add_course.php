<?php
session_start();
if (!isset($_SESSION["user"])) {
   header("Location: login.php");
   exit(); // Terminate script execution after redirection
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <link rel="stylesheet" href="sstyle.css">
</head>
<body>
<div class="wrapper">
<?php
require_once "database.php"; // Include the database connection script

if (isset($_POST["submit"])) {
    $courseName = $_POST["course_name"];
    $courseCode = $_POST["course_code"];

    // Insert course details into the database
    $sql = "INSERT INTO courses (course_name, course_code) VALUES (?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $courseName, $courseCode);
    mysqli_stmt_execute($stmt);

    echo "<div class='alert alert-success'>Course added successfully.</div>";
}
?>
    <h2>Add Course</h2>
    <form action="add_course.php" method="post">
        <div class="input-box">
            <input type="text" placeholder="Course Name" class="form-control" id="course_name" name="course_name" required>
        </div>
        <div class="input-box">
            <input type="text" placeholder="Course Codes" class="form-control" id="course_code" name="course_code" required>
        </div>
            <div class="input-box button">
                <input type="submit" class="btn btn-primary" value="Add Course" name="submit">
            </div>
    </form>
    </div>
</body>
</html>
