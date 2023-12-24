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
if ($_SERVER["REQUEST_METHOD"] == "POST") {
if (isset($_POST['submit'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];  // No hashing here
    $user_type = $_POST['user_type'];

    if (in_array($email, $allEmails)) {
        $error[] = 'Email already exists!';
    } else {
        $insert = "INSERT INTO users(name, email, password, role) VALUES('$name','$email','$password','$user_type')";
        mysqli_query($conn, $insert);
        header('location:login.php');
    }
}
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Form</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/font-awesome.min.css">

    <!-- Custom CSS file link  -->
    <link rel="stylesheet" href="css/style.css">
    <style>

        .form-btn {
            background-color: #4caf50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }

        .form-btn:hover {
            background-color: #45a049;
        }

        .password-strength, .email-availability {
            margin-top: 2px;
            font-size: 15px;
            text-align: left;
            color: green;
        }

        .emoji {
            font-size: 24px;
            margin-right: 5px;
        }

        .emoji-good {
            color: #4caf50;
        }

        .emoji-bad {
            color: #f44336;
        }

        .error{
         font-size: 15px;
         text-align: left;
         color: red;
         margin-left: 4px;
         margin-top: 2px;
        }
    </style>
</head>

<body>

    <div class="form-container">

        <form action="" method="post" onsubmit="return validateForm()">
            <h3>Register Now</h3>
            <?php
            if (isset($error)) {
                foreach ($error as $error) {
                    echo '<span class="error-msg">' . $error . '</span>';
                }
            };
            ?>
            <input type="text" name="name" required placeholder="Enter your name">
            <input type="email" name="email" required placeholder="Enter your email" oninput="checkEmailAvailability()">
            <div class="email-availability" id="email-availability"></div>
            <input type="password" name="password" required placeholder="Enter your password" oninput="checkPasswordStrength()">
            <div class="password-strength" id="password-strength"></div>
            <select name="user_type">
                <option value="teacher">Teacher</option>
                <option value="student">Student</option>
            </select>
            <input type="submit" name="submit" value="Register Now" class="form-btn">
            <p>Already have an account? <a href="login.php">Login Now</a></p>
        </form>

    </div>

    <script>
        function validateForm() {
            // You can add additional validation here if needed
            return true;
        }

        function checkEmailAvailability() {
            var email = document.getElementsByName('email')[0].value;

            // Check if the entered email already exists in the fetched emails
            if (<?php echo json_encode($allEmails); ?>.includes(email)) {
                document.getElementById('email-availability').innerHTML = '<span class="error">Email already exists!</span>';
            } else {
                document.getElementById('email-availability').innerHTML = 'Email Avalaible ';
            }
        }

        function checkPasswordStrength() {
            var password = document.getElementsByName('password')[0].value;
            var strength = 'Weak';

            if (password.length >= 6 && password.length < 8) {
                strength = 'Weak';
                updateStrengthUI(strength, '👎', 'emoji-bad');
            } else if (password.length >= 8 && password.length < 10) {
                strength = 'Good';
                updateStrengthUI(strength, '👍', 'emoji-good');
            } else if (password.length >= 10) {
                strength = 'Excellent';
                updateStrengthUI(strength, '🌟', 'emoji-good');
            } else {
                updateStrengthUI('', '', '');
            }
        }

        function updateStrengthUI(strength, emoji, emojiClass) {
            var strengthElement = document.getElementById('password-strength');
            strengthElement.innerHTML = '<span class="' + emojiClass + ' emoji">' + emoji + '</span>' + 'Password Strength: ' + strength;
        }
    </script>

</body>

</html>
