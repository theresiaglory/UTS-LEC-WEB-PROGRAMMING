<?php
require 'db.php';

if (!isset($_SESSION['userID'])) {
    header("Location: login.php");
    exit();
}
$userID = $_SESSION['userID'];

$sql_events = "SELECT * FROM events WHERE status = 'open'";
$result_events = $conn->query($sql_events);

$sql_registered = "SELECT e.eventName, e.eventID, e.date, e.time, e.location, e.description 
                   FROM registrations r
                   JOIN events e ON r.eventID = e.eventID 
                   WHERE r.userID = ?";
$stmt = $conn->prepare($sql_registered);
$stmt->bind_param("i", $userID);
$stmt->execute();
$result_registered = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="user.css">
</head>
<body>

<div class="dashboard-container">
    <header>
        <h1>User Dashboard</h1>
        <nav>
            <a href="view_profile.php">View Profile</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>
    <main>
        <section class="events-section">
            <h2>Available Events</h2>
            <?php
            if ($result_events->num_rows > 0) {
                while ($row = $result_events->fetch_assoc()) {
                    echo "<div class='event-card'>";
                    echo "<h3>" . htmlspecialchars($row['eventName']) . "</h3>";
                    echo "<a href='event_details.php?eventID=" . htmlspecialchars($row['eventID']) . "' class='toggle-button'>Show Details</a>";  // Link to event details page
                    echo "</div>";
                }
            } else {
                echo "<p>No events available.</p>";
            }
            ?>
        </section>
        <section class="events-section">
            <h2>Your Registered Events</h2>
            <?php
            if ($result_registered->num_rows > 0) {
                while ($row = $result_registered->fetch_assoc()) {
                    echo "<div class='event-card'>"; 
                    echo "<h3>" . htmlspecialchars($row['eventName']) . "</h3>";
                    echo "<a href='event_details_cancel.php?eventID=" . htmlspecialchars($row['eventID']) . "' class='toggle-button'>Show Details</a>";
                    echo "<div id='registered-details-" . htmlspecialchars($row['eventID']) . "' class='event-details' style='display:none;'>";
                    echo "<p><strong>Date:</strong> " . htmlspecialchars($row['date']) . " <strong>Time:</strong> " . htmlspecialchars($row['time']) . "</p>";
                    echo "<p><strong>Location:</strong> " . htmlspecialchars($row['location']) . "</p>";
                    echo "<p><strong>Description:</strong> " . htmlspecialchars($row['description']) . "</p>";
                    echo "<form action='resign_event.php' method='GET'>
                            <input type='hidden' name='eventID' value='" . htmlspecialchars($row['eventID']) . "'>
                            <button type='submit' class='resign-btn'>Cancel Registration</button>
                          </form>";
                    echo "</div>";
                    echo "</div>";
                }
            } else {
                echo "<p>You have not registered for any events.</p>";
            }
            ?>
        </section>
    </main>
</div>

</body>
</html>
