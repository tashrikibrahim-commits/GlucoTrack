<?php
require 'config.php';

echo "<div style='font-family:monospace;background:#09090b;color:#dde4dd;padding:20px;min-height:100vh;'>";
echo "<h2 style='color:#4edea3;margin-bottom:20px;'>GlucoTrack — Seeding Sample Data</h2>";

$tags = ['Fasting', 'Pre-meal', 'Post-meal', 'Bedtime', 'Night', ''];
$notes_options = ["", "Fasting", "Post breakfast", "Post lunch", "Post dinner", "Before bed", "After exercise", "Felt dizzy", "Normal day"];

// Get all user IDs
$users = $conn->query("SELECT id FROM users")->fetch_all(MYSQLI_ASSOC);

if (empty($users)) {
    echo "<p style='color:#ffb4ab;'>No users found. Please register a user first.</p>";
    echo "<a href='register.php' style='color:#4edea3;'>→ Register</a>";
    echo "</div>";
    exit;
}

foreach ($users as $u) {
    $user_id = $u['id'];
    $count = 0;
    while ($count < 200) {
        $days_ago = rand(1, 400);
        $log_date = date('Y-m-d', strtotime("- $days_ago days"));
        $log_time = sprintf("%02d:%02d:00", rand(0, 23), rand(0, 59));
        
        // Realistic glucose level
        $glucose = round(rand(38, 125) / 10, 1);
        if (rand(1, 100) <= 8) $glucose = round(rand(25, 37) / 10, 1);
        if (rand(1, 100) <= 5) $glucose = round(rand(130, 180) / 10, 1);
        if (rand(1, 100) <= 2) $glucose = round(rand(15, 27) / 10, 1); // dangerous low
        
        $notes = $notes_options[array_rand($notes_options)];
        $tag = $tags[array_rand($tags)];
        
        $stmt = $conn->prepare("INSERT INTO glucose_logs (user_id, log_date, log_time, glucose_level, notes, tag) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issdss", $user_id, $log_date, $log_time, $glucose, $notes, $tag);
        $stmt->execute();
        
        $count++;
    }
    echo "✅ User $user_id: 200 samples added<br>";
}

echo "<br><span style='color:#4edea3;font-weight:bold;'>All samples added successfully!</span><br><br>";
echo "<a href='dashboard.php' style='color:#4edea3;'>→ Go to Dashboard</a>";
echo "</div>";
?>