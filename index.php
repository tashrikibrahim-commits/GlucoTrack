<?php
require 'config.php';
require_once 'logo.php';

if (isset($_SESSION['user_id'])) { header("Location: dashboard.php"); exit(); }

$error = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $stmt = $conn->prepare("SELECT id, full_name, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        if ($row['password'] === $password) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['full_name'] = $row['full_name'];
            $_SESSION['username'] = $username;
            header("Location: dashboard.php");
            exit();
        } else { $error = "Invalid password"; }
    } else { $error = "User not found"; }
}
if (isset($_GET['error'])) $error = "Google authentication failed. Please try again.";
?>
<!DOCTYPE html>
<html class="dark" lang="en">
<head>
    <?php include 'header.php'; ?>
    <title>GlucoTrack — Sign In</title>
</head>
<body class="flex items-center justify-center min-h-screen selection:bg-primary/30">
    <div class="fixed inset-0 pointer-events-none">
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[400px] bg-primary/5 rounded-full blur-3xl"></div>
    </div>
    <div class="w-full max-w-md px-6 relative z-10">
        <div class="t-card border rounded-xl p-10">
            <div class="flex flex-col items-center mb-10">
                <span class="text-primary mb-3"><?= logoSVG(48) ?></span>
                <h1 class="font-h1 text-h1 t-text tracking-tighter mb-1">GlucoTrack</h1>
                <p class="font-label-caps text-label-caps t-text-muted uppercase tracking-widest">Medical System</p>
            </div>
            <h2 class="font-h3 text-h3 t-text text-center mb-8">Welcome Back</h2>
            <?php if ($error): ?>
                <div class="bg-error-container/20 border border-error-container/30 text-error px-4 py-3 rounded-lg mb-6 text-center font-body-sm text-body-sm flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-sm">error</span>
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>
            <a href="google_login.php" class="w-full t-elevated border t-border hover:border-primary/30 t-text font-body-md text-body-md font-medium py-3 rounded-lg flex items-center justify-center gap-3 transition-all duration-200 mb-6">
                <svg width="20" height="20" viewBox="0 0 48 48"><path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/><path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/><path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/><path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/></svg>
                Sign in with Google
            </a>
            <div class="flex items-center gap-4 mb-6">
                <div class="flex-1 h-px t-border border-t"></div>
                <span class="font-label-caps text-label-caps t-text-muted uppercase">or</span>
                <div class="flex-1 h-px t-border border-t"></div>
            </div>
            <form method="POST" class="space-y-5">
                <div>
                    <label class="block font-label-caps text-label-caps t-text-secondary uppercase mb-2">Username</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 t-text-muted text-sm">person</span>
                        <input type="text" name="username" required class="w-full t-input border rounded-lg py-3 pl-10 pr-4 font-body-sm focus:border-primary focus:ring-1 focus:ring-primary transition-colors outline-none" placeholder="Enter your username">
                    </div>
                </div>
                <div>
                    <label class="block font-label-caps text-label-caps t-text-secondary uppercase mb-2">Password</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 t-text-muted text-sm">lock</span>
                        <input type="password" name="password" required class="w-full t-input border rounded-lg py-3 pl-10 pr-4 font-body-sm focus:border-primary focus:ring-1 focus:ring-primary transition-colors outline-none" placeholder="Enter your password">
                    </div>
                </div>
                <button type="submit" class="w-full bg-primary text-on-primary font-body-md text-body-md font-semibold py-3 rounded-lg hover:opacity-90 transition-all flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-sm">login</span> Sign In
                </button>
            </form>
            <div class="text-center mt-8 space-y-2">
                <a href="register.php" class="text-primary hover:underline font-body-sm text-body-sm block">Don't have an account? Register →</a>
                <a href="admin_login.php" class="t-text-muted hover:text-red-400 font-label-caps text-[10px] uppercase tracking-widest block transition-colors">Admin Access</a>
            </div>
        </div>
    </div>
</body>
</html>