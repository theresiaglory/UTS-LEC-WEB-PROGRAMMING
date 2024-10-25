<?php
require 'db.php';

if (isset($_GET['event_id'])) {
    $eventID = $_GET['event_id'];
    $eventQuery = "SELECT eventName FROM events WHERE eventID = ?";
    $stmt = $conn->prepare($eventQuery);
    $stmt->bind_param("i", $eventID);
    $stmt->execute();
    $event = $stmt->get_result()->fetch_assoc();

    $registrantQuery = "
        SELECT u.username, u.email 
        FROM registrations r
        JOIN users u ON r.userID = u.userID
        WHERE r.eventID = ?
    ";
    $stmt = $conn->prepare($registrantQuery);
    $stmt->bind_param("i", $eventID);
    $stmt->execute();
    $registrants = $stmt->get_result();
} else {
    die("Event ID not provided.");
}

if (isset($_POST['export'])) {
    $filename = "registrants_event_" . $eventID . ".csv";
    header("Content-Type: text/csv");
    header("Content-Disposition: attachment;filename=$filename");

    $output = fopen("php://output", "w");
    fputcsv($output, array('Username', 'Email'));

    while ($row = $registrants->fetch_assoc()) {
        fputcsv($output, $row);
    }
    fclose($output);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrants for <?php echo htmlspecialchars($event['eventName']); ?></title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>

<div class="dashboard-container">
    <main class="dashboard-content">
        <section id="registrants-section" class="section">
            <h2>Registrants for <?php echo htmlspecialchars($event['eventName']); ?></h2>

            <?php if ($registrants->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Email</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $registrants->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <form method="post">
                <button type="submit" name="export">Export to CSV</button>
            </form>
            <?php else: ?>
            <p>No registrants for this event.</p>
            <?php endif; ?>
            <form action="event_management.php" method="get">
                <button type="submit" class="back-button">Back to Dashboard</button>
            </form>
        </section>
    </main>
</div>
</body>
</html>