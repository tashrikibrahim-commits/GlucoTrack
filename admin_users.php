<?php
require 'config.php';
require_once 'logo.php';
if (!isset($_SESSION['admin_id'])) { header("Location: admin_login.php"); exit(); }

$users = $conn->query("
    SELECT u.*, 
           COUNT(g.id) as total_readings,
           MAX(g.log_date) as last_reading_date,
           ROUND(AVG(g.glucose_level), 1) as avg_glucose
    FROM users u 
    LEFT JOIN glucose_logs g ON u.id = g.user_id 
    GROUP BY u.id 
    ORDER BY u.created_at DESC
")->fetch_all(MYSQLI_ASSOC);

$admin_page = 'users';
?>
<!DOCTYPE html>
<html class="dark" lang="en">
<head>
    <?php include 'header.php'; ?>
    <title>Users — Admin</title>
</head>
<body class="font-body-md min-h-screen flex selection:bg-primary/30">
    <?php include 'admin_sidebar.php'; ?>

    <main class="flex-1 md:ml-64 p-8 pt-6">
        <div class="max-w-6xl mx-auto">
            <div class="mb-8 flex justify-between items-center">
                <div>
                    <h2 class="font-h2 text-h2 t-text">Users</h2>
                    <p class="font-body-lg text-body-lg t-text-secondary mt-1"><?= count($users) ?> registered users</p>
                </div>
            </div>

            <div class="t-card border rounded-xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="t-elevated font-label-caps text-label-caps t-text-muted border-b t-border">
                                <th class="px-6 py-3 font-medium">ID</th>
                                <th class="px-6 py-3 font-medium">User</th>
                                <th class="px-6 py-3 font-medium">Diabetes</th>
                                <th class="px-6 py-3 font-medium">Auth</th>
                                <th class="px-6 py-3 font-medium">Total Readings</th>
                                <th class="px-6 py-3 font-medium">Avg Glucose</th>
                                <th class="px-6 py-3 font-medium">Last Active</th>
                                <th class="px-6 py-3 font-medium">Joined</th>
                            </tr>
                        </thead>
                        <tbody class="font-body-sm text-body-sm divide-y t-border">
                            <?php foreach ($users as $u): ?>
                            <tr class="t-hover transition-colors">
                                <td class="px-6 py-4 t-text-muted">#<?= $u['id'] ?></td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <?php 
                                        $img = $u['photo'] ?: ($u['google_photo'] ?: 'https://i.pravatar.cc/150?u='.$u['id']);
                                        ?>
                                        <img src="<?= htmlspecialchars($img) ?>" class="w-8 h-8 rounded-full object-cover border t-border" alt="">
                                        <div>
                                            <p class="t-text font-medium"><?= htmlspecialchars($u['full_name']) ?></p>
                                            <p class="t-text-muted text-xs"><?= htmlspecialchars($u['username']) ?></p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium <?= $u['diabetes'] === 'Yes' ? 'bg-amber-500/20 text-amber-400 border border-amber-500/30' : 'bg-primary/10 text-primary border border-primary/20' ?>">
                                        <?= $u['diabetes'] === 'Yes' ? 'Diabetic' : 'Non-Diabetic' ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <?php if ($u['google_id']): ?>
                                        <span class="inline-flex items-center gap-1 text-xs t-text-muted">
                                            <svg width="12" height="12" viewBox="0 0 48 48"><path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/><path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/></svg>
                                            Google
                                        </span>
                                    <?php else: ?>
                                        <span class="text-xs t-text-muted">Email</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 font-stat-value text-lg t-text"><?= $u['total_readings'] ?></td>
                                <td class="px-6 py-4">
                                    <?php if ($u['avg_glucose']): ?>
                                        <span style="color: <?= getStatusColor($u['avg_glucose']) ?>" class="font-medium"><?= $u['avg_glucose'] ?></span>
                                    <?php else: ?>
                                        <span class="t-text-muted">—</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 t-text-muted"><?= $u['last_reading_date'] ? date('M d, Y', strtotime($u['last_reading_date'])) : '—' ?></td>
                                <td class="px-6 py-4 t-text-muted"><?= date('M d, Y', strtotime($u['created_at'])) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
