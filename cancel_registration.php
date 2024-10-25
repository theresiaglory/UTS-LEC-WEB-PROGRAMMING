<?php
require 'db.php';
session_start();

if (!isset($_SESSION['userID'])) {
    header("Location: login.php");
    exit();
}
$userID = $_SESSION['userID'];

if (isset($_GET['eventID'])) {
    $eventID = intval($_GET['eventID']);
    $sql_resign = "DELETE FROM registrations WHERE userID = ? AND eventID = ?";
    $stmt = $conn->prepare($sql_resign);
    $stmt->bind_param("ii", $userID, $eventID);

    if ($stmt->execute()) {
        echo "<script>alert('You have successfully resigned from the event.'); window.location.href = 'dashboard.php';</script>";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
} else {
    echo "No event selected for resignation.";
}
$conn->close();
?>
