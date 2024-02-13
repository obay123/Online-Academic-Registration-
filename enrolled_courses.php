<?php
session_start();
require_once 'db_connection.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch enrolled courses and enrollment date for the user
$query = "SELECT c.course_title, e.enrollment_date FROM Courses c
          JOIN Enrollments e ON c.course_id = e.course_id
          WHERE e.student_id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Format the enrolled courses with additional information
$enrolled_courses = '<table>';
$enrolled_courses .= '<tr><th>Course Title</th><th>Enrollment Date</th></tr>';
while ($row = mysqli_fetch_assoc($result)) {
    $enrolled_courses .= '<tr>';
    $enrolled_courses .= '<td>' . $row['course_title'] . '</td>';
    $enrolled_courses .= '<td>' . $row['enrollment_date'] . '</td>';
    $enrolled_courses .= '</tr>';
}
$enrolled_courses .= '</table>';

echo $enrolled_courses;
?>
