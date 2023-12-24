<?php

session_start();

@include 'config.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
if(isset($_POST['submit'])){

   $email = $_POST['email'];
   $pass = $_POST['password'];

   $select = " SELECT * FROM users WHERE email = '$email' && password = '$pass' ";

   $result = mysqli_query($conn, $select);

   if(mysqli_num_rows($result) > 0){
      $row = mysqli_fetch_assoc($result);

      $_SESSION['user_email'] = $email;  
      $_SESSION['pass'] = $pass; 
      
      if($row['role'] == 'student'){
         $_SESSION['user_type'] = 'student';
         header('location: student_view.php');
      }elseif($row['role'] == 'teacher'){
         $_SESSION['user_type'] = 'teacher';
         header('location: teacher_view.php');
      }else{
         $error[] = 'Invalid user role!';
      }

   }else{
      $error[] = 'Invalid login credentials!';
   }

}}


?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Login Form</title>

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">
 <style>
    .alert{
        color: green !important ;
        font-size: 18px !important;
        
    }
   </style>
</head>
<body>
   
<div class="form-container">

   <form action="" method="post">
      <h3>Login Now</h3>
      <?php
      if(isset($error)){
         foreach($error as $error){
            echo '<span class="error-msg">'.$error.'</span>';
         };
      };
    
      ?>
      <?php
  if(isset($_GET['alert']) && $_GET['alert'] == 'true') {
   echo '<span class="alert">Your Password has been Changed.</span>';
   }elseif(isset($_GET['alert']) && $_GET['alert'] == 'false'){
      echo '<span class="error-msg">Access denied!</span>';
   }

      ?>

      <input type="email" name="email" required placeholder="Enter your email">
      <input type="password" name="password" required placeholder="Enter your password">
      <input type="submit" name="submit" value="Login Now" class="form-btn">
      <p><a href="forget.php" style="font-size: 18px;">Forget Your Password?</a></p>
      <p>Don't have an account? <a href="index.php">Register now</a></p>
   </form>

</div>

</body>
</html>
