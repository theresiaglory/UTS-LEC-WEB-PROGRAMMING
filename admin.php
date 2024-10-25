<?php
require 'db.php';

$eventQuery = "
    SELECT e.eventID, e.eventName, e.date, e.location, e.max_participants, e.image, 
           COUNT(r.registrationID) AS registrants
    FROM events e
    LEFT JOIN registrations r ON e.eventID = r.eventID
    GROUP BY e.eventID
";
$eventResult = $conn->query($eventQuery);

if (!$eventResult) {
    die("Error in event query: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <div class="dashboard-container">
        <aside class="sidebar">
            <h2>Admin Dashboard</h2>
            <ul>
                <li><a href="#events">Available Events</a></li>
                <li><a href="event_management.php">Event Management</a></li> 
                <li><a href="user_management.php">User Management</a></li> 
                <li><a href="logout.php">Logout</a></li> 
            </ul>
        </aside>
        <main class="dashboard-content">
            <section id="events" class="section">
                <h2>Available Events</h2>
                <div class="event-list">
                    <?php while($event = $eventResult->fetch_assoc()): ?>
                        <div class="event-item">
                            <div class="event-info">
                                <h3><?php echo htmlspecialchars($event['eventName']); ?></h3>
                                <p>Date: <?php echo htmlspecialchars($event['date']); ?></p>
                                <p>Location: <?php echo htmlspecialchars($event['location']); ?></p>
                                <p>Registrants: <?php echo $event['registrants']; ?> / <?php echo $event['max_participants']; ?></p>
                            </div>
                            <?php if (!empty($event['image'])): ?>
                                <img src="uploads/<?php echo htmlspecialchars($event['image']); ?>" alt="<?php echo htmlspecialchars($event['eventName']); ?>">
                            <?php else: ?>
                                <p>No image available for this event.</p>
                            <?php endif; ?>
                        </div>
                    <?php endwhile; ?>
                </div>
            </section>
        </main>
    </div>
</body>
</html>