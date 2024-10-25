<?php
require 'db.php';

$eventQuery = "SELECT * FROM events";
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
    <title>Event Management</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <div class="dashboard-container">
        <aside class="sidebar">
            <h2>Admin Dashboard</h2>
            <ul>
                <li><a href="admin.php">Available Events</a></li>
                <li><a href="event_management.php">Event Management</a></li>
                <li><a href="user_management.php">User Management</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </aside>
        <main class="dashboard-content">
            <h2>Event Management</h2>
            <section id="create-event-section">
                <h3>Create New Event</h3>
                <form action="create_event.php" method="post" enctype="multipart/form-data">
                    <label for="event-name">Event Name:</label>
                    <input type="text" id="event-name" name="event_name" required>

                    <label for="event-date">Date:</label>
                    <input type="date" id="event-date" name="event_date" required>

                    <label for="event-time">Time:</label>
                    <input type="time" id="event-time" name="event_time" required>

                    <label for="event-location">Location:</label>
                    <input type="text" id="event-location" name="event_location" required>

                    <label for="event-description">Description:</label>
                    <textarea id="event-description" name="event_description" required></textarea>

                    <label for="max-participants">Max Participants:</label>
                    <input type="number" id="max-participants" name="max_participants" required>

                    <label for="event-image">Upload Image:</label>
                    <input type="file" id="event-image" name="event_image" required>

                    <button type="submit">Create Event</button>
                </form>
            </section>
            <section id="manage-events-section">
                <h3>Manage Existing Events</h3>
                <div class="event-list">
                    <?php while ($event = $eventResult->fetch_assoc()): ?>
                    <div class="event-item">
                        <h4><?php echo htmlspecialchars($event['eventName']); ?></h4>
                        <p>Date: <?php echo htmlspecialchars($event['date']); ?></p>
                        <p>Location: <?php echo htmlspecialchars($event['location']); ?></p>
                        
                        <div class="button-group">
                            <form method="post" action="edit_event.php">
                                <input type="hidden" name="event_id" value="<?php echo $event['eventID']; ?>">
                                <button type="submit">Edit Event</button>
                            </form>
                            <button onclick="openModal(<?php echo $event['eventID']; ?>)">Delete Event</button>
                            <form method="get" action="view_registrants.php">
                                <input type="hidden" name="event_id" value="<?php echo $event['eventID']; ?>">
                                <button type="submit">View Registrants</button>
                            </form>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            </section>
        </main>
    </div>
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>Confirm Deletion</h2>
            <p>Are you sure you want to delete this event?</p>
            <form id="deleteForm" method="post" action="delete_event.php">
                <input type="hidden" name="event_id" id="eventID" value="">
                <button type="submit">Yes, Delete</button>
                <button type="button" onclick="closeModal()">Cancel</button>
            </form>
        </div>
    </div>

    <script>
        function openModal(eventID) {
            document.getElementById('eventID').value = eventID;
            document.getElementById('deleteModal').style.display = "block"; 
        }

        function closeModal() {
            document.getElementById('deleteModal').style.display = "none"; 
        }

        window.onclick = function(event) {
            const modal = document.getElementById('deleteModal');
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
</body>
</html>
