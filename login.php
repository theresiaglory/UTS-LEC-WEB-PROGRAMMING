<?php
session_start();
require 'db.php';
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT userID, password, roles FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($user_id, $hash, $role);
        $stmt->fetch();
        
        if (password_verify($password, $hash)) {
            $_SESSION['userID'] = $user_id;
            $_SESSION['roles'] = $role; 

            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "Invalid user.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <form method="POST" action="">
        <h2>Login</h2>
        
        <?php if ($error): ?>
            <p style="color:red"><?php echo $error; ?></p>
            <p><a href="forgot_password.php">Forgot Password?</a></p>
        <?php endif; ?>
        
        <input type="email" name="email" placeholder="Email" required> 
        <input type="password" id="password" name="password" placeholder="Password" required>
        
        <label>
            <input type="checkbox" onclick="togglePassword()"> Show Password
        </label>
        
        <button type="submit">Login</button>
        <p>Don't have an account? <a href="registration.php">Register here</a></p>
    </form>

    <script>
        function togglePassword() {
            var passwordField = document.getElementById("password");
            if (passwordField.type === "password") {
                passwordField.type = "text";
            } else {
                passwordField.type = "password";
            }
        }
    </script>
</body>
</html>
