<?php
require 'db.php';
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['userID'])) {
    header("Location: login.php");
    exit();
}

$userID = $_SESSION['userID'];

if (isset($_GET['eventID'])) {
    $eventID = intval($_GET['eventID']);

    $sql_check_event = "SELECT * FROM events WHERE eventID = ? AND status = 'open'";
    $stmt = $conn->prepare($sql_check_event);
    $stmt->bind_param("i", $eventID);
    $stmt->execute();
    $result_check = $stmt->get_result();

    if ($result_check->num_rows === 0) {
        echo "This event is not available for registration.";
        exit();
    }

    $sql_register = "INSERT INTO registrations (userID, eventID) VALUES (?, ?)";
    $stmt = $conn->prepare($sql_register);
    $stmt->bind_param("ii", $userID, $eventID);

    if ($stmt->execute()) {
        echo "<script type='text/javascript'>
                alert('You have successfully registered for the event.');
                window.location.href = 'dashboard.php'; // Redirect to dashboard after acknowledgment
              </script>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "No event selected for registration.";
}
$conn->close();
?>
