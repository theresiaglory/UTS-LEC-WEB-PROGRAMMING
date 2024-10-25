<?php
require 'db.php';

$userQuery = "
    SELECT u.userID, u.username, u.email, GROUP_CONCAT(e.eventName SEPARATOR ', ') AS registered_events 
    FROM users u
    LEFT JOIN registrations r ON u.userID = r.userID 
    LEFT JOIN events e ON r.eventID = e.eventID 
    WHERE u.roles != 'admin'
    GROUP BY u.userID
";

$userResult = $conn->query($userQuery);
if (!$userResult) {
    die("Error in user query: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
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
            <h2>User Management</h2>
            <table class="users-table">
                <thead>
                    <tr>
                        <th>User Name</th>
                        <th>Email</th>
                        <th>Registered Events</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($user = $userResult->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo htmlspecialchars($user['registered_events']); ?></td>
                        <td>
                            <form method="post" action="delete_user.php">
                                <input type="hidden" name="userID" value="<?php echo $user['userID']; ?>">
                                <button type="submit">Delete User</button>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </main>
    </div>
</body>
</html>
