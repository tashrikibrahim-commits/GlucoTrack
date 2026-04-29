<?php
require_once 'logo.php';
$current_page = $current_page ?? 'dashboard';
$full_name = $full_name ?? 'User';
$profileImg = $profileImg ?? 'https://i.pravatar.cc/150?u=default';
$diabetes_status = $diabetes_status ?? 'No';
?>

<nav class="hidden md:flex flex-col t-sidebar h-screen w-64 border-r t-border fixed left-0 top-0 z-40 transition-all duration-300">
    <div class="p-6 flex items-center gap-3">
        <span class="text-primary"><?= logoSVG(36) ?></span>
        <div>
            <h1 class="font-h3 text-[22px] t-text tracking-tighter font-bold">GlucoTrack</h1>
            <p class="font-label-caps text-[10px] t-text-muted uppercase tracking-widest">Medical System</p>
        </div>
    </div>

    <div class="px-6 mb-6 flex items-center gap-3">
        <img alt="Profile" class="w-11 h-11 rounded-full border t-border object-cover" src="<?= htmlspecialchars($profileImg) ?>" />
        <div>
            <p class="font-body-sm text-body-sm t-text font-semibold"><?= htmlspecialchars($full_name) ?></p>
            <div class="inline-flex items-center gap-1.5 mt-0.5">
                <span class="w-2 h-2 rounded-full bg-primary"></span>
                <span class="font-label-caps text-[10px] t-text-muted"><?= $diabetes_status === 'Yes' ? 'Diabetic Patient' : 'Non-Diabetic' ?></span>
            </div>
        </div>
    </div>

    <div class="flex flex-col gap-1 flex-grow px-2">
        <?php
        $links = [
            ['page' => 'dashboard', 'icon' => 'dashboard', 'label' => 'Dashboard', 'href' => 'dashboard.php'],
            ['page' => 'daily', 'icon' => 'calendar_today', 'label' => 'Daily Page', 'href' => 'daily.php'],
            ['page' => 'weekly', 'icon' => 'trending_up', 'label' => 'Weekly Trend', 'href' => 'weekly.php'],
            ['page' => 'monthly', 'icon' => 'monitoring', 'label' => 'Monthly Trend', 'href' => 'monthly.php'],
        ];
        foreach ($links as $link):
            $active = $current_page === $link['page'];
        ?>
        <a class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-all duration-200 <?= $active ? 't-elevated text-primary font-medium' : 't-text-secondary t-hover' ?>"
           href="<?= $link['href'] ?>">
            <span class="material-symbols-outlined text-[20px]" <?= $active ? "style=\"font-variation-settings: 'FILL' 1;\"" : '' ?>><?= $link['icon'] ?></span>
            <span class="font-body-sm text-body-sm"><?= $link['label'] ?></span>
        </a>
        <?php endforeach; ?>

        <div class="mt-auto">
            <a class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-all duration-200 <?= $current_page === 'profile' ? 't-elevated text-primary font-medium' : 't-text-secondary t-hover' ?>"
               href="user.php">
                <span class="material-symbols-outlined text-[20px]" <?= $current_page === 'profile' ? "style=\"font-variation-settings: 'FILL' 1;\"" : '' ?>>person</span>
                <span class="font-body-sm text-body-sm">Edit Profile</span>
            </a>
        </div>
    </div>

    <div class="p-4 space-y-2 border-t t-border">
        <!-- Theme Toggle -->
        <button onclick="toggleTheme()" class="theme-toggle w-full flex items-center gap-3 px-4 py-2 rounded-lg t-text-secondary t-hover transition-all duration-200 relative h-10">
            <span class="material-symbols-outlined text-[20px] icon-sun">light_mode</span>
            <span class="material-symbols-outlined text-[20px] icon-moon">dark_mode</span>
            <span class="font-body-sm text-body-sm ml-6">Toggle Theme</span>
        </button>
        <a href="dashboard.php?quick_log=1"
           class="w-full bg-primary text-on-primary font-body-md text-body-md font-semibold py-2.5 rounded-lg hover:opacity-90 transition-all flex items-center justify-center gap-2">
            <span class="material-symbols-outlined text-[18px]" style="font-variation-settings: 'FILL' 1;">add</span>
            Quick Log
        </a>
        <a href="logout.php"
           class="w-full t-text-muted hover:text-red-400 font-body-sm text-body-sm py-1.5 rounded-lg transition-colors flex items-center justify-center gap-2">
            <span class="material-symbols-outlined text-[16px]">logout</span>
            Sign Out
        </a>
    </div>
</nav>

<script>
function toggleTheme() {
    document.documentElement.classList.toggle('light-mode');
    document.body.classList.toggle('light-mode');
    const isLight = document.body.classList.contains('light-mode');
    localStorage.setItem('glucotrack-theme', isLight ? 'light' : 'dark');
}
// Apply saved theme on load
document.addEventListener('DOMContentLoaded', function() {
    const saved = localStorage.getItem('glucotrack-theme');
    if (saved === 'light') {
        document.documentElement.classList.add('light-mode');
        document.body.classList.add('light-mode');
    }
});
</script>
