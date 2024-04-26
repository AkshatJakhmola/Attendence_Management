<?php
session_start();

if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

require_once "database.php"; // Include the database connection script

// Fetch course codes from the database
$sql = "SELECT course_code FROM courses";
$result = mysqli_query($conn, $sql);
$courseCodes = mysqli_fetch_all($result, MYSQLI_ASSOC);

if (isset($_POST["submit"])) {
    // Retrieve form data
    $studentName = $_POST["student_name"];
    $studentRollNo = $_POST["roll_number"];
    $selectedCourseCode = $_POST["course_code"];

    // Check if the roll number already exists
    $sql_check = "SELECT * FROM stu_dtls WHERE roll_number = ?";
    $stmt_check = mysqli_prepare($conn, $sql_check);
    mysqli_stmt_bind_param($stmt_check, "s", $studentRollNo);
    mysqli_stmt_execute($stmt_check);
    $result_check = mysqli_stmt_get_result($stmt_check);

    if (mysqli_num_rows($result_check) > 0) {
        echo "<div class='alert alert-danger'>Roll number already exists!</div>";
    } else {
        // Insert the student details into the database
        $sql_insert = "INSERT INTO stu_dtls (student_name, roll_number, course_code) VALUES (?, ?, ?)";
        $stmt_insert = mysqli_prepare($conn, $sql_insert);
        mysqli_stmt_bind_param($stmt_insert, "sss", $studentName, $studentRollNo, $selectedCourseCode);
        mysqli_stmt_execute($stmt_insert);
        echo "<div class='alert alert-success'>Student details added successfully.</div>";
    }
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
    <title>Add Student</title>
</head>
<body>
    <div class="wrapper">
        <h2>Add Student Details</h2>
        <form action="add_student.php" method="post">
            <div class="input-box">
                <input type="text" placeholder="Student Name" class="form-control" id="student_name" name="student_name" required>
            </div>
            <div class="input-box">
                <input type="text" placeholder="Roll Number" class="form-control" id="roll_number" name="roll_number" required>
            </div>
            <div class="input-box">
                <select name="course_code" class="form-control" required>
                    <option value="">Select Course Code</option>
                    <?php foreach ($courseCodes as $course) : ?>
                        <option value="<?php echo $course['course_code']; ?>"><?php echo $course['course_code']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="input-box button">
                <input type="submit" value="Add Student" name="submit" class="btn btn-primary">
            </div>
        </form>
        <div style="width:18%;"  class="reg"><p>Back to <a href="index.php">Home</a></p></div>
    </div>
</body>
</html>
