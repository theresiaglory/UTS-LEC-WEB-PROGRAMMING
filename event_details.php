<?php
require 'db.php';
session_start();

if (!isset($_SESSION['userID'])) {
    header("Location: login.php");
    exit();
}

$userID = $_SESSION['userID'];
if (isset($_GET['eventID'])) {
    $eventID = $_GET['eventID'];
    $sql_event = "SELECT * FROM events WHERE eventID = ?";
    $stmt = $conn->prepare($sql_event);
    $stmt->bind_param("i", $eventID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $event = $result->fetch_assoc();
    } else {
        echo "Event not found.";
        exit();
    }

    $sql_check_registration = "SELECT * FROM registrations WHERE userID = ? AND eventID = ?";
    $stmt_check = $conn->prepare($sql_check_registration);
    $stmt_check->bind_param("ii", $userID, $eventID);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();
    $isRegistered = $result_check->num_rows > 0;
} else {
    echo "No event ID provided.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Details</title>
    <link rel="stylesheet" href="event_details.css">
</head>
<body>

<div class="event-details-container">
    <h1><?php echo htmlspecialchars($event['eventName']); ?></h1>

    <?php if (!empty($event['image'])): ?>
        <img src="uploads/<?php echo htmlspecialchars($event['image']); ?>" alt="<?php echo htmlspecialchars($event['eventName']); ?>" style="width: 200px; height: 200px;">
    <?php else: ?>
        <p>No image available for this event.</p>
    <?php endif; ?>

    <p><strong>Date:</strong> <?php echo htmlspecialchars($event['date']); ?></p>
    <p><strong>Time:</strong> <?php echo htmlspecialchars($event['time']); ?></p>
    <p><strong>Location:</strong> <?php echo htmlspecialchars($event['location']); ?></p>
    <p><strong>Description:</strong> <?php echo htmlspecialchars($event['description']); ?></p>
    <p><strong>Max Participants:</strong> <?php echo htmlspecialchars($event['max_participants']); ?></p>

    <?php if (!$isRegistered): ?>
        <a href="register_event.php?eventID=<?php echo htmlspecialchars($event['eventID']); ?>" class="register-btn">Register</a>
    <?php else: ?>
        <p>You are already registered for this event.</p>
    <?php endif; ?>

    <a href="dashboard.php" class="back-btn">Back to Dashboard</a>
</div>

</body>
</html>
