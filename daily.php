<?php
require 'config.php';
require_once 'logo.php';
if (!isset($_SESSION['user_id'])) { header("Location: index.php"); exit(); }

$user_id = $_SESSION['user_id'];
$full_name = $_SESSION['full_name'];
$selected_date = $_GET['date'] ?? date('Y-m-d');
$prev_date = date('Y-m-d', strtotime("$selected_date -1 day"));
$next_date = date('Y-m-d', strtotime("$selected_date +1 day"));

$userData = $conn->query("SELECT diabetes, gender, photo, google_photo FROM users WHERE id = $user_id")->fetch_assoc();
$diabetes_status = $userData['diabetes'] ?? 'No';
$profileImg = getProfileImg($userData);

$stmt = $conn->prepare("SELECT * FROM glucose_logs WHERE user_id = ? AND log_date = ? ORDER BY log_time");
$stmt->bind_param("is", $user_id, $selected_date);
$stmt->execute();
$logs = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$current_page = 'daily';
?>
<!DOCTYPE html>
<html class="dark" lang="en">
<head>
    <?php include 'header.php'; ?>
    <title>Daily View — GlucoTrack</title>
</head>
<body class="font-body-md min-h-screen flex selection:bg-primary/30">
    <?php include 'sidebar.php'; ?>
    <?php include 'topbar.php'; ?>

    <main class="flex-1 md:ml-64 mt-16 overflow-y-auto p-container-padding">
        <div class="max-w-7xl mx-auto space-y-xl">
            <section class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h2 class="font-h2 text-h2 t-text">Daily Log</h2>
                    <p class="font-body-md text-body-md t-text-secondary">Review and add your glucose readings.</p>
                </div>
                <div class="flex items-center t-card border rounded-lg p-1">
                    <a href="?date=<?= $prev_date ?>" class="p-2 t-text-secondary hover:t-text t-hover rounded-md transition-colors flex items-center">
                        <span class="material-symbols-outlined text-[20px]">chevron_left</span>
                    </a>
                    <div class="px-4 py-2 font-body-md font-medium t-text min-w-[160px] text-center flex items-center gap-2 justify-center">
                        <span class="material-symbols-outlined text-[18px] t-text-muted">calendar_month</span>
                        <?= date('M d, Y', strtotime($selected_date)) ?>
                    </div>
                    <a href="?date=<?= $next_date ?>" class="p-2 t-text-secondary hover:t-text t-hover rounded-md transition-colors flex items-center">
                        <span class="material-symbols-outlined text-[20px]">chevron_right</span>
                    </a>
                </div>
            </section>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 t-card border rounded-xl overflow-hidden flex flex-col">
                    <div class="px-6 py-4 border-b t-border flex flex-wrap justify-between items-center gap-2">
                        <h3 class="font-h3 text-h3 t-text text-lg"><?= date('l', strtotime($selected_date)) ?>'s Readings</h3>
                        <div class="flex items-center gap-2 text-sm t-text-muted flex-wrap">
                            <?php 
                            $thresholds = getGlucoseThresholds($conn);
                            foreach ($thresholds as $th): ?>
                                <span class="flex items-center gap-1 ml-1"><div class="w-2 h-2 rounded-full" style="background-color:<?= $th['color_hex'] ?>"></div> <?= $th['level_name'] ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <?php if (empty($logs)): ?>
                            <div class="px-6 py-16 text-center">
                                <span class="material-symbols-outlined t-text-muted text-5xl mb-4 block">event_busy</span>
                                <p class="t-text-secondary font-body-lg">No readings for this day.</p>
                                <p class="t-text-muted font-body-sm mt-1">Use the form to add your first entry.</p>
                            </div>
                        <?php else: ?>
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="t-elevated font-label-caps text-label-caps t-text-muted border-b t-border">
                                        <th class="px-6 py-3 font-medium">Time</th>
                                        <th class="px-6 py-3 font-medium">Level (mmol/L)</th>
                                        <th class="px-6 py-3 font-medium">Status</th>
                                        <th class="px-6 py-3 font-medium">Context / Notes</th>
                                    </tr>
                                </thead>
                                <tbody class="font-body-sm text-body-sm divide-y t-border">
                                    <?php foreach ($logs as $log):
                                        $si = getGlucoseStatus($log['glucose_level']);
                                    ?>
                                    <tr class="t-hover transition-colors">
                                        <td class="px-6 py-4 t-text"><?= date('h:i A', strtotime($log['log_time'])) ?></td>
                                        <td class="px-6 py-4 font-stat-value text-xl"><?= $log['glucose_level'] ?></td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium border" style="background-color: <?= $si['color'] ?>15; color: <?= $si['color'] ?>; border-color: <?= $si['color'] ?>30;">
                                                <?= $si['label'] ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 t-text-muted"><?= htmlspecialchars($log['tag'] ?? '') ?><?= ($log['tag'] && $log['notes']) ? ' / ' : '' ?><?= htmlspecialchars($log['notes'] ?? '—') ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="t-card border rounded-xl p-6 flex flex-col h-fit">
                    <h3 class="font-h3 text-h3 t-text text-lg mb-6 flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">add_circle</span> Add Reading
                    </h3>
                    <form action="log_data.php" method="POST" class="space-y-5">
                        <input type="hidden" name="log_date" value="<?= $selected_date ?>">
                        <input type="hidden" name="redirect" value="daily.php?date=<?= $selected_date ?>">
                        <div>
                            <label class="block font-label-caps text-label-caps t-text-muted mb-2">Glucose Level (mmol/L)</label>
                            <input name="glucose_level" class="w-full t-input border rounded-lg px-4 py-3 focus:ring-1 focus:ring-primary focus:border-primary transition-colors font-stat-value text-2xl h-14" placeholder="e.g. 5.5" step="0.1" type="number" required />
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block font-label-caps text-label-caps t-text-muted mb-2">Time</label>
                                <input name="log_time" class="w-full t-input border rounded-lg px-4 py-2.5 focus:ring-1 focus:ring-primary focus:border-primary transition-colors" type="time" value="<?= date('H:i') ?>" required />
                            </div>
                            <div>
                                <label class="block font-label-caps text-label-caps t-text-muted mb-2">Tag</label>
                                <select name="tag" class="w-full t-input border rounded-lg px-4 py-2.5 focus:ring-1 focus:ring-primary focus:border-primary transition-colors">
                                    <option value="">Select</option>
                                    <option value="Fasting">Fasting</option>
                                    <option value="Pre-meal">Pre-meal</option>
                                    <option value="Post-meal">Post-meal</option>
                                    <option value="Bedtime">Bedtime</option>
                                    <option value="Night">Night</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="block font-label-caps text-label-caps t-text-muted mb-2">Notes (Optional)</label>
                            <textarea name="notes" class="w-full t-input border rounded-lg px-4 py-3 focus:ring-1 focus:ring-primary focus:border-primary transition-colors resize-none text-sm" placeholder="Meal details, exercise, symptoms..." rows="3"></textarea>
                        </div>
                        <button type="submit" class="w-full bg-primary text-on-primary font-body-md font-medium py-3 rounded-lg hover:opacity-90 transition-all mt-2">Save Entry</button>
                    </form>
                </div>
            </div>
        </div>
    </main>
</body>
</html>