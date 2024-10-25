<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['email'])) {
    $email = $_POST['email'];
    
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        $verification_code = rand(100000, 999999);
        
        $_SESSION['verification_code'] = $verification_code;
        $_SESSION['email'] = $email;

        echo "<script>alert('Kode verifikasi Anda: $verification_code');</script>";
    } else {
        echo "<p>Email tidak ditemukan.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2>Lupa Password</h2>
        <form method="POST" action="">
            <label>Masukkan Email Anda:</label><br>
            <input type="email" name="email" required><br>
            <button type="submit">Kirim Kode Verifikasi</button>
        </form>
        
        <?php if (isset($_SESSION['verification_code'])): ?>
            <form method="POST" action="verify_code.php">
                <label>Masukkan Kode Verifikasi:</label><br>
                <input type="text" name="entered_code" required><br>
                <button type="submit">Verifikasi Kode</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
