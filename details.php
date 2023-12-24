<?php
session_start();
include('config.php');

if (!isset($_SESSION['user_email'])) {
    header('location: login.php');
}
if(!isset($_SESSION['user_type']) == 'student'){
    header('location:login.php');
}
$subject = isset($_GET['name']) && !empty($_GET['name']) ? $_GET['name'] : null;
$email = isset($_GET['teacher']) && !empty($_GET['teacher']) ? $_GET['teacher'] : null;
$select_query = "SELECT section, room_no FROM classes WHERE t_email = '$email' AND subject = '$subject'";
$result = mysqli_query($conn, $select_query);

if ($result && mysqli_num_rows($result) > 0) {
    // Fetch the section
    $row = mysqli_fetch_assoc($result);
    $section = $row['section'];
    $code = $row['room_no'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/font-awesome.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Kdam+Thmor+Pro&family=Roboto+Serif:opsz@8..144&display=swap" rel="stylesheet">
  <style>
    
       .sidebar {
        border: 1px grey black;
        margin-left: -10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        padding: 20px;
        height: 100vh;
        padding-top: 60px;
        overflow-y: auto; /* Add this line to enable vertical scrollbar */
        max-height: calc(100vh - 60px); /* Limit the height of the sidebar */
    }


        .class-circle {
            display: flex;
            flex-direction: column;
            align-items: left;
            margin-bottom: 10px;
            padding: 10px;
        }
        .container{
            display: flex;
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
            text-align: left;
            margin-left: 50px;
            margin-top: -50px;
            font-size: 16px;
            font-weight: 600;
        }

        .main-content {
            padding: 20px;
        }

        .top-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        .code{
            text-align: center;
            font-style: italic;
            padding-top: 50px !important;
            padding:10px;
        }
        .sideclass,  .archived{
            border: 2px grey black;
            padding: 8px;
            padding-left: 10px;
            padding-top: 8px;
            border-radius: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            transition: all 0.5s ease;
        }
        .sideclass:hover{
            background-color: #007bff;
        }
        .add{
            border: 2px grey #007bff;
            height: 100vh;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            padding: 10px;
            padding-top: 20px;
        }
        .archived, .ar-class{
           text-decoration: none;
           font-size: 18px;
           color: black;
           font-weight: 600;
           text-align: center;
           transition: all 0.5s ease;
           height: 50px;
           padding-top: 12px;
        }
        .archived:hover{
            background-color: #007bff;
        }
        .ar-class:hover{
            color: black;
        }
        .task{
            padding: 10px;
        }
        #classCode{
            border: 2px dotted black; margin-top: 20px; padding: 6px; border-radius: 15px;
        }
        .fa{
            font-size: 30px !important;
            letter-spacing: 30px;
        }
        .announce{
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            border: 2px grey black;
            height: 70px;
            width: 100%;
            border-radius: 10px;
            padding-left: 80px;
            padding-top: 16px;
            padding-right: 15px;
        }
        .announce ,input{
            border-radius: 10px;
        }
        .sideclass.active {
         background-color: #007bff;
        color: #fff;
}
.active {
        background-color: #007bff !important;
        color: #fff !important;
    }

    .active .circle-icon {
        background-color: #fff !important;
        color: black;   
        font-weight: 600;
    }

    .active .class-info {
        color: #007bff !important;
    }
    .active .sec{
        color: white;
    }
    .setting{
        margin-left: 132vh;
        
    }
    .fa-bars{
        margin-top: -500px !important;
        color: black;
    }
       .menu-button {
        color: black;
        text-decoration: none;
        padding: 0;
        font-size: 16px;
    }

    .menu-button:hover {
        background-color: transparent;
    }

    .menu-button i {
        margin-right: 5px;
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
            <a class="navbar-brand" href="#">
                <img src="assets/logo.png" alt="" width="86px" class="d-inline-block align-text-top">
                Classroom
            </a>
            <span class="navbar-text mr-2">
                <i class="fa fa-plus"></i>
                <a href="profile.php"><i class="fa fa-gear"></a></i>
                 </span>
        </div>
    </nav>
    <div id="loading-bar"></div>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar (3 columns) -->
           <div class="col-md-3 sidebar">
    <h4>
<div class="container home" style="height: 80px; border-bottom: 1px solid #D6D5D4">
    <ul style="list-style-type: none; margin-left:-40px;">
        <li style="text-align:left;">
            <button class="btn btn-link menu-button" style="font-size:20px; text-transform: uppercase; margin-top: -50px; font-weight: 600;">
                <i class="fa fa-home icon" style="margin-right: -20px;"></i><a href="teacher_view.php" style="text-decoration: none; color: black;">Home</a> 
            </button>
        </li><br>
       <li style="text-align:left;">
            <button class="btn btn-link menu-button" style="font-size:20px; text-transform: uppercase; margin-top:-60px; font-weight: 600;">
                <i class="fa fa-calendar icon" style="margin-right: -20px;"></i> Calendar
            </button>
        </li><br>
    </ul>
</div>

        <div class="archived" style="margin-top: 35px;">
        <button class="btn btn-link ar-class" style="color: black; margin-top: -12px;" id="enrolledCoursesBtn" onclick="toggleEnrolledCourses()">
           <i class="fa fa-graduation-cap"  style="font-size: 20px !important; color: #007bff; margin-right:-25px; margin-left: -15px;"></i> Enrolled Courses
        </button>
        </div>
    </h4>
    <div id="enrolledCourses" style="display: none;">
        <?php
        // Fetch and display classes from the database
        $select_query = "SELECT * FROM classes";
        $result = mysqli_query($conn, $select_query);

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
        ?>
    </div>
    <div class="bottom"style="margin-top: 40px; border-top: 1px solid #D6D5D4; padding-top: 20px;"></div>
    <div class="archived" >
        <i class="fa fa-archive" style="font-size: 20px !important; color: #007bff; margin-right:-25px; margin-left: -15px;"></i>
        <a href="archived_class.php" class="ar-class">Archived Classes</a>
    </div><br>
    <div class="archived" >
        <i class="fa fa-gear"  style="font-size: 20px !important; color: #007bff; margin-right:-25px; margin-left: -15px;"></i>
        <a href="" class="ar-class">Settings</a>
    </div>

    <script>
        function toggleEnrolledCourses() {
            var enrolledCourses = document.getElementById('enrolledCourses');
            enrolledCourses.style.display = (enrolledCourses.style.display === 'none' || enrolledCourses.style.display === '') ? 'block' : 'none';
        }
    </script>
</div>

            <script>
    function changeBackgroundColor(element) {
        // Remove 'active' class from all sideclass elements
        var classCircles = document.querySelectorAll('.sideclass');
        classCircles.forEach(function (circle) {
            circle.classList.remove('active');
        });

        // Add 'active' class to the clicked element
        element.classList.add('active');
    }
</script>




            <!-- Main Content (9 columns) -->
            <div class="col-md-9 main-content">
                <img src="assets/back.jpg" style="opacity: 0.8; z-index: 0;" alt="Top Image" class="top-image">
                <div style="margin-top:-100px; padding-left: 20px; z-index: 1;  position: relative;">
              <?php  $subject = isset($_GET['name']) && !empty($_GET['name']) ? $_GET['name'] : null;?>
                <strong><h1 style="letter-spacing: 2px; font-weight: 630; margin-bottom:-5px;"><?php echo $subject; ?></h1></strong> 
                <h2><?php echo $section; ?></h2>
                </div>
               <div class="container task">
               <div class="col-md-3 code" style="border: 2px grey black; border-radius: 10px; height: 250px; margin-right:18px; margin-left: -8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);">
                    <h4>Classroom <br> Code</h4>
                    <div id="classCode" style="" onclick="copyCodeToClipboard()">
                    <h2 style="color:#007bff; cursor: pointer;"><?php echo $code; ?></h2>
                    </div>  
                </div>
       
                    <div class="col-md-9 add">
                        <div class="announce">
                            <input type="text" class="form-control " placeholder="announce something to class">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
    function copyCodeToClipboard() {
        var codeElement = document.getElementById('classCode');
        var codeText = codeElement.innerText || codeElement.textContent;
        var input = document.createElement('textarea');
        input.value = codeText;
        document.body.appendChild(input);
        input.select();
        document.execCommand('copy');
        document.body.removeChild(input);
        alert('Code copied to clipboard!');
    }
</script>

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
<script src="https://use.fontawesome.com/fc6bfe99ee.js"></script>

</html>
