<header class="fixed top-0 right-0 z-30 w-full md:w-[calc(100%-16rem)] t-glass border-b t-border flex items-center justify-between px-8 h-14 transition-all duration-300">
    <div class="md:hidden flex items-center gap-2">
        <?php if (function_exists('logoSVG')): ?>
            <span class="text-primary"><?= logoSVG(24) ?></span>
        <?php endif; ?>
        <span class="text-lg font-bold text-primary font-h3 tracking-tight">GlucoTrack</span>
    </div>
    <div class="hidden md:flex items-center">
        <span class="font-body-sm text-body-sm t-text-secondary"><?= date('M d, Y — l') ?></span>
    </div>
    <div class="flex items-center gap-3">
        <button class="t-text-muted hover:text-primary transition-colors active:scale-90 p-1">
            <span class="material-symbols-outlined text-[20px]">notifications</span>
        </button>
        <button class="t-text-muted hover:text-primary transition-colors active:scale-90 p-1">
            <span class="material-symbols-outlined text-[20px]">settings</span>
        </button>
    </div>
</header>
