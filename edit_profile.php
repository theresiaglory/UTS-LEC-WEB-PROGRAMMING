<?php
session_start();
require 'db.php';

if (!isset($_SESSION['userID'])) {
    header("Location: login.php");
    exit();
}

$userID = $_SESSION['userID'];
$stmt = $conn->prepare("SELECT username, email FROM users WHERE userID = ?");
$stmt->bind_param("i", $userID);
$stmt->execute();
$stmt->bind_result($username, $email);
$stmt->fetch();
$stmt->close();

$error = "";

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $error = "Invalid CSRF token.";
    } else {
        $new_username = $_POST['username'];
        $new_email = $_POST['email'];
        $new_password = $_POST['password'];
        
        $stmt = $conn->prepare("SELECT userID FROM users WHERE email = ? AND userID != ?");
        $stmt->bind_param("si", $new_email, $userID);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $error = "The email is already registered with another account.";
        } else {
            if (!empty($new_password)) {
                $new_password_hash = password_hash($new_password, PASSWORD_BCRYPT);
                $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, password = ? WHERE userID = ?");
                $stmt->bind_param("sssi", $new_username, $new_email, $new_password_hash, $userID);
            } else {
                $stmt = $conn->prepare("UPDATE users SET username = ?, email = ? WHERE userID = ?");
                $stmt->bind_param("ssi", $new_username, $new_email, $userID);
            }

            if ($stmt->execute()) {
                header("Location: view_profile.php");
                exit();
            } else {
                $error = "Error updating profile: " . $stmt->error;
            }
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="edit.css">
</head>
<body>
    <div class="container">
        <h2>Edit Profile</h2>

        <?php if (!empty($error)): ?>
            <p style="color: red;"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form method="POST" action="">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
            <label for="username">Username:</label>
            <input type="text" name="username" value="<?= htmlspecialchars($username) ?>" required>
            <br>
            <label for="email">Email:</label>
            <input type="email" name="email" value="<?= htmlspecialchars($email) ?>" required>
            <br>
            <label for="password">New Password (optional):</label>
            <input type="password" name="password">
            <br>
            <button type="submit">Update Profile</button>
        </form>

        <a href="view_profile.php">Back to Profile</a>
    </div>
</body>
</html>

<?php
$conn->close();
?>
