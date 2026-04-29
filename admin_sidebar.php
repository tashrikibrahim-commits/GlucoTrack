<?php
require_once 'logo.php';
$admin_page = $admin_page ?? 'dashboard';
?>
<nav class="hidden md:flex flex-col t-sidebar h-screen w-64 border-r t-border fixed left-0 top-0 z-40 transition-all duration-300">
    <div class="p-6 flex items-center gap-3">
        <span class="text-red-500"><?= logoSVG(36) ?></span>
        <div>
            <h1 class="font-h3 text-[22px] t-text tracking-tighter font-bold">GlucoTrack</h1>
            <p class="font-label-caps text-[10px] text-red-400 uppercase tracking-widest">Admin Panel</p>
        </div>
    </div>

    <div class="px-6 mb-6 flex items-center gap-3">
        <div class="w-10 h-10 rounded-full bg-red-500/10 border border-red-500/30 flex items-center justify-center">
            <span class="material-symbols-outlined text-red-400 text-[20px]">shield_person</span>
        </div>
        <div>
            <p class="font-body-sm text-body-sm t-text font-semibold"><?= htmlspecialchars($_SESSION['admin_username'] ?? 'Admin') ?></p>
            <p class="font-label-caps text-[10px] text-red-400">Administrator</p>
        </div>
    </div>

    <div class="flex flex-col gap-1 flex-grow px-2">
        <a class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-all duration-200 <?= $admin_page === 'dashboard' ? 't-elevated text-red-400 font-medium' : 't-text-secondary t-hover' ?>" href="admin_dashboard.php">
            <span class="material-symbols-outlined text-[20px]">dashboard</span>
            <span class="font-body-sm text-body-sm">Overview</span>
        </a>
        <a class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-all duration-200 <?= $admin_page === 'thresholds' ? 't-elevated text-red-400 font-medium' : 't-text-secondary t-hover' ?>" href="admin_thresholds.php">
            <span class="material-symbols-outlined text-[20px]">tune</span>
            <span class="font-body-sm text-body-sm">Glucose Thresholds</span>
        </a>
        <a class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-all duration-200 <?= $admin_page === 'users' ? 't-elevated text-red-400 font-medium' : 't-text-secondary t-hover' ?>" href="admin_users.php">
            <span class="material-symbols-outlined text-[20px]">group</span>
            <span class="font-body-sm text-body-sm">Users</span>
        </a>
    </div>

    <div class="p-4 space-y-2 border-t t-border">
        <button onclick="toggleTheme()" class="theme-toggle w-full flex items-center gap-3 px-4 py-2 rounded-lg t-text-secondary t-hover transition-all duration-200 relative h-10">
            <span class="material-symbols-outlined text-[20px] icon-sun">light_mode</span>
            <span class="material-symbols-outlined text-[20px] icon-moon">dark_mode</span>
            <span class="font-body-sm text-body-sm ml-6">Toggle Theme</span>
        </button>
        <a href="index.php" class="w-full flex items-center gap-3 px-4 py-2 rounded-lg t-text-secondary t-hover transition-all duration-200">
            <span class="material-symbols-outlined text-[20px]">home</span>
            <span class="font-body-sm text-body-sm">User Site</span>
        </a>
        <a href="admin_logout.php" class="w-full flex items-center gap-3 px-4 py-2 rounded-lg t-text-muted hover:text-red-400 transition-all duration-200">
            <span class="material-symbols-outlined text-[20px]">logout</span>
            <span class="font-body-sm text-body-sm">Sign Out</span>
        </a>
    </div>
</nav>
<script>
function toggleTheme() {
    document.documentElement.classList.toggle('light-mode');
    document.body.classList.toggle('light-mode');
    localStorage.setItem('glucotrack-theme', document.body.classList.contains('light-mode') ? 'light' : 'dark');
}
document.addEventListener('DOMContentLoaded', function() {
    if (localStorage.getItem('glucotrack-theme') === 'light') {
        document.documentElement.classList.add('light-mode');
        document.body.classList.add('light-mode');
    }
});
</script>
