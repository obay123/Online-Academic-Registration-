<?php
session_start();
require_once 'db_connection.php';

$course_id = "";
$course_title = "";
$course_description = "";
$course_credits = "";
$instructor_ids = array();
$language_ids = array();
$errorMessage = "";
$successMessage = "";

// Check if the course ID is provided in the URL
if (isset($_GET['id'])) {
    $editCourseID = $_GET['id'];

    // Fetch course details from the database
    $fetchCourseQuery = "SELECT * FROM Courses WHERE course_id = '$editCourseID'";
    $fetchCourseResult = mysqli_query($conn, $fetchCourseQuery);

    if ($fetchCourseResult && mysqli_num_rows($fetchCourseResult) > 0) {
        $courseData = mysqli_fetch_assoc($fetchCourseResult);

        // Assign fetched data to variables
        $course_id = $courseData['course_id'];
        $course_title = $courseData['course_title'];
        $course_description = $courseData['course_description'];
        $course_credits = $courseData['course_credits'];

        // Fetch associated instructors
        $fetchCourseInstructorsQuery = "SELECT ci.instructor_id, ci.language_id, u.username, l.language_name 
            FROM course_instructors ci
            INNER JOIN users u ON ci.instructor_id = u.user_id
            INNER JOIN languages l ON ci.language_id = l.language_id
            WHERE ci.course_id = '$editCourseID'";
        $fetchCourseInstructorsResult = mysqli_query($conn, $fetchCourseInstructorsQuery);

        if ($fetchCourseInstructorsResult) {
            while ($row = mysqli_fetch_assoc($fetchCourseInstructorsResult)) {
                $instructor_ids[] = $row['instructor_id'];
                $language_ids[] = $row['language_id'];
                $instructor_names[] = $row['username'];
                $language_names[] = $row['language_name'];
            }
        }
    } else {
        $errorMessage = "Course not found with ID: $editCourseID";
    }
} else {
    $errorMessage = "Course ID not provided in the URL.";
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $course_id = $_POST['course_id'];
    $course_title = $_POST['course_title'];
    $course_description = $_POST['course_description'];
    $course_credits = $_POST['course_credits'];

    // Check if the instructor and language arrays are provided
    if (isset($_POST['instructor_id']) && isset($_POST['language_id'])) {
        $instructor_ids = $_POST['instructor_id'];
        $language_ids = $_POST['language_id'];

        // Check for required fields
        if (
            empty($course_id) || empty($course_title) || empty($course_description) || empty($course_credits) ||
            empty($instructor_ids) || empty($language_ids)
        ) {
            $errorMessage = "All the fields are required.";
        } else {
            // Check if there are valid instructors
            $validInstructorIDs = array();
            foreach ($instructor_ids as $instructorID) {
                $checkInstructorQuery = "SELECT user_id FROM users WHERE user_id = $instructorID AND role = 'teacher'";
                $checkInstructorResult = mysqli_query($conn, $checkInstructorQuery);
                if (mysqli_num_rows($checkInstructorResult) == 1) {
                    $validInstructorIDs[] = $instructorID;
                } else {
                    $errorMessage = "Invalid instructor provided or some instructors are not teachers.";
                    break; // Exit the loop if any invalid instructor is found
                }
            }

            if (empty($validInstructorIDs)) {
                $errorMessage = "No valid instructors were provided or some instructors are not teachers.";
            } else {
                // Update Courses table
                $updateCourseQuery = "UPDATE Courses 
                        SET course_title = '$course_title', 
                            course_description = '$course_description', 
                            course_credits = '$course_credits' 
                        WHERE course_id = '$editCourseID'";

                $updateCourseResult = mysqli_query($conn, $updateCourseQuery);

                // Update course_instructors table
                if ($updateCourseResult) {
                    // Delete existing instructors for the course
                    $deleteInstructorsQuery = "DELETE FROM course_instructors WHERE course_id = '$editCourseID'";
                    mysqli_query($conn, $deleteInstructorsQuery);

                    // Check if the arrays are set before trying to use them
                    $newInstructorIDs = isset($_POST['instructor_id']) ? $_POST['instructor_id'] : [];
                    $newLanguageIDs = isset($_POST['language_id']) ? $_POST['language_id'] : [];

                    // Insert new instructors if arrays are not empty
                    if (!empty($newInstructorIDs) && !empty($newLanguageIDs)) {
                        foreach ($newInstructorIDs as $key => $instructorID) {
                            // Check if the instructor is already associated with the course
                            $checkInstructorQuery = "SELECT * FROM course_instructors WHERE course_id = '$editCourseID' AND instructor_id = '$instructorID'";
                            $checkInstructorResult = mysqli_query($conn, $checkInstructorQuery);

                            if (mysqli_num_rows($checkInstructorResult) == 0) {
                                $languageID = $newLanguageIDs[$key];
                                $insertInstructorQuery = "INSERT INTO course_instructors (course_id, instructor_id, language_id) 
                                            VALUES ('$editCourseID', '$instructorID', '$languageID')";
                                mysqli_query($conn, $insertInstructorQuery);
                            }
                        }
                    }

                    $successMessage = "Course details updated successfully.";
                } else {
                    $errorMessage = "Error updating course details: " . mysqli_error($conn);
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Course</title>
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container my-5">
        <h2>Edit Course</h2>

        <?php 
        if(!empty($errorMessage)){
            echo"
            <div class='alert alert-warning alert-dismissible fade show' role='alert'>
                <strong>$errorMessage</strong>
                <button type='button' class'btn-close'  data-bs-dismiss='alert' area-label='close'></button>
            </div>
            ";
        }
        if(!empty($successMessage)){
            echo"
            <div class='alert alert-success alert-dismissible fade show' role='alert'>
                <strong>$successMessage</strong>
                <button type='button' class'btn-close'  data-bs-dismiss='alert' area-label='close'></button>
            </div>
            ";
        }
        ?>

        <!-- Your form, corrected -->
        <form method="POST">
            <!-- Hidden input to pass course ID -->
            <input type="hidden" name="id" value="<?php echo $course_id; ?>">

            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Course ID</label>
                <div class="col-sm-6">
                    <input type="text" class="form-control" name="course_id" value="<?php echo $course_id; ?>" readonly>
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Course Title</label>
                <div class="col-sm-6">
                    <input type="text" class="form-control" name="course_title" value="<?php echo $course_title; ?>">
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Course Description</label>
                <div class="col-sm-6">
                    <input type="text" class="form-control" name="course_description" value="<?php echo $course_description; ?>">
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Course Credits</label>
                <div class="col-sm-6">
                    <input type="text" class="form-control" name="course_credits" value="<?php echo $course_credits; ?>">
                </div>
            </div> 

            <div class="instructor-inputs">
                <?php
                // Populate existing instructors
                if (!empty($instructor_ids)) {
                    foreach ($instructor_ids as $key => $instructor_id) {
                        $language_id = isset($language_ids[$key]) ? $language_ids[$key] : ''; // Check if the key exists
                        $language_name = isset($language_names[$key]) ? $language_names[$key] : ''; // Check if the key exists
                    
                        echo '
                            <div class="row mb-3">
                                <div class="col-sm-3">
                                    <input type="text" class="form-control" name="instructor_id[]" value="' . $instructor_id . '" placeholder="Instructor ID" required readonly>
                                </div>
                                <div class="col-sm-3">
                                    <select name="language_id[]" required>
                                        <option value="' . $language_id . '">' . $language_name . '</option>
                                        <!-- Add other language options as needed -->
                                    </select>
                                </div>
                                <div class="col-sm-1">
                                    <button type="button" class="btn btn-danger btn-remove-instructor">X</button>
                                </div>
                            </div>';
                    }
                    
                    }
                
                ?>
            </div>
            
            <button type="button" id="add-instructor">Add Instructor</button>
            
            <div class="row mb-3">
                <div class="offset-sm-3 col-sm-3 d-grid">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
                <div class="col-sm-3 d-grid">
                    <a class="btn btn-outline-primary" href="/website/admin.php" role="button">Cancel</a>
                </div>
            </div>
        </form>

        <script>
            document.getElementById('add-instructor').addEventListener('click', function () {
                var instructorInput = document.createElement('div');
                instructorInput.classList.add('instructor-input');
                instructorInput.innerHTML = `
                    <div class="row mb-3">
                        <div class="col-sm-3">
                            <input type="text" class="form-control" name="instructor_id[]" value="" placeholder="Instructor ID" required>
                        </div>
                        <div class="col-sm-3">
                            <select name="language_id[]" required>
                                <option value="1">Arabic</option>
                                <option value="2">English</option>
                                <option value="3">French</option>
                            </select>
                        </div>
                        <div class="col-sm-1">
                            <button type="button" class="btn btn-danger btn-remove-instructor">X</button>
                        </div>
                    </div>
                `;
                document.querySelector('.instructor-inputs').appendChild(instructorInput);
            });

            // Event delegation for dynamically added close buttons
            document.querySelector('.instructor-inputs').addEventListener('click', function (event) {
                if (event.target.classList.contains('btn-remove-instructor')) {
                    // Remove the parent row when the close button is clicked
                    event.target.closest('.row').remove();
                }
            });
        </script>
    </div>
</body>
</html>
