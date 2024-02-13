<?php
session_start();
require_once 'db_connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header('Location: login.html');
    exit;
}

$teacher_id = $_SESSION['user_id'];
$sql = "SELECT username FROM users WHERE user_id = $teacher_id";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Fetch the result as an associative array
    $row = $result->fetch_assoc();

    // Set user_name in the session
    $_SESSION['user_name'] = $row['username'];

// Query to get the username of the logged-in teacher
$username_query = "SELECT username FROM Users WHERE user_id = $teacher_id";
$username_result = mysqli_query($conn, $username_query);

if (!$username_result) {
    die("Database query error: " . mysqli_error($conn));
}

// Fetch the username
$row = mysqli_fetch_assoc($username_result);
$teacher_username = $row['username'];
}
// Query to get the courses assigned to the teacher
$sql = "SELECT ci.course_id, c.course_title
        FROM course_instructors ci
        JOIN courses c ON ci.course_id = c.course_id
        WHERE ci.instructor_id = $teacher_id";

// Display the list of courses for grading
$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Database query error: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Dashboard</title>
    <link rel="stylesheet" href="teacher_dashboard.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
</head>
<body>

<header>
    <nav>
        <ul>
            <li><a href="homepage.php"><ion-icon name="home-outline"></ion-icon>Home</a></li>
            <li><a href="logout.php"><ion-icon name="power-outline"></ion-icon>Logout</a></li>
        </ul>
    </nav>
</header>
<?php

echo '<h1> Hello ' . $_SESSION['user_name'] . '</h1>';

 ?>
<h1></h1>

<form method="post" action="teacher_dashboard.php">
    <label for="course">Select Course:</label>
    <select name="course" id="course" required>
        <?php
        while ($row = mysqli_fetch_assoc($result)) {
            echo '<option value="' . $row['course_id'] . '">' . $row['course_id'] . '</option>';
        }
        ?>
    </select>

    <button type="submit" name="view_students">View Students</button>
</form>

<?php
// Check if the form is submitted to view students
if (isset($_POST['view_students'])) {
    $selected_course_id = $_POST['course'];

    // Query to get the students enrolled in the selected course
    $students_query = "SELECT u.user_id, u.username, e.enrollment_status
                       FROM users u
                       INNER JOIN enrollments e ON u.user_id = e.student_id
                       WHERE e.course_id = '$selected_course_id'";

    $students_result = mysqli_query($conn, $students_query);

    if ($students_result) {
        echo '<h2>Students Enrolled in Course ' . $selected_course_id . '</h2>';
        echo '<table border="1">
                <thead>
                    <tr>
                        <th>Student ID</th>
                        <th>Student Name</th>
                        <th>Enrollment Status</th>
                        <th>Grades</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>';
                while ($student_row = mysqli_fetch_assoc($students_result)) {
                    $student_id = $student_row['user_id'];
                
                    // Query to get grades for the student in the selected course
                    $grades_query = "SELECT grade FROM grades
                                    WHERE student_id = '$student_id'
                                    AND course_id = '$selected_course_id'";
                
                    $grades_result = mysqli_query($conn, $grades_query);
                    $grade = ($grades_result && mysqli_num_rows($grades_result) > 0) ? mysqli_fetch_assoc($grades_result)['grade'] : '-';
                
                    echo '<tr>
                            <td>' . $student_id . '</td>
                            <td>' . $student_row['username'] . '</td>
                            <td id="enrollment-status-' . $student_id . '">' . $student_row['enrollment_status'] . '</td>
                            <td id="grade-' . $student_id . '" contenteditable="false">' . $grade . '</td>
                            <td>';
                
                    // Add Grade button for enrolled students
                  // Add Grade button for enrolled students
            if ($student_row['enrollment_status'] === 'enrolled') {
                $addGradeLink = 'Add_grade.php?student_id=' . $student_id . '&course_id=' . $selected_course_id;
                echo '<a class="btn btn-primary" href="' . $addGradeLink . '" role="button">Add Grade</a>';
            }   
                    
                    // Edit Grade button for withdrawn students
                    elseif ($student_row['enrollment_status'] === 'withdrawn'||$student_row['enrollment_status'] === 'completed') {
                        $updategradeLink = 'update_grade.php?student_id=' . $student_id . '&course_id=' . $selected_course_id;
                        echo '<a class="btn btn-primary" href="' . $updategradeLink . '" role="button">Update Grade</a>';
                        
                      
                    }
                
                    echo '</td></tr>';
                }
                
                echo '</tbody></table>';
                
    } else {
        echo '<div class="message error">Error fetching students: ' . mysqli_error($conn) . '</div>';
    }
}
?>

<!-- The rest of your HTML content... -->

<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

</body>
</html>