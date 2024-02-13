<?php
session_start();
require_once 'db_connection.php';

$id = "";
$username = "";
$email = "";
$password = "";
$role = "";

$errorMessage = "";
$successMessage = "";

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (!isset($_GET["id"])) {
        header("location:/website/admin.php");
        exit;
    }

    $id = $_GET["id"];

    // You should fetch the user's data from the database within this block.
    $sql = "SELECT * FROM users WHERE user_id = $id";
    $results = $conn->query($sql);
    $row = $results->fetch_assoc();

    if (!$row) {
        header("location:/website/admin.php");
        exit;
    }

    $username = $row['username'];
    $email = $row['email'];
    $password = $row['password'];
    $role = $row['role'];
} else {
    $id = $_POST["id"];
    $username = $_POST["username"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $role = $_POST["role"];

    do {
        if (empty($id) || empty($username) || empty($email) || empty($password) || empty($role)) {
            $errorMessage = "All the fields are required";
            break;
        }

        // Fix the SQL UPDATE statement. You had a typo in the WHERE clause.
        $sql = "UPDATE users SET username='$username', email='$email', password='$password', role='$role' WHERE user_id=$id";

        $results = $conn->query($sql);

        if (!$results) {
            $errorMessage = "Invalid query " . $conn->error;
            break;
        }

        $successMessage = "User updated correctly";
        header("location:admin.php");
        exit;
       

    } while (false); // The 'do-while' loop should be properly closed.

}

// You may want to show your HTML form for editing user details here.
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
    <body>
        <div class="container my-5">
        <h2>Edit User</h2>
<?php 
if(!empty($errorMessage)){
    echo"
    <div class='alert alert-warning alert-dismissible fade show' role='alert'>
    <strong>$errorMessage</strong>
    <button type='button' class'btn-close'  data-bs-dismiss='alert' area-label='close'></button>
    </div>
    ";
}
?>


        <form  method="POST">
            <input type="hidden"  name="id" value="<?php echo $id; ?>">
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Username</label>
                <div class="col-sm-6">
                    <input type="text" class="form-control" name="username" value="<?php echo $username; ?>">
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Password</label>
                <div class="col-sm-6">
                    <input type="text" class="form-control" name="password" value="<?php echo $password; ?>">
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Email</label>
                <div class="col-sm-6">
                    <input type="text" class="form-control" name="email" value="<?php echo $email; ?>">
                </div>
            </div>
            
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Role</label>
                <div class="col-sm-6">
                <select name="role">
                        <option value="admin">Admin</option>
                        <option value="teacher">Teacher</option>
                        <option value="Student">Student</option>
                    </select><br> 

                    <?php 
            if(!empty($successMessage)){
                echo"
                <div class='row mb-3>
                    <div class='offset-sm-3 col-sm-3 d-grid'>
                        <div class='alert alert-success alert-dismissible fade show' role='aler'>
                        <strong>$successMessage</strong>
                        <button type='button'   class='btn-close' data-bs-dismiss='alert' aria-label></button>
                        </div>
                    </div>
                </div>
                ";
            }
            ?>  
                   
            <div class="row mb-3">
                <div class="offset-sm-3 col-sm-3 d-grid">
                <button type="submit" class="btn btn-primary">Submit</button>
                </div>
                <div clas="col-sm-3 d-grid">
                    <a class="btn btn-outline-primary" href="/website/admin.php" role="button">Cacnel</a>    
                </div>
            </div> 
        </form>
        </div>
        </body>
        </html>