<?php
session_start();
require_once 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    

    $query = "SELECT * FROM Users WHERE username ='$username'";
    $sql=mysqli_query($conn , $query);
    $result=mysqli_fetch_assoc($sql);
    

    if (mysqli_num_rows($sql) > 0) {
        $user = mysqli_fetch_assoc($sql);
        if ($result['password']==$password) {
            // Store user data in session
            $_SESSION['username'] = $username;
            $_SESSION['user_id'] = $result['user_id']; // Add user_id to the session
            $_SESSION['role'] = $result['role'];

            if ($result ['role'] == 'admin') {
                header("Location: admin.php"); // Redirect to admin dashboard
            } else if ($result['role']=='teacher'){
                header("Location: teacher_dashboard.php"); 
            }else{ header("location:student_dashboard.php");               
            }   
        } else {
            $_SESSION['message'] = 'Username or password is incorrect.';
            header("Location: login.php"); 
        }
    } 
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="login.css"> <!-- Include your stylesheet -->
    <title>Login</title>
</head>
<body>

    <div class="container">
        <div class="login-form">
            <h2>Login</h2>
            <form action="login.php" method="post">
                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit">Login</button>
                

            </form>
           
        </div>
    </div>
</body>
</html>
