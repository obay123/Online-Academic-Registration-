<?php
session_start();
require_once 'db_connection.php';

$course_id = "";
$course_title = "";
$course_description = "";
$course_credits = "";
$instructor_ids = array();
$language_ids = array();
$major_id = "";
$year_id = "";
$semester_id = "";


$errorMessage = "";
$successMessage = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $course_id = $_POST["course_id"];
    $course_title = $_POST["course_title"];
    $course_description = $_POST["course_description"];
    $course_credits = $_POST["course_credits"];
    $major_id = $_POST["major_id"];
    $year_id = $_POST["year_id"];
    $semester_id = $_POST["semester_id"];
    

    // Check if the course_id already exists in the Courses table
    $checkCourseQuery = "SELECT course_id FROM Courses WHERE course_id = '$course_id'";
    $checkCourseResult = mysqli_query($conn, $checkCourseQuery);

    if (mysqli_num_rows($checkCourseResult) > 0) {
        $errorMessage = "Course with ID $course_id already exists.";
    } else {
        // Check if the instructor and language arrays are provided
        if (isset($_POST['instructor_id']) && isset($_POST['language_id'])) {
            $instructor_ids = $_POST['instructor_id'];
            $language_ids = $_POST['language_id'];

            // Check for required fields
            if (
                empty($course_id) || empty($course_title) || empty($course_description) || empty($course_credits) ||
                empty($major_id) || empty($year_id) || empty($semester_id) ||
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
                    }
                }

                if (empty($validInstructorIDs)) {
                    $errorMessage = "No valid instructors were provided.";
                } else {
                    // Check if instructors already exist for the given course_id
                    $existingInstructors = array();
                    foreach ($validInstructorIDs as $instructorID) {
                        $checkCourseInstructorQuery = "SELECT instructor_id FROM course_instructors WHERE course_id = '$course_id' AND instructor_id = '$instructorID'";
                        $checkCourseInstructorResult = mysqli_query($conn, $checkCourseInstructorQuery);
                        if (mysqli_num_rows($checkCourseInstructorResult) > 0) {
                            $existingInstructors[] = $instructorID;
                        }
                    }

                    if (!empty($existingInstructors)) {
                        $errorMessage = "Instructor(s) with ID(s) " . implode(', ', $existingInstructors) . " already exist(s) for the course.";
                    } else {
                        // Insert course details into the Courses table
                        $courseInsertQuery = "INSERT INTO Courses (course_id, course_title, course_description, course_credits, major_id, year_id, semester_id)
                            VALUES ('$course_id', '$course_title', '$course_description', '$course_credits', '$major_id', '$year_id', '$semester_id')";

                        if (mysqli_query($conn, $courseInsertQuery)) {
                            // Get the last inserted course_id
                            $lastCourseID = $course_id;

                            // Loop through valid instructors and languages
                            foreach ($validInstructorIDs as $key => $instructorID) {
                                $languageID = $language_ids[$key];

                                // Insert instructor and language details into Course_Instructors
                                $courseInstructorInsertQuery = "INSERT INTO course_instructors (course_id, instructor_id, language_id)
                                    VALUES ('$lastCourseID', '$instructorID', '$languageID')";

                                if (!mysqli_query($conn, $courseInstructorInsertQuery)) {
                                    $errorMessage = "Failed to add the instructors. Please try again.";
                                    echo "Error: " . mysqli_error($conn);
                                }
                            }

                            // Success message
                            $successMessage = "Course added successfully!";
                        } else {
                            $errorMessage = "Failed to add the course. Please try again.";
                            echo "Error: " . mysqli_error($conn);
                        }
                    }
                }
            }
        } else {
            $errorMessage = "No instructors and languages provided.";
        }
    }
}
?>

<!-- The rest of your HTML remains unchanged -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
    <body>
        <div class="container my-5">
        <h2>Add Course</h2>
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


      <!-- ... Previous HTML code ... -->

<!-- Your form, corrected -->
<form method="POST">
    <div class="row mb-3">
        <label class="col-sm-3 col-form-label">Course ID</label>
        <div class="col-sm-6">
            <input type="text" class="form-control" name="course_id" value="<?php echo $course_id; ?>">
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
   
    <div class="row mb-3">
        <label class="col-sm-3 col-form-label">Major ID</label>
        <div class="col-sm-6">
            <select name="major_id" id="major_id">
            <?php
        $majorQuery = "SELECT major_id, major_name FROM majors";
        $majorResult = mysqli_query($conn, $majorQuery);

        while ($majorRow = mysqli_fetch_assoc($majorResult)) {
            echo '<option value="' . $majorRow['major_id'] . '">' . $majorRow['major_name'] . '</option>';
        }
        ?>
                    </select>
                </div>
            </div>
    <div class="row mb-3">
        <label class="col-sm-3 col-form-label">Year ID</label>
        <div class="col-sm-6">
            <select name="year_id" id="year_id">
            <?php
        $yearQuery = "SELECT year_id, year_name FROM Years";
        $yearResult = mysqli_query($conn, $yearQuery);

        while ($yearRow = mysqli_fetch_assoc($yearResult)) {
            echo '<option value="' . $yearRow['year_id'] . '">' . $yearRow['year_name'] . '</option>';
        }
        ?>
            </select>
        </div>
    </div>
    <div class="row mb-3">
        <label class="col-sm-3 col-form-label">Semester ID</label>
        <div class="col-sm-6">
            <select name="semester_id" id="semester_id">
            <?php
        $semesterQuery = "SELECT semester_id, semester_name FROM Semesters";
        $semesterResult = mysqli_query($conn, $semesterQuery);
        while ($semesterRow = mysqli_fetch_assoc($semesterResult)) {
            echo '<option value="' . $semesterRow['semester_id'] . '">' . $semesterRow['semester_name'] . '</option>';
        }
        ?>
            </select>
        </div>
    </div>
    <div class="instructor-inputs">
        <!-- This is where instructors will be dynamically added -->
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



    </body>
</html>
