<?php
session_start();
require_once 'db_connection.php';

// Check if the user is logged in as a teacher
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header('Location: login.html');
    exit;
}

// Check if student_id and course_id are provided in the URL
if (!isset($_GET['student_id']) || !isset($_GET['course_id'])) {
    echo "Error: Student ID or Course ID not provided.";
    exit;
}

$student_id = $_GET['student_id'];
$course_id = $_GET['course_id'];

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize the input grade
    $grade = filter_input(INPUT_POST, 'grade', FILTER_VALIDATE_INT);
    if ($grade === false || $grade < 0 || $grade > 100) {
        echo "Error: Invalid grade value.";
        exit;
    }

    // Update the grades table with the new grade
    $update_grades_query = "UPDATE grades SET grade = '$grade' WHERE student_id = '$student_id' AND course_id = '$course_id'";
    $update_grades_result = mysqli_query($conn, $update_grades_query);

    if (!$update_grades_result) {
        die("Database query error: " . mysqli_error($conn));
    }

    // Update enrollment status if the grade is 50 and above
    if ($grade >= 50) {
        $update_enrollment_query = "UPDATE enrollments SET enrollment_status = 'completed' WHERE student_id = '$student_id' AND course_id = '$course_id'";
        $update_enrollment_result = mysqli_query($conn, $update_enrollment_query);

        if (!$update_enrollment_result) {
            die("Database query error: " . mysqli_error($conn));
        }
    }

    // Redirect back to the teacher dashboard after updating the grade
    header("Location: teacher_dashboard.php");
    exit;
}

// Fetch the existing grade for the student in the selected course
$get_grade_query = "SELECT grade FROM grades WHERE student_id = '$student_id' AND course_id = '$course_id'";
$get_grade_result = mysqli_query($conn, $get_grade_query);

if (!$get_grade_result) {
    die("Database query error: " . mysqli_error($conn));
}

// Fetch the existing grade value
$existing_grade = ($get_grade_result && mysqli_num_rows($get_grade_result) > 0) ? mysqli_fetch_assoc($get_grade_result)['grade'] : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Grade</title>
    <!-- Include any necessary CSS stylesheets here -->
</head>
<body>

<h1>Update Grade for Student ID <?php echo $student_id; ?> - Course ID <?php echo $course_id; ?></h1>

<form method="post" action="update_grade.php?student_id=<?php echo $student_id; ?>&course_id=<?php echo $course_id; ?>">
    <label for="grade">Enter Updated Grade:</label>
    <input type="number" name="grade" id="grade" min="0" max="100" value="<?php echo $existing_grade; ?>" required>
    <button type="submit">Update Grade</button>
</form>

<!-- Include any necessary scripts here -->

</body>
</html>
