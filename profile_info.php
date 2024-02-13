<?php
//session_start();
require_once 'db_connection.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch the user's profile information
$query = "SELECT username, email FROM users WHERE user_id = ?";
$stmt = mysqli_prepare($conn,$query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);

$username = $row['username'];
$email = $row['email'];

// Format the profile information
$profile_info = '<p><strong>Username:</strong> ' . $username . '</p>';
$profile_info .= '<p><strong>Email:</strong> ' . $email . '</p>';

echo $profile_info;

mysqli_close($conn);

?>
