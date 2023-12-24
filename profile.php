<?php
session_start();
include('config.php');

if (!isset($_SESSION['user_email'])) {
    header('location: login.php');
}

$userEmail = $_SESSION['user_email'];

// Fetch user information from the database
$selectUserQuery = "SELECT * FROM users WHERE email = ?";
$stmt_user = $conn->prepare($selectUserQuery);
$stmt_user->bind_param('s', $userEmail);
$stmt_user->execute();
$resultUser = $stmt_user->get_result();
$userData = $resultUser->fetch_assoc();

// Fetch classes created by the user
$selectClassesQuery = "SELECT * FROM classes WHERE t_email = ?";
$stmt_classes = $conn->prepare($selectClassesQuery);
$stmt_classes->bind_param('s', $userEmail);
$stmt_classes->execute();
$resultClasses = $stmt_classes->get_result();
$createdCourses = $resultClasses->num_rows;

// Fetch distinct students enrolled in the user's classes
$selectDistinctStudentsQuery = "SELECT DISTINCT s_email FROM std_courses";
$stmt_students = $conn->prepare($selectDistinctStudentsQuery);
// $stmt_students->bind_param('s');
$stmt_students->execute();
$resultStudents = $stmt_students->get_result();
$enrolledStudents = $resultStudents->num_rows;

$stmt_user->close();
$stmt_classes->close();
$stmt_students->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <style>
        .profile-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px;
            background-color: #f8f9fa;
            margin-bottom: 20px;
        }

        .user-info {
            display: flex;
            align-items: center;
        }

        .user-image {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            overflow: hidden;
            margin-right: 10px;
        }

        .user-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .user-details {
            display: flex;
            flex-direction: column;
        }

        .stats-container {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .stat-card {
            flex: 1;
            padding: 15px;
            border-radius: 8px;
            background-color: #e2f0ff;
            margin-right: 10px;
            text-align: center;
        }

        .classes-container {
            margin-top: 20px;
        }

        .class-circle {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 10px;
            padding: 10px;
            border: 1px solid #d1d1d1;
            border-radius: 10px;
        }

        .circle-icon {
            width: 40px;
            height: 40px;
            background-color: #007bff;
            color: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            margin-bottom: 5px;
        }

        .class-info {
            text-align: center;
            font-size: 16px;
            font-weight: 600;
        }

        .logout-btn {
            margin-top: 20px;
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
<nav class="navbar navbar-light bg-light" style="border-bottom: 1.5px solid #D6D5D4;">
    <div class="container-fluid">
        <a class="navbar-brand" href="#" style="margin-left: 12px;">
            <img src="assets/logo.png" alt="" width="86px" class="d-inline-block align-text-top">
            Classroom
        </a>
        <span class="navbar-text mr-2">
            <a href="profile.php"><i class="fa fa-gear"></a></i>
        </span>
    </div>
</nav>
<div id="loading-bar"></div>
    <div class="container">
        <div class="profile-container">
            <div class="user-info">
                <div class="user-image">
                    <img src="path/to/user-image.jpg" alt="User Image">
                </div>
                <div class="user-details">
                    <h4><?php echo $userData['name']; ?></h4>
                    <p><?php echo $userEmail; ?></p>
                </div>
            </div>
            <div class="options">
            <a href="teacher_view.php" class="btn btn-success">Go Home-></a>
            <a href="logout.php" class="btn btn-danger">Logout</a>
            </div>
        </div>

        <div class="stats-container">
            <div class="stat-card">
                <h5>Created Courses</h5>
                <p><?php echo $createdCourses; ?></p>
            </div>
            <div class="stat-card">
                <h5>Enrolled Students</h5>
                <p><?php echo $enrolledStudents; ?></p>
            </div>
        </div>

        <div class="classes-container">
            <h3>Your Classes</h3>
            <?php
            while ($classData = $resultClasses->fetch_assoc()) {
                $subject = $classData['subject'];
                $section = $classData['section'];
                $firstLetter = strtoupper(substr($subject, 0, 1));

                echo '<div class="class-circle">';
                echo '<div class="circle-icon">' . $firstLetter . '</div>';
                echo '<div class="class-info">';
                echo '<div>' . $subject . '</div>';
                echo '<div style="margin-top: 5px;">' . $section . '</div>';
                echo '</div>';
                echo '</div>';
            }
            ?>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>

</html>
