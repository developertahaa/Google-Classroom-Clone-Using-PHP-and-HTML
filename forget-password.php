<?php

function generateOTP() {
    // Generate a random six-digit number
    $otp = mt_rand(100000, 999999);
    return $otp;
}

function storeInDatabase($email, $otp) {
   include 'config.php';
    $sql = "INSERT INTO forget_password(email, temp_key) VALUES ('$email', '$otp')";
    $conn->query($sql);

    $conn->close();
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

if(isset($_POST['submit'])){
    $mail = new PHPMailer(true);

    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'your@gmail.com';
    $mail->Password = 'password'; //go to your google account and generate a password key
    $mail->SMTPSecure = 'ssl';
    $mail->Port = 465;

    $mail->setFrom('your@gmail.com');
    $recipientEmail = $_POST['email'];
    $mail->addAddress($recipientEmail);
    $mail->isHTML(true);
    $mail->Subject = 'Your OTP for Password Reset';
    $otp = generateOTP();
    $mail->Body = "Your OTP is: $otp";
    $mail->send();
        echo "<script> alert('Mail sent successfully'); </script>";
        storeInDatabase($recipientEmail, $otp);
        header("Location: forget.php?newpass=true&email=$recipientEmail");
      
}

function getStoredOTP($email) {
 
    include 'config.php';    
    $sql = "SELECT temp_key FROM forget_password WHERE email = '$email' AND status ='' ";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['temp_key'];
    } else {
        return false; // No OTP found
    }

    $conn->close();
}


if (isset($_POST['verify'])) {
    $userotp = $_POST['otp'];
    $recipientEmail = $_POST['email'];

    $storedOTP = getStoredOTP($recipientEmail);

    if ($storedOTP) {
        if ($userotp == $storedOTP) {
            echo "OTP verification successful. You can reset the password now.";

            header("Location: forget.php?alert=true&otp=$userotp&email=$recipientEmail");
            exit();
        } else {
            // Incorrect OTP
            echo "Incorrect OTP. Please try again.";
        }
    } else {
        // No stored OTP found for the user
        echo "Error: No OTP found for the specified email.";
    }
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['newPass'])) {
        $newPassword = $_POST['pass'];
        $recipientEmail = $_POST['email'];
        $otp = $_POST['otp'];
        include 'config.php';

        $status = 'verified';

        mysqli_query($conn, "UPDATE forget_password SET status = '$status' WHERE email = '$recipientEmail' AND temp_key = '$otp'");
        mysqli_query($conn, "UPDATE users SET password='$newPassword' WHERE email='$recipientEmail'");
        header("Location: login.php?alert=true");
        exit();

        $conn->close();
    }
}
?>