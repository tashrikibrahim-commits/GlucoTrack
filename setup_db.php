<?php
require 'config.php';

echo "<div style='font-family:monospace;background:#09090b;color:#dde4dd;padding:20px;min-height:100vh;'>";
echo "<h2 style='color:#4edea3;margin-bottom:20px;'>GlucoTrack — TiDB Cloud Setup</h2>";

$conn->query("CREATE DATABASE IF NOT EXISTS diabetes_tracker");
$conn->select_db("diabetes_tracker");
echo "✅ Database 'diabetes_tracker' ready<br><br>";

$conn->query("CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) UNIQUE,
    full_name VARCHAR(200) NOT NULL,
    password VARCHAR(255) DEFAULT NULL,
    age INT DEFAULT NULL,
    gender VARCHAR(10) DEFAULT 'Male',
    weight DECIMAL(5,1) DEFAULT NULL,
    bloodgroup VARCHAR(5) DEFAULT NULL,
    diabetes VARCHAR(5) DEFAULT 'No',
    photo MEDIUMTEXT DEFAULT NULL,
    google_id VARCHAR(255) DEFAULT NULL,
    google_photo VARCHAR(500) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");
echo "✅ Table 'users' created<br>";
// Upgrade photo column for base64 storage (in case table already existed with VARCHAR)
$conn->query("ALTER TABLE users MODIFY COLUMN photo MEDIUMTEXT DEFAULT NULL");

$conn->query("CREATE TABLE IF NOT EXISTS glucose_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    log_date DATE NOT NULL,
    log_time TIME NOT NULL,
    glucose_level DECIMAL(4,1) NOT NULL,
    notes TEXT DEFAULT NULL,
    tag VARCHAR(50) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");
echo "✅ Table 'glucose_logs' created<br>";

$conn->query("CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");
echo "✅ Table 'admins' created<br>";

// Seed default admin if not exists
$check = $conn->query("SELECT id FROM admins WHERE username = 'tashrik'");
if ($check->num_rows === 0) {
    $hash = password_hash('tashrikM31#', PASSWORD_DEFAULT);
    $un = 'tashrik';
    $stmt = $conn->prepare("INSERT INTO admins (username, password_hash) VALUES (?, ?)");
    $stmt->bind_param("ss", $un, $hash);
    $stmt->execute();
    echo "✅ Default admin 'tashrik' created<br>";
} else {
    echo "⏭️ Admin 'tashrik' already exists<br>";
}

$conn->query("CREATE TABLE IF NOT EXISTS glucose_thresholds (
    id INT AUTO_INCREMENT PRIMARY KEY,
    level_name VARCHAR(50) NOT NULL,
    min_value DECIMAL(4,1) NOT NULL,
    max_value DECIMAL(4,1) NOT NULL,
    color_bg VARCHAR(100) NOT NULL,
    color_text VARCHAR(100) NOT NULL,
    color_border VARCHAR(100) NOT NULL,
    color_hex VARCHAR(20) NOT NULL DEFAULT '#71717a',
    extra_class VARCHAR(100) DEFAULT '',
    sort_order INT DEFAULT 0
)");
echo "✅ Table 'glucose_thresholds' created<br>";

// Seed default thresholds if empty
$check = $conn->query("SELECT id FROM glucose_thresholds LIMIT 1");
if ($check->num_rows === 0) {
    $defaults = [
        ['Dangerous', 0, 2.7, 'bg-red-950', 'text-red-400', 'border-red-500/50', '#ef4444', 'font-bold uppercase tracking-wider', 1],
        ['Low', 2.8, 3.9, 'bg-secondary-container/20', 'text-secondary', 'border-secondary-container/30', '#3b82f6', '', 2],
        ['Normal', 4.0, 7.8, 'bg-primary-container/10', 'text-primary-fixed-dim', 'border-primary-container/20', '#4edea3', '', 3],
        ['Borderline', 7.9, 10.0, 'bg-amber-500/20', 'text-amber-500', 'border-amber-500/30', '#f59e0b', '', 4],
        ['High', 10.1, 99.9, 'bg-error-container/20', 'text-error', 'border-error-container/30', '#ef4444', '', 5],
    ];
    $stmt = $conn->prepare("INSERT INTO glucose_thresholds (level_name, min_value, max_value, color_bg, color_text, color_border, color_hex, extra_class, sort_order) VALUES (?,?,?,?,?,?,?,?,?)");
    foreach ($defaults as $d) {
        $stmt->bind_param("sddsssssi", $d[0], $d[1], $d[2], $d[3], $d[4], $d[5], $d[6], $d[7], $d[8]);
        $stmt->execute();
    }
    echo "✅ Default glucose thresholds seeded<br>";
} else {
    echo "⏭️ Glucose thresholds already exist<br>";
}

echo "<br><span style='color:#4edea3;font-weight:bold;'>Setup complete!</span><br><br>";
echo "<a href='index.php' style='color:#4edea3;'>→ Go to Login</a> | ";
echo "<a href='seed_samples.php' style='color:#4edea3;'>→ Seed Sample Data</a> | ";
echo "<a href='admin_login.php' style='color:#4edea3;'>→ Admin Panel</a>";
echo "</div>";
?>
