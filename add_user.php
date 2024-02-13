<?php
session_start();
require_once 'db_connection.php';

$username="";
$password="";
$email="";
$role="";

$errorMessage="";
$successMessage="";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password =$_POST['password'];
    $email = $_POST['email'];
    $role = $_POST['role'];

    do{
        if(empty($username) || empty($password) || empty($email) || empty($role)){
            $errorMessage="All the fields  are required";
            break;
        }
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        
        $query = "INSERT INTO Users (username, password, email, role) VALUES ('$username', '$hashed_password', '$email','$role')";
        $results=$conn->query($query);
        if(!$results){
            $errorMessage="invalid query: " . $connection->error;
            break;
        }

        $username="";
        $password="";
        $email="";
        $role="";

        $successMessage="Client added correctly";
        header("location:/website/admin.php");
        exit;

    }while(false);
}
    
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
        <h2>Add User</h2>
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


        <form  method="post">
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
                        <div class='alert alert-success alert-dismissible fade show' role='alert'>
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