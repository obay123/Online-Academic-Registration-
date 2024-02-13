    <?php
    session_start();
    require_once 'db_connection.php';
    ?>
    <DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Student Dashboard</title>
        <link rel="stylesheet" href="student_dashboard.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css"/>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,400;0,500;0,600;0,800;1,900&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
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
        
        <main>
            <!-- Enrolled Courses Section -->
            <section id="enrolled-courses">
                <h2>Enrolled Courses</h2>
                <?php $user_id = $_SESSION['user_id'];

    // Fetch enrolled courses and enrollment date for the user
    $query = "SELECT c.course_title, c.course_credits, g.grade, e.enrollment_status, e.enrollment_date
          FROM Courses c
          JOIN Enrollments e ON c.course_id = e.course_id
          LEFT JOIN Grades g ON e.student_id = g.student_id AND e.course_id = g.course_id
          WHERE e.student_id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Format the enrolled courses with additional information
$enrolled_courses = '<table>';
$enrolled_courses .= '<tr><th>Course Title</th><th>Credits</th><th>Grade</th><th>Status</th><th>Enrollment Date</th></tr>';
while ($row = mysqli_fetch_assoc($result)) {
    $enrollmentStatus = ($row['grade'] >= 50) ? 'Completed' : (($row['grade'] === null) ? 'Enrolled' : 'Withdrawn');

    $enrolled_courses .= '<tr>';
    $enrolled_courses .= '<td>' . $row['course_title'] . '</td>';
    $enrolled_courses .= '<td>' . $row['course_credits'] . '</td>';
    $enrolled_courses .= '<td>' . (($row['grade'] !== null) ? $row['grade'] : '-') . '</td>';
    $enrolled_courses .= '<td>' . $enrollmentStatus . '</td>';
    $enrolled_courses .= '<td>' . $row['enrollment_date'] . '</td>';
    $enrolled_courses .= '</tr>';
}
$enrolled_courses .= '</table>';

echo $enrolled_courses; ?>
            </section>

            

            <!-- Credit Balance Section -->
            <section id="credit-balance">
                <h2>Credit Balance</h2>
                <?php include('credit_balance.php'); ?>
            </section>

            <!-- Profile Information Section -->
            <section id="profile-info">
                <h2>Profile Information</h2>
                <?php include('profile_info.php'); ?>
            </section>
        </main>

        <script src="student_dashboard.js"></script>
        <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>     
    </body>
    </html>
