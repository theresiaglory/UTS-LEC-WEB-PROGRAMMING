<?php
session_start();
require 'db.php';

$message = "";
$showPasswordForm = true;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['entered_code'])) {
    if (isset($_SESSION['verification_code'])) {
        $entered_code = $_POST['entered_code'];

        if ($entered_code == $_SESSION['verification_code']) {
            $_SESSION['code_verified'] = true; 
            unset($_SESSION['verification_code']); 
        } else {
            $message = "<p style='color:red;'>Kode verifikasi salah. Silakan coba lagi.</p>";
        }
    } else {
        $message = "<p style='color:red;'>Kode verifikasi belum dihasilkan. Silakan kembali ke halaman sebelumnya.</p>";
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['new_password']) && isset($_SESSION['code_verified']) && $_SESSION['code_verified']) {
    $new_password = password_hash($_POST['new_password'], PASSWORD_BCRYPT);

    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
    $stmt->bind_param("ss", $new_password, $_SESSION['email']);
    
    if ($stmt->execute()) {
        $message = "<div class='success-message'>Password berhasil diubah! Anda bisa <a href='login.php'>login</a> dengan password baru Anda.</div>";
        $showPasswordForm = false;
        session_destroy();
    } else {
        $message = "<p style='color:red;'>Terjadi kesalahan saat mengubah password.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2>Verifikasi Kode</h2>
        <?php if (!empty($message)): ?>
            <div><?php echo $message; ?></div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['code_verified']) && $_SESSION['code_verified']): ?>
            <?php if ($showPasswordForm): ?>
                <h2>Reset Password</h2>
                <form method="POST" action="">
                    <label>Password Baru:</label><br>
                    <input type="password" name="new_password" required><br>
                    <button type="submit">Ubah Password</button>
                </form>
            <?php endif; ?>
        <?php else: ?>
            <form method="POST" action="">
                <label>Masukkan Kode Verifikasi:</label><br>
                <input type="text" name="entered_code" required><br>
                <button type="submit">Verifikasi Kode</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
