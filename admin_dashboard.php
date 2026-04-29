<?php
require 'config.php';
require_once 'logo.php';
if (!isset($_SESSION['admin_id'])) { header("Location: admin_login.php"); exit(); }

$total_users = $conn->query("SELECT COUNT(*) as c FROM users")->fetch_assoc()['c'];
$total_readings = $conn->query("SELECT COUNT(*) as c FROM glucose_logs")->fetch_assoc()['c'];
$today_readings = $conn->query("SELECT COUNT(*) as c FROM glucose_logs WHERE log_date = CURDATE()")->fetch_assoc()['c'];
$active_users = $conn->query("SELECT COUNT(DISTINCT user_id) as c FROM glucose_logs WHERE log_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)")->fetch_assoc()['c'];
$avg_glucose = $conn->query("SELECT ROUND(AVG(glucose_level),1) as a FROM glucose_logs")->fetch_assoc()['a'] ?? '—';
$recent_users = $conn->query("SELECT id, username, full_name, diabetes, created_at FROM users ORDER BY created_at DESC LIMIT 5")->fetch_all(MYSQLI_ASSOC);

$admin_page = 'dashboard';
?>
<!DOCTYPE html>
<html class="dark" lang="en">
<head>
    <?php include 'header.php'; ?>
    <title>Admin Dashboard — GlucoTrack</title>
</head>
<body class="font-body-md min-h-screen flex selection:bg-primary/30">
    <?php include 'admin_sidebar.php'; ?>

    <main class="flex-1 md:ml-64 p-8 pt-6">
        <div class="max-w-6xl mx-auto">
            <div class="mb-8">
                <h2 class="font-h2 text-h2 t-text">Admin Dashboard</h2>
                <p class="font-body-lg text-body-lg t-text-secondary mt-1">System overview and management.</p>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                <?php
                $stats = [
                    ['Total Users', $total_users, 'group', 'text-blue-400'],
                    ['Total Readings', $total_readings, 'monitoring', 'text-primary'],
                    ['Today\'s Readings', $today_readings, 'today', 'text-amber-400'],
                    ['Active Users (7d)', $active_users, 'person_check', 'text-emerald-400'],
                ];
                foreach ($stats as $s): ?>
                <div class="t-card border rounded-xl p-5">
                    <div class="flex items-center justify-between mb-3">
                        <span class="font-label-caps text-label-caps t-text-muted uppercase"><?= $s[0] ?></span>
                        <span class="material-symbols-outlined <?= $s[3] ?> text-[20px]"><?= $s[2] ?></span>
                    </div>
                    <p class="font-stat-value text-stat-value t-text"><?= $s[1] ?></p>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Platform Avg -->
                <div class="t-card border rounded-xl p-6">
                    <h3 class="font-h3 text-h3 t-text text-lg mb-4">Platform Average</h3>
                    <div class="flex items-end gap-3">
                        <span class="font-stat-value text-[56px] text-primary leading-none"><?= $avg_glucose ?></span>
                        <span class="t-text-muted pb-2">mmol/L</span>
                    </div>
                </div>

                <!-- Recent Users -->
                <div class="t-card border rounded-xl p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-h3 text-h3 t-text text-lg">Recent Users</h3>
                        <a href="admin_users.php" class="text-primary font-body-sm text-body-sm hover:underline">View All →</a>
                    </div>
                    <div class="space-y-3">
                        <?php foreach ($recent_users as $u): ?>
                        <div class="flex justify-between items-center py-2 border-b t-border last:border-0">
                            <div>
                                <p class="font-body-sm t-text font-medium"><?= htmlspecialchars($u['full_name']) ?></p>
                                <p class="font-label-caps text-[10px] t-text-muted"><?= htmlspecialchars($u['username']) ?></p>
                            </div>
                            <span class="font-label-caps text-[10px] t-text-muted"><?= date('M d', strtotime($u['created_at'])) ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
