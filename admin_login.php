<?php
require 'config.php';
require_once 'logo.php';

if (isset($_SESSION['admin_id'])) { header("Location: admin_dashboard.php"); exit(); }

$error = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    $stmt = $conn->prepare("SELECT id, password_hash FROM admins WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password_hash'])) {
            $_SESSION['admin_id'] = $row['id'];
            $_SESSION['admin_username'] = $username;
            header("Location: admin_dashboard.php");
            exit();
        }
    }
    $error = "Invalid credentials";
}
?>
<!DOCTYPE html>
<html class="dark" lang="en">
<head>
    <?php include 'header.php'; ?>
    <title>Admin Login — GlucoTrack</title>
</head>
<body class="flex items-center justify-center min-h-screen selection:bg-primary/30">
    <div class="fixed inset-0 pointer-events-none">
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[600px] h-[300px] bg-red-500/5 rounded-full blur-3xl"></div>
    </div>
    <div class="w-full max-w-md px-6 relative z-10">
        <div class="t-card border rounded-xl p-10">
            <div class="flex flex-col items-center mb-8">
                <span class="text-primary mb-3"><?= logoSVG(48) ?></span>
                <h1 class="font-h2 text-h2 t-text tracking-tighter">Admin Panel</h1>
                <p class="font-label-caps text-label-caps t-text-muted uppercase tracking-widest mt-1">GlucoTrack Management</p>
            </div>

            <?php if ($error): ?>
                <div class="bg-error-container/20 border border-error-container/30 text-error px-4 py-3 rounded-lg mb-6 text-center font-body-sm text-body-sm flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-sm">error</span>
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-5">
                <div>
                    <label class="block font-label-caps text-label-caps t-text-secondary uppercase mb-2">Admin Username</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 t-text-muted text-sm">shield_person</span>
                        <input type="text" name="username" required class="w-full t-input border rounded-lg py-3 pl-10 pr-4 font-body-sm focus:border-primary focus:ring-1 focus:ring-primary transition-colors outline-none" placeholder="Admin username">
                    </div>
                </div>
                <div>
                    <label class="block font-label-caps text-label-caps t-text-secondary uppercase mb-2">Password</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 t-text-muted text-sm">lock</span>
                        <input type="password" name="password" required class="w-full t-input border rounded-lg py-3 pl-10 pr-4 font-body-sm focus:border-primary focus:ring-1 focus:ring-primary transition-colors outline-none" placeholder="Admin password">
                    </div>
                </div>
                <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-body-md text-body-md font-semibold py-3 rounded-lg transition-colors flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-sm">admin_panel_settings</span>
                    Access Admin Panel
                </button>
            </form>
            <div class="text-center mt-6">
                <a href="index.php" class="t-text-muted hover:text-primary font-body-sm text-body-sm transition-colors">← Back to User Login</a>
            </div>
        </div>
    </div>
</body>
</html>
