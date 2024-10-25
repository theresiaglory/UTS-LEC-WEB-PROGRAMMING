<?php
session_start();
include 'db.php';

if (!isset($_SESSION['userID'])) {
    header('Location: login.php');
    exit;
}
$role = $_SESSION['roles'];

if ($role == 'admin') {
    // Admin Dashboard
    include 'admin.php';
} else {
    // User Dashboard
    include 'user.php';
}
?>