<?php
require 'db.php';

if (isset($_POST['event_id'])) {
    $eventID = $_POST['event_id'];
    $sql = "DELETE FROM events WHERE eventID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $eventID);
    if ($stmt->execute()) {
        echo "Event deleted successfully!";
    } else {
        echo "Error deleting event: " . $conn->error;
    }
    header("Location: event_management.php");
    exit;
} else {
    echo "No event ID provided!";
}
?>