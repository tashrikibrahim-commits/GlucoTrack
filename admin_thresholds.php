<?php
require 'config.php';
require_once 'logo.php';
if (!isset($_SESSION['admin_id'])) { header("Location: admin_login.php"); exit(); }

$message = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'update') {
        $id = (int)$_POST['id'];
        $level_name = trim($_POST['level_name']);
        $min_value = (float)$_POST['min_value'];
        $max_value = (float)$_POST['max_value'];
        $color_hex = trim($_POST['color_hex']);
        
        $stmt = $conn->prepare("UPDATE glucose_thresholds SET level_name=?, min_value=?, max_value=?, color_hex=? WHERE id=?");
        $stmt->bind_param("sddsi", $level_name, $min_value, $max_value, $color_hex, $id);
        $stmt->execute();
        $message = 'updated';
        clearThresholdCache();
    }
    
    if ($action === 'add') {
        $level_name = trim($_POST['level_name']);
        $min_value = (float)$_POST['min_value'];
        $max_value = (float)$_POST['max_value'];
        $color_hex = trim($_POST['color_hex']);
        $color_bg = 'bg-[' . $color_hex . ']/20';
        $color_text = 'text-[' . $color_hex . ']';
        $color_border = 'border-[' . $color_hex . ']/30';
        $max_order = $conn->query("SELECT MAX(sort_order) as m FROM glucose_thresholds")->fetch_assoc()['m'] ?? 0;
        $sort_order = $max_order + 1;
        
        $stmt = $conn->prepare("INSERT INTO glucose_thresholds (level_name, min_value, max_value, color_bg, color_text, color_border, color_hex, extra_class, sort_order) VALUES (?,?,?,?,?,?,?,'',?)");
        $stmt->bind_param("sddsssis", $level_name, $min_value, $max_value, $color_bg, $color_text, $color_border, $color_hex, $sort_order);
        $stmt->execute();
        $message = 'added';
        clearThresholdCache();
    }
    
    if ($action === 'delete') {
        $id = (int)$_POST['id'];
        $conn->query("DELETE FROM glucose_thresholds WHERE id = $id");
        $message = 'deleted';
        clearThresholdCache();
    }
}

$thresholds = $conn->query("SELECT * FROM glucose_thresholds ORDER BY sort_order ASC")->fetch_all(MYSQLI_ASSOC);
$admin_page = 'thresholds';
?>
<!DOCTYPE html>
<html class="dark" lang="en">
<head>
    <?php include 'header.php'; ?>
    <title>Glucose Thresholds — Admin</title>
</head>
<body class="font-body-md min-h-screen flex selection:bg-primary/30">
    <?php include 'admin_sidebar.php'; ?>

    <main class="flex-1 md:ml-64 p-8 pt-6">
        <div class="max-w-5xl mx-auto">
            <div class="mb-8">
                <h2 class="font-h2 text-h2 t-text">Glucose Thresholds</h2>
                <p class="font-body-lg text-body-lg t-text-secondary mt-1">Configure the glucose level ranges and their status labels used across the platform.</p>
            </div>

            <?php if ($message): ?>
                <div class="bg-primary-container/10 border border-primary-container/20 text-primary px-4 py-3 rounded-lg mb-6 font-body-sm flex items-center gap-2">
                    <span class="material-symbols-outlined text-sm">check_circle</span>
                    Threshold <?= $message ?> successfully! Changes are live.
                </div>
            <?php endif; ?>

            <!-- Current Thresholds Table -->
            <div class="t-card border rounded-xl overflow-hidden mb-8">
                <div class="px-6 py-4 border-b t-border flex justify-between items-center">
                    <h3 class="font-h3 text-h3 t-text text-lg">Active Levels</h3>
                    <span class="font-label-caps text-label-caps t-text-muted"><?= count($thresholds) ?> levels configured</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="t-elevated font-label-caps text-label-caps t-text-muted border-b t-border">
                                <th class="px-6 py-3 font-medium">Order</th>
                                <th class="px-6 py-3 font-medium">Level Name</th>
                                <th class="px-6 py-3 font-medium">Min (mmol/L)</th>
                                <th class="px-6 py-3 font-medium">Max (mmol/L)</th>
                                <th class="px-6 py-3 font-medium">Color</th>
                                <th class="px-6 py-3 font-medium">Preview</th>
                                <th class="px-6 py-3 font-medium text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="font-body-sm text-body-sm divide-y t-border">
                            <?php foreach ($thresholds as $t): ?>
                            <tr class="t-hover transition-colors">
                                <form method="POST">
                                    <input type="hidden" name="action" value="update">
                                    <input type="hidden" name="id" value="<?= $t['id'] ?>">
                                    <td class="px-6 py-4 t-text-muted">#<?= $t['sort_order'] ?></td>
                                    <td class="px-6 py-3">
                                        <input type="text" name="level_name" value="<?= htmlspecialchars($t['level_name']) ?>" class="t-input border rounded px-3 py-1.5 text-sm w-28 focus:ring-1 focus:ring-primary outline-none">
                                    </td>
                                    <td class="px-6 py-3">
                                        <input type="number" step="0.1" name="min_value" value="<?= $t['min_value'] ?>" class="t-input border rounded px-3 py-1.5 text-sm w-20 focus:ring-1 focus:ring-primary outline-none">
                                    </td>
                                    <td class="px-6 py-3">
                                        <input type="number" step="0.1" name="max_value" value="<?= $t['max_value'] ?>" class="t-input border rounded px-3 py-1.5 text-sm w-20 focus:ring-1 focus:ring-primary outline-none">
                                    </td>
                                    <td class="px-6 py-3">
                                        <div class="flex items-center gap-2">
                                            <input type="color" name="color_hex" value="<?= $t['color_hex'] ?>" class="w-8 h-8 rounded cursor-pointer border-0 p-0 bg-transparent">
                                            <span class="t-text-muted text-xs"><?= $t['color_hex'] ?></span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-3">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium border" style="background-color: <?= $t['color_hex'] ?>20; color: <?= $t['color_hex'] ?>; border-color: <?= $t['color_hex'] ?>40;">
                                            <?= htmlspecialchars($t['level_name']) ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-3 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <button type="submit" class="text-primary hover:text-primary-fixed transition-colors" title="Save">
                                                <span class="material-symbols-outlined text-[18px]">save</span>
                                            </button>
                                </form>
                                <form method="POST" class="inline" onsubmit="return confirm('Delete this threshold level?')">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= $t['id'] ?>">
                                    <button type="submit" class="text-red-400 hover:text-red-300 transition-colors" title="Delete">
                                        <span class="material-symbols-outlined text-[18px]">delete</span>
                                    </button>
                                </form>
                                        </div>
                                    </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Add New Threshold -->
            <div class="t-card border rounded-xl p-6">
                <h3 class="font-h3 text-h3 t-text text-lg mb-6 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">add_circle</span>
                    Add New Level
                </h3>
                <form method="POST" class="grid grid-cols-1 sm:grid-cols-5 gap-4 items-end">
                    <input type="hidden" name="action" value="add">
                    <div>
                        <label class="block font-label-caps text-label-caps t-text-muted mb-2">Level Name</label>
                        <input type="text" name="level_name" required placeholder="e.g. Critical" class="w-full t-input border rounded-lg px-4 py-2.5 focus:ring-1 focus:ring-primary outline-none">
                    </div>
                    <div>
                        <label class="block font-label-caps text-label-caps t-text-muted mb-2">Min (mmol/L)</label>
                        <input type="number" step="0.1" name="min_value" required placeholder="0.0" class="w-full t-input border rounded-lg px-4 py-2.5 focus:ring-1 focus:ring-primary outline-none">
                    </div>
                    <div>
                        <label class="block font-label-caps text-label-caps t-text-muted mb-2">Max (mmol/L)</label>
                        <input type="number" step="0.1" name="max_value" required placeholder="99.9" class="w-full t-input border rounded-lg px-4 py-2.5 focus:ring-1 focus:ring-primary outline-none">
                    </div>
                    <div>
                        <label class="block font-label-caps text-label-caps t-text-muted mb-2">Color</label>
                        <input type="color" name="color_hex" value="#4edea3" class="w-full h-[42px] rounded-lg cursor-pointer border t-border p-1">
                    </div>
                    <button type="submit" class="bg-primary text-on-primary font-body-md font-semibold py-2.5 rounded-lg hover:opacity-90 transition-all">
                        Add Level
                    </button>
                </form>
            </div>
        </div>
    </main>
</body>
</html>
