<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $userID = $_POST['userID'];
    $sql = "DELETE FROM users WHERE userID = '$userID'";
    
    if ($conn->query($sql) === TRUE) {
        header("Location: user_management.php");
    } else {
        echo "Error deleting record: " . $conn->error;
    }
    $conn->close();
}
?>