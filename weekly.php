<?php
require 'config.php';
require_once 'logo.php';
if (!isset($_SESSION['user_id'])) { header("Location: index.php"); exit(); }
$user_id = $_SESSION['user_id']; $full_name = $_SESSION['full_name'];
$userData = $conn->query("SELECT diabetes, gender, photo, google_photo FROM users WHERE id = $user_id")->fetch_assoc();
$diabetes_status = $userData['diabetes'] ?? 'No';
$profileImg = getProfileImg($userData);
$logs = $conn->query("SELECT log_date, AVG(glucose_level) as avg_glucose FROM glucose_logs WHERE user_id = $user_id AND log_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) GROUP BY log_date ORDER BY log_date")->fetch_all(MYSQLI_ASSOC);
$labels = []; $values = [];
foreach ($logs as $r) { $labels[] = date('M d', strtotime($r['log_date'])); $values[] = round($r['avg_glucose'], 1); }
$current_page = 'weekly';
?>
<!DOCTYPE html>
<html class="dark" lang="en">
<head><?php include 'header.php'; ?><title>Weekly Trend — GlucoTrack</title></head>
<body class="font-body-md min-h-screen flex selection:bg-primary/30">
    <?php include 'sidebar.php'; ?><?php include 'topbar.php'; ?>
    <main class="flex-1 md:ml-64 mt-16 overflow-y-auto p-container-padding">
        <div class="max-w-7xl mx-auto">
            <div class="mb-xl"><h2 class="font-h2 text-h2 t-text mb-2">Weekly Trend</h2><p class="font-body-lg text-body-lg t-text-secondary">Average daily glucose over the last 7 days.</p></div>
            <div class="t-card border rounded-xl p-lg"><div style="height:400px;"><canvas id="weeklyChart"></canvas></div></div>
        </div>
    </main>
<script>
const isDark = !document.body.classList.contains('light-mode');
new Chart(document.getElementById('weeklyChart'), {
    type: 'line', data: { labels: <?= json_encode($labels) ?>, datasets: [{ label: 'Average Glucose (mmol/L)', data: <?= json_encode($values) ?>, borderColor: isDark?'#4edea3':'#10b981', backgroundColor: isDark?'rgba(78,222,163,0.1)':'rgba(16,185,129,0.08)', fill:true, tension:0.4, borderWidth:3, pointRadius:6, pointBackgroundColor: isDark?'#4edea3':'#10b981', pointBorderColor: isDark?'#18181b':'#fff', pointBorderWidth:3 }] },
    options: { responsive:true, maintainAspectRatio:false, plugins:{legend:{display:false},tooltip:{backgroundColor:isDark?'#18181b':'#fff',borderColor:isDark?'#27272a':'#e2e8f0',borderWidth:1,titleColor:isDark?'#dde4dd':'#0f172a',bodyColor:isDark?'#4edea3':'#10b981',padding:12,cornerRadius:8}}, scales:{y:{grid:{color:isDark?'#27272a':'#e2e8f0'},ticks:{color:isDark?'#71717a':'#64748b'}},x:{grid:{display:false},ticks:{color:isDark?'#71717a':'#64748b'}}} }
});
</script>
</body></html>