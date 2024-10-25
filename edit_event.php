<?php
require 'db.php';

if (isset($_POST['event_id'])) {
    $eventID = $_POST['event_id'];

    $sql = "SELECT * FROM events WHERE eventID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $eventID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $event = $result->fetch_assoc();
    } else {
        echo "Event not found!";
        exit;
    }
} else {
    echo "No event selected!";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_event'])) {
    $name = $_POST['event_name'];
    $date = $_POST['event_date'];
    $time = $_POST['event_time'];
    $location = $_POST['event_location'];
    $description = $_POST['event_description'];
    $max_participants = $_POST['max_participants'];
    $status = $_POST['status'];

    $sql = "UPDATE events SET eventName = ?, date = ?, time = ?, location = ?, description = ?, max_participants = ?, status = ? WHERE eventID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssi", $name, $date, $time, $location, $description, $max_participants, $status, $eventID);

    if ($stmt->execute()) {
        echo "Event updated successfully!";
        header("Location: admin.php");
        exit;
    } else {
        echo "Error updating event: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Event</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <div class="form-container">
        <h2>Edit Event: <?php echo htmlspecialchars($event['eventName']); ?></h2>
        <form action="edit_event.php" method="post">
            <input type="hidden" name="event_id" value="<?php echo $eventID; ?>">

            <label for="event-name">Event Name:</label>
            <input type="text" id="event-name" name="event_name" value="<?php echo htmlspecialchars($event['eventName']); ?>" required>
            
            <label for="event-date">Date:</label>
            <input type="date" id="event-date" name="event_date" value="<?php echo $event['date']; ?>" required>
            
            <label for="event-time">Time:</label>
            <input type="time" id="event-time" name="event_time" value="<?php echo $event['time']; ?>" required>
            
            <label for="event-location">Location:</label>
            <input type="text" id="event-location" name="event_location" value="<?php echo htmlspecialchars($event['location']); ?>" required>
            
            <label for="event-description">Description:</label>
            <textarea id="event-description" name="event_description" required><?php echo htmlspecialchars($event['description']); ?></textarea>
            
            <label for="max-participants">Max Participants:</label>
            <input type="number" id="max-participants" name="max_participants" value="<?php echo $event['max_participants']; ?>" required>
            
            <label for="status">Event Status:</label>
            <select id="status" name="status" required>
                <option value="open" <?php if($event['status'] == 'open') echo 'selected'; ?>>Open</option>
                <option value="closed" <?php if($event['status'] == 'closed') echo 'selected'; ?>>Closed</option>
                <option value="canceled" <?php if($event['status'] == 'canceled') echo 'selected'; ?>>Canceled</option>
            </select>
            
            <button type="submit" name="update_event">Update Event</button>
        </form>
        <form action="event_management.php" method="get">
            <button type="submit" class="back-button">Back to Dashboard</button>
        </form>
    </div>
</body>
</html>
