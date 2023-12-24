<?php
@include 'config.php';

// Fetch all emails from the database
$selectAllEmails = "SELECT email FROM users";
$resultAllEmails = mysqli_query($conn, $selectAllEmails);

if (!$resultAllEmails) {
    die("Error fetching emails: " . mysqli_error($conn));
}

$allEmails = [];
while ($row = mysqli_fetch_assoc($resultAllEmails)) {
    $allEmails[] = $row['email'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>

    <!-- custom css file link  -->
    <link rel="stylesheet" href="css/style.css">
    <style>
        .alert {
            color: green !important;
            font-size: 18px !important;
        }
        .email-availability {
            font-size: 15px;
            text-align: left !important;
            color: red;
            margin-left: 4px;
            margin-top: 2px;
        }
    </style>
</head>
<body>

<div class="form-container">

    <form action="forget-password.php" method="post">
        <h3>Reset Password</h3>
        <?php
        if(isset($error)){
            foreach($error as $error){
                echo '<span class="error-msg">'.$error.'</span>';
            };
        };
        ?>

        <?php 
         $userEmail = isset($_GET['email']) && !empty($_GET['email']) ? $_GET['email'] : null;
        if(isset($_GET['newpass']) && $_GET['newpass'] == 'true') {
            echo '<p class="alert">OTP has been sent to Your email. Check your Inbox or spam/junk folder</p>';
            echo '<input type="number" name="otp" required placeholder="Enter OTP">';
            echo '<input type="email" value="'.$userEmail.'" name="email" style="display: none;">';
            echo '<input type="submit" name="verify" value="Verify OTP" class="form-btn">';
        } else {
            if(isset($_GET['alert']) && $_GET['alert'] == 'true') {
                echo '<p class="alert">Your OTP is verified. Enter your new Password.</p>';
                $userOTP = isset($_GET['otp']) && !empty($_GET['otp']) ? $_GET['otp'] : null;
                echo '<input type="number" value="'.$userOTP.'" name="otp" style="display: none;">';
                echo '<input type="email" value="'.$userEmail.'" name="email" style="display: none;">';
                echo '<input type="password" name="pass" required placeholder="Enter your new Password">';
                echo '<input type="submit" name="newPass" value="Reset Password" class="form-btn">';
            } else {
                echo '<input type="number" name="otp" style="display: none;">';
                echo '<input type="email" name="email" oninput="checkEmailAvailability()" required placeholder="Enter your email">';
                echo '<div id="email-availability" class="email-availability"></div>';
                echo '<input type="submit" name="submit" value="Send OTP" class="form-btn" id="sendOtpBtn" disabled>';
            }
        }
        ?>
    </form>
</div>

<script>
    function checkEmailAvailability() {
        var email = document.getElementsByName('email')[0].value;

        var emailExists = <?php echo json_encode(in_array($userEmail, $allEmails)); ?>;

        // Update the email availability message
        var emailAvailability = document.getElementById('email-availability');
        var sendOtpBtn = document.getElementById('sendOtpBtn');

        if (emailExists) {
            emailAvailability.innerHTML = '';
            sendOtpBtn.disabled = false;
        } else {
            emailAvailability.innerHTML = '<span class="error">Email Not Found!</span>';
            sendOtpBtn.disabled = true;
        }
    }
</script>
</body>
</html>
