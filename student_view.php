<?php
session_start();

include('config.php');
if (!isset($_SESSION['user_email'])) {
    header('location:login.php');
}
if ($_SESSION['user_type'] != 'student') {
    header('location:login.php?alert=false');
}
$std_email = $_SESSION['user_email'];

if (isset($_POST['submit'])) {
    // Get form data
    $code = mysqli_real_escape_string($conn, $_POST['code']);

    $insert_query = "INSERT INTO std_courses (s_email, code) VALUES ('$std_email','$code')";

    if (mysqli_query($conn, $insert_query)) {
        // Success message
        $success = 'Class created successfully!';
        header("location: student_view.php");
        exit;
    } else {
        $error = 'Error creating class!';
    }
}

$stdQuery = "SELECT code FROM std_courses WHERE s_email = '$std_email'";
$stdResult = mysqli_query($conn, $stdQuery);

$codes = [];
while ($row = mysqli_fetch_assoc($stdResult)) {
    $codes[] = $row['code'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Page</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/font-awesome.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC"
          crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
            crossorigin="anonymous"></script>
    <link rel="stylesheet" href="css/home.css">

    <style>
        .content {
            text-align: left;
            margin-top: 20px;
        }

        .form-control {
            height: 40px;
        }

        .container-cards {
            display: flex;
            flex-wrap: wrap;
            margin-top: 18px;
        }

        .card {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            margin: 10px;
            width: calc(33.33% - 20px); /* 33.33% width with 10px margin on both sides */
        }

        .fa {
            font-size: 30px !important;
            letter-spacing: 30px;
        }

        .sidebar {
            padding: 20px;
            background-color: #f8f9fa;
            height: 100vh;
        }
        .home{
            height: 120px;
        }
        #loading-bar {
            height: 4px;
            background-color: #007bff; /* Loading bar color */
            position: fixed;
            top: 0;
            left: 0;
            width: 0;
            transition: width 0.5s ease;
        }

        body {
            overflow: hidden; /* Hide scrollbar during loading */
        }
    </style>
</head>

<body>
<body>
   
<nav class="navbar navbar-light bg-light" style="border-bottom: 1.5px solid #D6D5D4;">
    <div class="container-fluid">
        <a class="navbar-brand" href="#" style="margin-left: 12px;">
            <img src="assets/logo.png" alt="" width="86px" class="d-inline-block align-text-top">
            Classroom
        </a>
        <span class="navbar-text mr-2">
            <i class="fa fa-plus" data-toggle="modal" data-target="#addClassModal" style="cursor: pointer;"></i>
            <a href="student_profile.php"><i class="fa fa-gear"></a></i>
        </span>
    </div>
</nav>


<div class="container-fluid">
<div id="loading-bar"></div>

    <div class="row">
        <div class="col-md-3 sidebar" style="height: 150vh;">
            <h4>
                <div class="container home" style=" border-bottom: 1px solid #D6D5D4">
                    <ul style="list-style-type: none; margin-left:-40px; padding:">
                        <li style="text-align:left;">
                            <button class="btn btn-link menu-button"
                                    style="font-size:20px; text-transform: uppercase; font-weight: 600; padding-bottom: 12px;">
                                <i class="fa fa-home icon" style="margin-right: -20px;"></i><a
                                        href="teacher_view.php" style="text-decoration: none; color: black;">Home</a>
                            </button>
                        </li><br>
                        <li style="text-align:left;">
                            <button class="btn btn-link menu-button"
                                    style="font-size:20px; text-transform: uppercase; margin-top:-40px; font-weight: 600;">
                                <i class="fa fa-calendar icon" style="margin-right: -20px;"></i> Calendar
                            </button>
                        </li><br>
                    </ul>
                </div>

                <div class="archived" style="margin-top: 20px;">
                    <button class="btn btn-link ar-class" style="color: black; margin-top: -12px;"
                            id="enrolledCoursesBtn" onclick="toggleEnrolledCourses()">
                        <i class="fa fa-graduation-cap"
                           style="font-size: 20px !important; color: #007bff; margin-right:-25px; margin-left: -15px;"></i>
                        Enrolled Courses
                    </button>
                </div>
            </h4>
            <div id="enrolledCourses" style="display: none;">
                <?php
                  foreach ($codes as $code) {
                 $select_query = "SELECT * FROM classes WHERE room_no = '$code'";
                 $result = mysqli_query($conn, $select_query);
     
                 if (mysqli_num_rows($result) > 0) {
                // Assuming $activeClass is the name of the currently active class
                $activeSubject = isset($_GET['name']) ? $_GET['name'] : '';

                while ($row = mysqli_fetch_assoc($result)) {
                    $subject = $row['subject'];
                    $section = $row['section'];
                    $teacher = $row['t_email'];

                    $firstLetter = strtoupper(substr($subject, 0, 1));

                    // Check if the current class is the active class
                    $isActiveClass = ($subject === $activeSubject) ? 'active' : '';

                    echo '<div class="class-circle">';
                    echo '<div class="sideclass ' . $isActiveClass . '" onclick="changeBackgroundColor(this)">';
                    echo '<div class="circle-icon">' . $firstLetter . '</div>';
                    echo '<div class="class-info ">';
                    echo '<a style="text-decoration: none; color: black;" href="details.php?name=' . urlencode($subject) . '&teacher=' . urlencode($teacher) . '"><div ">' . $subject . '</div></a>';
                    echo '<div style="margin-top: 5px;" class="sec">' . $section . '</div>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                }
            }
        }
                ?>
            </div>
            <div class="bottom"
                 style="margin-top: 40px; border-top: 1px solid #D6D5D4; padding-top: 20px;"></div>
            <div class="archived" style="margin-top: 20px;">
                <i class="fa fa-archive"
                   style="font-size: 20px !important; color: #007bff; margin-right:-25px; margin-left: -15px;"></i>
                <a href="archived_class.php" class="ar-class">Archived Classes</a>
            </div><br>
            <div class="archived" style="margin-top: 20px;">
                <i class="fa fa-gear"
                   style="font-size: 20px !important; color: #007bff; margin-right:-25px; margin-left: -15px;"></i>
                <a href="student_profile.php" class="ar-class">Settings</a>
            </div>
            <script>
                function toggleEnrolledCourses() {
                    var enrolledCourses = document.getElementById('enrolledCourses');
                    enrolledCourses.style.display = (enrolledCourses.style.display === 'none' || enrolledCourses.style.display === '') ? 'block' : 'none';
                }
            </script>
        </div>

        <div class="col-md-9 main-content">
            <div class="content">
            <?php
// Include your database connection file (config.php)
include 'config.php';

echo '<div class="container-cards">'; 
foreach ($codes as $code) {
    $select_query = "SELECT * FROM classes WHERE room_no = '$code'";
    $result = mysqli_query($conn, $select_query);

    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $subject = $row['subject'];
            $teacher = $row['t_email'];

            $teacherQuery = "SELECT name FROM users WHERE email = '$teacher'";
            $teacherResult = mysqli_query($conn, $teacherQuery);
            $teacherRow = mysqli_fetch_assoc($teacherResult);
            $teacherName = $teacherRow['name'];

            echo '<div class="card" style="border-radius: 12px; position: relative;">';
            echo '<a href="details.php?name=' . urlencode($subject) . '&teacher=' . urlencode($teacher) . '"><img src="assets/class.jpg" class="card-img-top" alt="Class Image" style="border-radius: 12px; opacity: 0.8; z-index: 0;"></a>';
            echo '<div class="card-body" style="text-align: left; margin-top: -105px; margin-bottom: 40px; position: relative; z-index: 1;">';
            echo '<div class="card-settings" style="margin-left: 265px; margin-top: -40px;">
                <i class="fa fa-ellipsis-v" style="font-size: 8px; cursor: pointer; color: black;" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></i>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton"">
                    <a class="dropdown-item" href="archived.php?code='.$code.'"">Unenroll</a>
                </div>
            </div>';
            echo '<a href="details.php?name=' . urlencode($subject) . '&teacher=' . urlencode($teacher) . '" style="text-decoration: none; color: black;"><h4 class="card-title">' . $row['subject'] . '</h4></a>';
            echo '<p class="card-text">' . $row['section'] . '</p>';
            echo '<p class="card-text" style="margin-top: -20px;">' . $teacherName . '</p>';
            echo '</div>';
            echo '<div class="card-footer" style="background: rgba(255, 255, 255, 0.8); margin-bottom: 5px; z-index: 2;">';
            echo '<i class="fa fa-folder" style="margin-right: -300px; margin-top: 10px;"></i>';
            echo '</div>';
            echo '</div>';
        }
    }
}

// Close the foreach loop
echo '</div>';

// Display the image and button if no records are found
// Display the image and button if no records are found
if (empty($codes)) {
    echo '<div style="text-align: center; margin-top: 50px;">';
    echo '<img src="assets/women.jpg" width="300px" alt="">';
    echo '<p>Join a class to get started</p>';
    echo '<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addClassModal">
        Join Class
    </button>';
    echo '</div>';
}

// Close the database connection
$conn->close();
?>


                
                <!-- Modal -->
                <div class="modal fade" id="addClassModal" tabindex="-1" role="dialog"
                aria-labelledby="addClassModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addClassModalLabel">Add Class</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form method="post" action="">
                               
                                <div class="form-group">
                                    <label for="subject"></label>
                                    <input type="text" class="form-control" id="code" name="code" required
                                        placeholder="Enter the Code">
                                </div>
                                <button type="submit" class="btn btn-primary" style="margin-top: 15px;" id="submit"
                                    name="submit">Join</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
                </div>
            </div>
        </div>
    </div>
</div>



<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"
        integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n"
        crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"
    integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo"
    crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.min.js"
    integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6"
    crossorigin="anonymous"></script>

    <script>
        document.onreadystatechange = function () {
            var loadingBar = document.getElementById('loading-bar');

            function setProgressBar(width) {
                loadingBar.style.width = width + '%';
            }

            function hideLoadingBar() {
                loadingBar.style.display = 'none';
                document.body.style.overflow = 'visible'; // Restore scrollbar after loading
            }

            switch (document.readyState) {
                case 'loading':
                    setProgressBar(30);
                    break;
                case 'loading':
                    setProgressBar(50); 
                    break;
                case 'interactive':
                    setProgressBar(70);
                    break;
                case 'complete':
                    setProgressBar(100); 
                    setTimeout(hideLoadingBar, 1000); // Hide the loading bar after a short delay
                    break;
            }
        };

        // Simulate a delay before displaying the actual content
        setTimeout(function () {
            document.body.style.overflow = 'visible'; // Restore scrollbar before showing content
        }, 2000); // Adjust the delay time in milliseconds (e.g., 2000 = 2 seconds)
    </script>
<script src="https://use.fontawesome.com/fc6bfe99ee.js"></script>

</html>
