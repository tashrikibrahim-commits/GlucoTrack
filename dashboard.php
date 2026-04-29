<?php
require 'config.php';
require_once 'logo.php';
if (!isset($_SESSION['user_id'])) { header("Location: index.php"); exit(); }

$user_id = $_SESSION['user_id'];
$full_name = $_SESSION['full_name'];
$userData = $conn->query("SELECT diabetes, gender, photo, google_photo FROM users WHERE id = $user_id")->fetch_assoc();
$diabetes_status = $userData['diabetes'] ?? 'No';
$profileImg = getProfileImg($userData);

$latest = $conn->query("SELECT glucose_level, log_date, log_time FROM glucose_logs WHERE user_id = $user_id ORDER BY log_date DESC, log_time DESC LIMIT 1")->fetch_assoc();
$statusInfo = ['label' => 'No Data', 'class' => 'bg-zinc-800 text-zinc-400 border-zinc-700', 'color' => '#71717a'];
if ($latest) { $statusInfo = getGlucoseStatus($latest['glucose_level']); }

$recent = $conn->query("SELECT log_date, AVG(glucose_level) as avg_g FROM glucose_logs WHERE user_id = $user_id AND log_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) GROUP BY log_date ORDER BY log_date")->fetch_all(MYSQLI_ASSOC);
$labels = []; $values = [];
foreach ($recent as $r) { $labels[] = date('D', strtotime($r['log_date'])); $values[] = round($r['avg_g'], 1); }

$hour = (int)date('H');
$greeting = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');
$current_page = 'dashboard';
?>
<!DOCTYPE html>
<html class="dark" lang="en">
<head>
    <?php include 'header.php'; ?>
    <title>Dashboard — GlucoTrack</title>
</head>
<body class="font-body-md min-h-screen flex selection:bg-primary/30">
    <?php include 'sidebar.php'; ?>
    <?php include 'topbar.php'; ?>

    <main class="flex-1 w-full md:ml-64 pt-16 p-container-padding max-w-[1280px] mx-auto">
        <div class="mb-xl">
            <h2 class="font-h2 text-h2 t-text mb-2"><?= $greeting ?>, <?= htmlspecialchars(explode(' ', $full_name)[0]) ?></h2>
            <p class="font-body-lg text-body-lg t-text-secondary flex items-center gap-2">
                <span class="material-symbols-outlined text-sm">today</span> <?= date('F d, Y') ?>
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-lg">
            <!-- Latest Reading Hero -->
            <div class="lg:col-span-8 t-card border rounded-xl p-lg relative overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-br from-primary/5 to-transparent opacity-50 pointer-events-none"></div>
                <div class="relative z-10 flex flex-col h-full items-center justify-center py-8">
                    <div class="flex flex-col items-center mb-8">
                        <p class="font-label-caps text-label-caps t-text-muted uppercase tracking-widest mb-2">Latest Reading</p>
                        <?php if ($latest): ?>
                            <p class="font-body-sm text-body-sm t-text-muted mb-6"><?= date('M d, Y • h:i A', strtotime($latest['log_date'].' '.$latest['log_time'])) ?></p>
                        <?php else: ?>
                            <p class="font-body-sm text-body-sm t-text-muted mb-6">No readings yet</p>
                        <?php endif; ?>
                        <div class="px-4 py-1.5 rounded-full flex items-center gap-2 border text-xs font-medium" style="background-color: <?= $statusInfo['color'] ?>15; color: <?= $statusInfo['color'] ?>; border-color: <?= $statusInfo['color'] ?>30;">
                            <span class="w-2 h-2 rounded-full animate-pulse" style="background-color: <?= $statusInfo['color'] ?>"></span>
                            <span class="font-label-caps text-label-caps uppercase"><?= $statusInfo['label'] ?></span>
                        </div>
                    </div>
                    <div class="flex items-end gap-4">
                        <span class="font-stat-value text-[72px] leading-none" style="color: <?= $statusInfo['color'] ?>"><?= $latest ? $latest['glucose_level'] : '—' ?></span>
                        <span class="font-h3 text-h3 t-text-muted pb-2">mmol/L</span>
                    </div>
                </div>
            </div>

            <!-- Quick Log -->
            <div class="lg:col-span-4 t-card border rounded-xl p-lg flex flex-col">
                <div class="flex items-center gap-2 mb-6">
                    <span class="material-symbols-outlined text-primary">add_circle</span>
                    <h3 class="font-h3 text-h3 t-text">Quick Log</h3>
                </div>
                <form action="log_data.php" method="POST" class="flex flex-col gap-4 flex-grow">
                    <input type="hidden" name="log_date" value="<?= date('Y-m-d') ?>">
                    <div>
                        <label class="block font-label-caps text-label-caps t-text-muted uppercase mb-2">Time</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 t-text-muted text-sm">schedule</span>
                            <input name="log_time" class="w-full t-input border rounded-lg py-2.5 pl-10 pr-4 font-body-sm focus:border-primary focus:ring-1 focus:ring-primary transition-colors outline-none" type="time" value="<?= date('H:i') ?>" />
                        </div>
                    </div>
                    <div>
                        <label class="block font-label-caps text-label-caps t-text-muted uppercase mb-2">Glucose Level (mmol/L)</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 t-text-muted text-sm">water_drop</span>
                            <input name="glucose_level" class="w-full t-input border rounded-lg py-2.5 pl-10 pr-4 font-body-sm focus:border-primary focus:ring-1 focus:ring-primary transition-colors outline-none" placeholder="e.g. 5.5" step="0.1" type="number" required />
                        </div>
                    </div>
                    <div class="flex-grow">
                        <label class="block font-label-caps text-label-caps t-text-muted uppercase mb-2">Notes</label>
                        <textarea name="notes" class="w-full h-full t-input border rounded-lg py-2.5 px-4 font-body-sm focus:border-primary focus:ring-1 focus:ring-primary transition-colors outline-none resize-none" placeholder="Fasting, post-meal..." rows="2"></textarea>
                    </div>
                    <button type="submit" class="w-full bg-primary text-on-primary font-body-md text-body-md font-semibold py-3 rounded-lg hover:opacity-90 transition-all mt-2">Log Reading</button>
                </form>
            </div>

            <!-- 7-Day Trend -->
            <div class="lg:col-span-12 t-card border rounded-xl p-lg flex flex-col">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="font-h3 text-h3 t-text">7-Day Trend</h3>
                    <a href="weekly.php" class="font-body-sm text-body-sm text-primary flex items-center gap-1 hover:underline">Full Analysis <span class="material-symbols-outlined text-sm">arrow_forward</span></a>
                </div>
                <div style="height: 280px;"><canvas id="weekChart"></canvas></div>
            </div>
        </div>
    </main>

<script>
const isDark = !document.body.classList.contains('light-mode');
new Chart(document.getElementById('weekChart'), {
    type: 'bar',
    data: {
        labels: <?= json_encode($labels) ?>,
        datasets: [{
            label: 'Avg Glucose (mmol/L)',
            data: <?= json_encode($values) ?>,
            backgroundColor: isDark ? 'rgba(78, 222, 163, 0.7)' : 'rgba(16, 185, 129, 0.6)',
            borderColor: isDark ? '#4edea3' : '#10b981',
            borderWidth: 1, borderRadius: 4, borderSkipped: false
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false }, tooltip: { backgroundColor: isDark ? '#18181b' : '#fff', borderColor: isDark ? '#27272a' : '#e2e8f0', borderWidth: 1, titleColor: isDark ? '#dde4dd' : '#0f172a', bodyColor: isDark ? '#4edea3' : '#10b981', padding: 12, cornerRadius: 8 } },
        scales: {
            y: { beginAtZero: false, min: 2, max: 12, grid: { color: isDark ? '#27272a' : '#e2e8f0' }, ticks: { color: isDark ? '#71717a' : '#64748b' } },
            x: { grid: { display: false }, ticks: { color: isDark ? '#71717a' : '#64748b' } }
        }
    }
});
</script>
</body>
</html>