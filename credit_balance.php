<?php
//session_start();
require_once 'db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Query to retrieve the total credits for the logged-in user
$query = "SELECT SUM(c.course_credits) AS credit_balance
          FROM Courses c
          JOIN Enrollments e ON c.course_id = e.course_id
          WHERE e.student_id = ? AND e.enrollment_status = 'completed'";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Get the credit balance value
$credit_balance = ($row = mysqli_fetch_assoc($result)) ? $row['credit_balance'] : 0;

echo '<p>Credit Balance: ' . $credit_balance . ' credits</p>';


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Credit Balance</title>
    <link rel="stylesheet" href="student_dashboard.css"> <!-- Include your student dashboard CSS here -->
</head>
<body>
    <div class="container">
        <h1>Credit Balance</h1>
        <p>Total Credits: <?php echo $credit_balance; ?></p>
    </div>
</body>
</html>

