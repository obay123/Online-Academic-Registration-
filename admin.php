<?php
session_start();

require_once 'db_connection.php';
if (isset($_POST['filter_courses'])) {
    $year = $_POST['year_filter'];
    $semester = $_POST['semester_filter'];
    $major = $_POST['major_filter'];

    $query = "SELECT courses.course_id, course_title FROM courses 
              JOIN majors ON courses.major_id = majors.major_id
              JOIN years ON courses.year_id = years.year_id
              JOIN semesters ON courses.semester_id = semesters.semester_id
              WHERE 
              (courses.year_id = $year OR '$year' = '') AND
              (courses.semester_id = $semester OR '$semester' = '') AND
              (courses.major_id = $major OR '$major' = '')";
    
    $result = mysqli_query($conn, $query);

    if ($result) {
        $filteredCourses = mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="admin_dashboard.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script>
        // Function to set the active tab based on the value in local storage
        function setActiveTab() {
            // Get the tab name from local storage
            const activeTab = localStorage.getItem('activeTab');

            // If there's an active tab in local storage, set it as active
            if (activeTab) {
                const tabContent = document.getElementById(activeTab);
                if (tabContent) {
                    tabContent.style.display = 'block';
                }
            }
        }

        // Function to handle tab clicks and update local storage
        function openTab(evt, tabName) {
            const tabContent = document.getElementById(tabName);
            if (tabContent) {
                const tabcontents = document.getElementsByClassName('tabcontent');
                for (const content of tabcontents) {
                    content.style.display = 'none';
                }
                tabContent.style.display = 'block';
                localStorage.setItem('activeTab', tabName); // Store active tab in local storage
            }
        }

        // When the page loads, set the active tab
        window.addEventListener('load', setActiveTab);
    </script>
    
</head>
<body>       
    <header>
        <div class="container"> 
      <img src="download.png" alt="Academic Registration Logo" class="logo">
      <nav>
        <nav>
            <ul>
                
               <li><a href="homepage.php"><ion-icon name="home-outline"></ion-icon>Home</a></li>
                <li><a href="logout.php"><ion-icon name="power-outline"></ion-icon>Logout</a></li>
            </ul>
        </nav>
    </header>
             
    <main>
        <div class="tab">
           
            <button class="tablinks" onclick="openTab(event, 'courses')"> <ion-icon name="book-outline"></ion-icon> Manage Courses</button>
            <button class="tablinks" onclick="openTab(event, 'users')"><ion-icon name="people-outline"></ion-icon>Manage Users</button>    
            <button class="tablinks" onclick="openTab(event, 'enrollments')"><ion-icon name="add-circle-outline"></ion-icon>Enrollments</button>
        </div> 
        
        <div id="courses" class="tabcontent">
    <div class="container my-5">
       
        <a class="btn btn-primary" href="Add_course.php" role="button">New Course</a>

        <div class="my-3">
            <label for="searchCourse" class="form-label"><h3>Search Course by ID:</h3></label>
            <input type="text" class="form-control" id="searchCourse" oninput="searchCourse()" placeholder="Enter Course ID">
            <script>
    function searchCourse() {
        let input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("searchCourse");
        filter = input.value.toUpperCase();
        table = document.querySelector(".table");
        tr = table.getElementsByTagName("tr");
        for (i = 0; i < tr.length; i++) {
            td = tr[i].getElementsByTagName("td")[0]; // Change index if the Course ID column is different
            if (td) {
                txtValue = td.textContent || td.innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                } else {
                    tr[i].style.display = "none";
                }
            }
        }
    }
</script>
        </div>

        <!-- Add dropdown for major selection -->
        <div class="my-3">
            <label for="majorFilter" class="form-label"><h3>Filter by Major:</h3></label>
            <select class="form-select" id="majorFilter" onchange="filterByMajor()">
                <option value="">All Majors</option>
                <?php
                // Fetch majors from your database and populate the options
                $majorQuery = "SELECT major_id, major_name FROM majors";
                $majorResult = mysqli_query($conn, $majorQuery);

                while ($majorRow = mysqli_fetch_assoc($majorResult)) {
                    echo '<option value="' . $majorRow['major_id'] . '">' . $majorRow['major_name'] . '</option>';
                }
                ?>
            </select>
        </div>

        <br>
        
<table class="table">
            <thead>
                <tr>
                    <th>Course ID</th>
                    <th>Course Title</th>
                    <th>Credits</th>
                    <th>Instructors</th>
                    <th>Language</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Modify the SQL query to include major_id
                $sql = "SELECT c.course_id, c.course_title, c.course_credits, c.major_id, GROUP_CONCAT(CONCAT(u.user_id, ' : ', u.username) SEPARATOR '-') as instructors, GROUP_CONCAT(CONCAT(l.language_id, ' :', l.language_name) SEPARATOR '-') as languages
                        FROM courses c
                        LEFT JOIN course_instructors ci ON c.course_id = ci.course_id
                        LEFT JOIN users u ON ci.instructor_id = u.user_id
                        LEFT JOIN languages l ON ci.language_id = l.language_id
                        GROUP BY c.course_id, c.major_id";
                $results = $conn->query($sql);

                if (!$results) {
                    die("Invalid query " . $conn->error);
                }

                while ($row = $results->fetch_assoc()) {
                    echo "
                    <tr data-major-id='$row[major_id]'>
                        <td>$row[course_id]</td>
                        <td>$row[course_title]</td>
                        <td>$row[course_credits]</td>
                        <td title='Instructor IDs and Names: $row[instructors]'>$row[instructors]</td>
                        <td title='Language IDs and Names: $row[languages]'>$row[languages]</td>
                        <td>
                            <a class='btn btn-primary btn-sm' href='/website/edit_course.php?id=$row[course_id]'>Edit</a>
                            <a class='btn btn-danger btn-sm' href='/website/delete_course.php?id=$row[course_id]'>Freeze</a>
                        </td>
                    </tr>
                    ";
                }
                ?>
            </tbody>
        </table>
    </div>
            </div>

    <script>
        // Add JavaScript function to filter by major
        function filterByMajor() {
            var filterValue = document.getElementById('majorFilter').value;
            var rows = document.querySelectorAll('tbody tr');

            rows.forEach(function (row) {
                var majorId = row.getAttribute('data-major-id');
                if (filterValue === '' || majorId === filterValue) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }
    </script>


<div id="users" class="tabcontent">
    <div class="container my-5">
        <a class="btn btn-primary" href="Add_user.php" role="button">New User</a>
        <br>
        <div class="mb-3">
            <label for="userSearch" class="form-label"><h3>Search User by ID:</h3></label>
            <input type="text" class="form-control" id="userSearch" oninput="searchUsers()" placeholder="Enter User ID">
        </div>
        <script>
            function searchUsers() {
                const input = document.getElementById("userSearch");
                const searchValue = input.value.toLowerCase();

                const rows = document.querySelectorAll("table tbody tr");

                rows.forEach(row => {
                    const userID = row.querySelector("td:first-child").textContent.toLowerCase();
                    if (userID.includes(searchValue)) {
                        row.style.display = "";
                    } else {
                        row.style.display = "none";
                    }
                });
            }
        </script>
    </div>

    <!-- Tabs for different user roles -->
    <div class="mb-3">
            <a href="?role=students" class="btn btn-secondary">Students</a>
            <a href="?role=teachers" class="btn btn-secondary">Teachers</a>
            <a href="?role=admins" class="btn btn-secondary">Admins</a>
        </div>
        <?php
        $role = isset($_GET['role']) ? $_GET['role'] : 'students';

        switch ($role) {
            case 'students':
                include 'students_table.php';
                break;
            case 'teachers':
                include 'teachers_table.php';
                break;
            case 'admins':
                include 'admins_table.php';
                break;
            default:
                include 'students_table.php';
                break;
        }
        ?>
    </div>
</div>

                    <!-- enrollments -->
        <div id="enrollments" class="tabcontent">
            <section class="Enrollments">
                <h2>Enroll Students</h2>
                
                <form id="enrollment-form" method="post" action="admin.php">
                <div class="filter-options">
                <label for="year_filter">Year Filter:</label>
                <select name="year_filter" id="year_filter">
   
                       
                        <?php
                        $yearQuery = "SELECT year_id, year_name FROM Years";
                        $yearResult = mysqli_query($conn, $yearQuery);

                        while ($yearRow = mysqli_fetch_assoc($yearResult)) {
                            echo '<option value="' . $yearRow['year_id'] . '">' . $yearRow['year_name'] . '</option>';
                        }
                        ?>
                    </select>

                    <label for="semester_filter">Semester Filter:</label>
                    <select name="semester_filter" id="semester_filter">
                        
                        <?php
                        $semesterQuery = "SELECT semester_id, semester_name FROM Semesters";
                        $semesterResult = mysqli_query($conn, $semesterQuery);
                        while ($semesterRow = mysqli_fetch_assoc($semesterResult)) {
                            echo '<option value="' . $semesterRow['semester_id'] . '">' . $semesterRow['semester_name'] . '</option>';
                        }
                        ?>
                    </select>

                    <label for="major_filter">Major Filter:</label>
                    <select name="major_filter" id="major_filter">
                      
                        <?php
                        $majorQuery = "SELECT major_id, major_name FROM Majors";
                        $majorResult = mysqli_query($conn, $majorQuery);

                        while ($majorRow = mysqli_fetch_assoc($majorResult)) {
                            echo '<option value="' . $majorRow['major_id'] . '">' . $majorRow['major_name'] . '</option>';
                        }
                        ?>
                    </select>

                    <button type="submit" name="filter_courses">Filter Courses</button>
                    </div>
                    </form>
                    </section>
                <!--course selection and enrolments-->
            
            <div class="course-selection">
                <section>
                <h2>Filtered Courses</h2>
                <form id="enroll-course-form" method="post" acton="admin.php">
                    <?php
                    if (isset($filteredCourses) && count($filteredCourses) > 0) {
                        foreach ($filteredCourses as $course) {
                            echo '<label>';
                            echo '<input type="checkbox" name="course_id[]" value="' . $course['course_id'] . '">';
                            echo $course['course_title'];
                            echo '</label>';
                        }
                    } else {
                        echo '<div class="message error">No courses match the selected filters.</div>';
                    }
                    ?>
                    <div class="student-enrollment">
                    <label for="student_id">Student ID:</label>
                    <input type="text" name="student_id" id="student_id" required>
                    <button type="submit" name="enroll_student">Enroll</button>
                </form>
                </section>
                </div>
                    
                <?php
if (isset($_POST['enroll_student'])) {
    $student_id = $_POST['student_id'];
    $selected_courses = isset($_POST['course_id']) ? $_POST['course_id'] : [];

    // Check if the student exists
    $checkStudentQuery = "SELECT * FROM users WHERE role='student' AND user_id = '$student_id'";
    $checkStudentResult = mysqli_query($conn, $checkStudentQuery);

    if (mysqli_num_rows($checkStudentResult) === 0) {
        echo '<div class="message error">Student with ID ' . $student_id . ' does not exist in the database or a student.</div>';
    } else {
        // Proceed with enrollment
        $enrollmentDate = date("Y-m-d H:i:s");  // Get the current timestamp
        // Create an array of quoted course IDs for the SQL query
        $quotedCourseIds = array_map(function ($courseId) use ($conn) {
            return "'" . mysqli_real_escape_string($conn, $courseId) . "'";
        }, $selected_courses);
        $courseIds = implode(',', $quotedCourseIds);

        $updateEnrollmentQuery = "UPDATE enrollments 
                                  SET enrollment_status = 'enrolled', enrollment_date = '$enrollmentDate' 
                                  WHERE student_id = '$student_id' AND course_id IN ($courseIds)";
        $updt = mysqli_query($conn, $updateEnrollmentQuery);

        foreach ($selected_courses as $course_id) {
            // Check if the student is already enrolled in this course
            $checkEnrollmentQuery = "SELECT * FROM enrollments WHERE student_id = '$student_id' AND course_id = '$course_id'";
            $checkEnrollmentResult = mysqli_query($conn, $checkEnrollmentQuery);

            if (mysqli_num_rows($checkEnrollmentResult) === 0) {
                // Student is not enrolled in this course; enroll them
                $enrollStudentQuery = "INSERT INTO enrollments (student_id, course_id) VALUES ('$student_id', '$course_id')";
                $enrollStudentResult = mysqli_query($conn, $enrollStudentQuery);

                if ($enrollStudentResult) {
                    echo '<div class="message success">Enrollment successful for Student ID ' . $student_id . ' in Course ID ' . $course_id . '</div>';
                } else {
                    echo '<<div class="message error">Enrollment failed for Student ID ' . $student_id . ' in Course ID ' . $course_id . '</div>';
                }
            } else {
                echo '<div class="message error">Student with ID ' . $student_id . ' is already enrolled in Course ID ' . $course_id . '</div>';
            }
        }
    }
}
?>

</div>
    </main>
    <script>
    document.getElementById('add-instructor').addEventListener('click', function () {
        var instructorInput = document.createElement('div');
        instructorInput.classList.add('instructor-input');
        instructorInput.innerHTML = `
            <input type="text" name="instructor_id[]" placeholder="Instructor ID" required>
            <select name="language_id[]" required>
                <option value="1">Arabic</option>
                <option value="2">English</option>
                <option value="3">French</option>
            </select>
        `;
        document.querySelector('.instructor-inputs').appendChild(instructorInput);
    });
</script>

   
<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

</body>
</html>
