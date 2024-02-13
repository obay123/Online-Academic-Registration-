
<?php
session_start();
require_once 'db_connection.php';


if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.html');
    exit;
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['submit'])) {
        $course_ID = $_POST['course_id'];
        $course_title = $_POST['course_title'];
        $course_description = $_POST['course_description'];
        $course_credits = $_POST['course_credits'];
        $major_id = $_POST['major_id'];
        $year_id = $_POST['year_id'];
        $semester_id = $_POST['semester_id'];

        // Check if the course with the same course_id already exists
        $checkCourseQuery = "SELECT course_id FROM Courses WHERE course_id = '$course_ID'";
        $checkCourseResult = mysqli_query($conn, $checkCourseQuery);

        if (mysqli_num_rows($checkCourseResult) == 0) {
            // The course doesn't exist; proceed with insertion

            // Your existing code for checking instructors and languages goes here.
            // Assuming you have arrays of instructor IDs and language IDs from your form

            if (isset($_POST['instructor_id']) && isset($_POST['language_id'])) {
                $instructorIDs = $_POST['instructor_id'];
                $languageIDs = $_POST['language_id'];

                // Insert course details into the Courses table
                $courseInsertQuery = "INSERT INTO Courses (course_id, course_title, course_description, course_credits, major_id, year_id, semester_id)
                    VALUES ('$course_ID', '$course_title', '$course_description', '$course_credits', '$major_id', '$year_id', '$semester_id')";

                if (mysqli_query($conn, $courseInsertQuery)) {
                    // Get the last inserted course_id
                    $lastCourseID = mysqli_insert_id($conn);

                    // Loop through valid instructors and languages
                    foreach ($instructorIDs as $key => $instructorID) {
                        $languageID = $languageIDs[$key];

                        // Insert instructor and language details into Course_Instructors
                        $courseInstructorInsertQuery = "INSERT INTO course_instructors (course_id, instructor_id, language_id)
                            VALUES ('$lastCourseID', '$instructorID', '$languageID')";

                        if (!mysqli_query($conn, $courseInstructorInsertQuery)) {
                            echo '<div class="error-message">Failed to add instructors and languages. Please try again.</div>';
                            header('Location: admin.php');
                            exit;
                        }
                    }

                    // Redirect to the admin page with a success message
                    echo '<div class="success-message">Course added successfully!</div>';
                    header('Location: admin.php');
                    exit;
                } else {
                    // Handle the error if course insertion fails
                    // You might want to log the error or show a message
                    echo '<div class="error-message">Failed to add the course. Please try again.</div>';
                    header('Location: admin.php'); // Redirect back to the admin page
                    exit;
                }
            } else {
                // Handle the case where no instructors and languages are provided
                echo '<div class="error-message">No instructors and languages provided.</div>';
                header('Location: admin.php'); // Redirect back to the admin page
                exit;
            }
        } else {
            // The course already exists
            echo '<div class="error-message">A course with the same ID already exists.</div>';
            header('Location: admin.php'); // Redirect back to the admin page
            exit;
        }
    }
}

if (isset($_POST['delete_course'])){
    $course_id=$_POST['course_id_to_delete'];
    $query="DELETE FROM courses WHERE course_id='$course_id'";
    $results=mysqli_query($conn,$query);
    if($results){
        echo'<div class="error-message"> course deleted successfully</div>';
    }else{
        echo'<div class="success-message"> course with ID' . $course_id .'is not found </div>';
    }
    header('location:admin.php');
    exit;

}


if (isset($_POST['add_user'])){
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $email = $_POST['email'];
    $role = $_POST['role'];    
    $query = "INSERT INTO Users (username, password, email, role) VALUES ('$username' ,'$password' ,'$email'  , '$role')";
    $results=mysqli_query($conn,$query);

    header("Location: admin.php");
    exit; 
} 



if (isset($_POST['delete'])) {

    $user_id = $_POST['user_id'];
    $query= "DELETE FROM users WHERE user_id = $user_id";
    $results= mysqli_query($conn,$query); 
    header('Location: admin.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['load_course_for_edit'])) {
        $course_id_to_edit = $_POST['edit_course_id'];
        $query = "SELECT * FROM courses WHERE course_id = '$course_id_to_edit'";
        $result = mysqli_query($conn, $query);

        if ($result && $row = mysqli_fetch_assoc($result)) {
            // Populate the form with the existing course details
            $course_title = $row['course_title'];
            $course_description = $row['course_description'];
            $course_instructor = $row['course_instructor_id'];
            $course_credits = $row['course_credits'];

            // Populate other course details here (major_id, year_id, semester_id)
        } else {
            // Course not found, display an error message
            echo '<div class="error-message">Course with ID ' . $course_id_to_edit . ' not found.</div>';
        }
    } elseif (isset($_POST['edit_course'])) {
        $course_id_to_edit = $_POST['edit_course_id'];
        $course_title = $_POST['course_title'];
        $course_description = $_POST['course_description'];
        $course_instructor = $_POST['course_instructor'];
        $course_credits = $_POST['course_credits'];

        // Update other course details here (major_id, year_id, semester_id)

        $query = "UPDATE courses 
                  SET course_title = '$course_title', course_description = '$course_description', course_instructor_id = '$course_instructor', course_credits = '$course_credits'
                  WHERE course_id = '$course_id_to_edit'";

        $result = mysqli_query($conn, $query);

        if ($result) {
            echo '<div class="message success">Course with ID ' . $course_id_to_edit . ' has been successfully updated.</div>';
        } else {
            echo '<div class="message error">Failed to update the course.</div>';
        }
    }
}
?>

