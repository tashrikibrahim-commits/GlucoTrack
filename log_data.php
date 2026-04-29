<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $log_date = $_POST['log_date'];
    $log_time = $_POST['log_time'];
    $glucose_level = $_POST['glucose_level'];
    $notes = $_POST['notes'] ?? '';
    $tag = $_POST['tag'] ?? '';

    $stmt = $conn->prepare("INSERT INTO glucose_logs (user_id, log_date, log_time, glucose_level, notes, tag) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issdss", $user_id, $log_date, $log_time, $glucose_level, $notes, $tag);
    $stmt->execute();
}

// Redirect back to the referring page
$redirect = $_POST['redirect'] ?? 'dashboard.php';
header("Location: $redirect");
exit();
?>