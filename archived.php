<?php
session_start();

include('config.php');
if (!isset($_SESSION['user_email'])) {
    header('location:login.php');
}
if ($_SESSION['user_type'] != 'student') {
    header('location:login.php?alert=false');
}

$user = $_SESSION['user_email'];

if(isset($_GET['code'])){
$code = $_GET['code']; 
$select_query = "SELECT s_email FROM std_courses WHERE code = '$code'";
$result = $conn->query($select_query);

if ($result->num_rows > 0) {
    // Assuming there is only one record for a given 'code'
    $row = $result->fetch_assoc();
    // Delete the class using the provided 'code'
    $delete_query = "DELETE FROM std_courses WHERE code = '$code' AND s_email = '$user'";
    if ($conn->query($delete_query) === TRUE) {
        // If deletion is successful, insert into the 'archived' table
        $insert_query = "INSERT INTO archived (code, s_email) VALUES ('$code', '$user')";
        
        if ($conn->query($insert_query) === TRUE) {
            header("location: student_view.php");
        } else {
            echo "Error inserting record into archived: " . $conn->error;
        }
    } else {
        echo "Error deleting record from class: " . $conn->error;
    }
} else {
    echo "No records found for the given code in std_courses table";
}
}   else{
    $room_no = $_GET['enroll'];
    $select_query = "SELECT s_email FROM archived WHERE code = '$room_no'";
    $result = $conn->query($select_query);

if ($result->num_rows > 0) {
    // Assuming there is only one record for a given 'code'
    $row = $result->fetch_assoc();
    $s_email = $row['s_email'];

    // Delete the class using the provided 'code'
    $delete_query = "DELETE FROM archived WHERE code = '$room_no' AND s_email = '$user'";
    if ($conn->query($delete_query) === TRUE) {
        // If deletion is successful, insert into the 'archived' table
        $insert_query = "INSERT INTO std_courses (code, s_email) VALUES ('$room_no', '$s_email')";
        
        if ($conn->query($insert_query) === TRUE) {
            header("location: student_view.php");
        } else {
            echo "Error inserting record into archived: " . $conn->error;
        }
    } else {
        echo "Error deleting record from class: " . $conn->error;
    }
} 
}

$conn->close();
