<meta charset="utf-8" />
<meta content="width=device-width, initial-scale=1.0" name="viewport" />
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com" rel="preconnect" />
<link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect" />
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Space+Grotesk:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    tailwind.config = {
        darkMode: "class",
        theme: {
            extend: {
                colors: {
                    "primary": "#4edea3",
                    "primary-container": "#10b981",
                    "primary-fixed": "#6ffbbe",
                    "primary-fixed-dim": "#4edea3",
                    "on-primary": "#003824",
                    "on-primary-container": "#00422b",
                    "secondary": "#adc6ff",
                    "secondary-container": "#0566d9",
                    "on-secondary": "#002e6a",
                    "tertiary": "#ffb3af",
                    "tertiary-container": "#fc7c78",
                    "on-tertiary": "#650911",
                    "error": "#ffb4ab",
                    "error-container": "#93000a",
                    "on-error": "#690005",
                    "on-error-container": "#ffdad6",
                    "outline": "#86948a",
                    "outline-variant": "#3c4a42",
                    "inverse-primary": "#006c49",
                    "inverse-surface": "#dde4dd",
                    "surface-variant": "#2f3632",
                    "surface-container": "#1a211d",
                    "surface-container-high": "#242c27",
                    "surface-container-low": "#161d19",
                    "surface-container-lowest": "#09100c",
                    "surface-bright": "#343b36",
                    "on-surface": "#dde4dd",
                    "on-surface-variant": "#bbcabf",
                    "on-background": "#dde4dd",
                    "background": "#0e1511",
                    "surface": "#0e1511",
                    "surface-dim": "#0e1511"
                },
                borderRadius: { DEFAULT: "0.25rem", lg: "0.5rem", xl: "0.75rem", full: "9999px" },
                spacing: { md: "16px", "container-padding": "24px", unit: "4px", lg: "24px", gutter: "20px", sm: "8px", xs: "4px", xl: "40px" },
                fontFamily: { h1: ["Space Grotesk"], h2: ["Space Grotesk"], h3: ["Space Grotesk"], "body-sm": ["Inter"], "body-md": ["Inter"], "body-lg": ["Inter"], "stat-value": ["Space Grotesk"], "label-caps": ["Inter"] },
                fontSize: {
                    h1: ["48px", { lineHeight: "1.1", letterSpacing: "-0.02em", fontWeight: "700" }],
                    h2: ["32px", { lineHeight: "1.2", letterSpacing: "-0.01em", fontWeight: "600" }],
                    h3: ["24px", { lineHeight: "1.3", fontWeight: "600" }],
                    "body-sm": ["14px", { lineHeight: "1.4", fontWeight: "400" }],
                    "body-md": ["16px", { lineHeight: "1.5", fontWeight: "400" }],
                    "body-lg": ["18px", { lineHeight: "1.6", fontWeight: "400" }],
                    "stat-value": ["40px", { lineHeight: "1", fontWeight: "500" }],
                    "label-caps": ["12px", { lineHeight: "1", letterSpacing: "0.05em", fontWeight: "600" }]
                }
            }
        }
    }
</script>
<style>
    /* ===== THEME SYSTEM ===== */
    :root {
        --bg-base: #09090b;
        --bg-card: #18181b;
        --bg-elevated: #27272a;
        --bg-input: #09090b;
        --bg-sidebar: #09090b;
        --bg-hover: rgba(39, 39, 42, 0.5);
        --border-color: #27272a;
        --border-strong: #3f3f46;
        --text-primary: #fafafa;
        --text-secondary: #a1a1aa;
        --text-muted: #71717a;
        --text-inverse: #09090b;
        --accent: #4edea3;
        --accent-dim: rgba(78, 222, 163, 0.1);
        --chart-grid: #27272a;
        --chart-text: #71717a;
        --glass-bg: rgba(9, 9, 11, 0.6);
        --shadow-card: 0 0 0 1px var(--border-color);
        --transition-speed: 300ms;
    }

    .light-mode {
        --bg-base: #f8fafc;
        --bg-card: #ffffff;
        --bg-elevated: #f1f5f9;
        --bg-input: #f1f5f9;
        --bg-sidebar: #ffffff;
        --bg-hover: rgba(241, 245, 249, 0.8);
        --border-color: #e2e8f0;
        --border-strong: #cbd5e1;
        --text-primary: #0f172a;
        --text-secondary: #475569;
        --text-muted: #94a3b8;
        --text-inverse: #ffffff;
        --accent: #10b981;
        --accent-dim: rgba(16, 185, 129, 0.08);
        --chart-grid: #e2e8f0;
        --chart-text: #64748b;
        --glass-bg: rgba(255, 255, 255, 0.7);
        --shadow-card: 0 1px 3px rgba(0,0,0,0.08), 0 0 0 1px var(--border-color);
    }

    /* Apply CSS variables */
    body { 
        background-color: var(--bg-base); 
        color: var(--text-primary); 
        transition: background-color var(--transition-speed) ease, color var(--transition-speed) ease;
    }

    .t-card { background: var(--bg-card); border-color: var(--border-color); box-shadow: var(--shadow-card); transition: background var(--transition-speed) ease, border-color var(--transition-speed) ease, box-shadow var(--transition-speed) ease; }
    .t-elevated { background: var(--bg-elevated); transition: background var(--transition-speed) ease; }
    .t-input { background: var(--bg-input); border-color: var(--border-color); color: var(--text-primary); transition: background var(--transition-speed) ease, border-color var(--transition-speed) ease; }
    .t-sidebar { background: var(--bg-sidebar); border-color: var(--border-color); transition: background var(--transition-speed) ease; }
    .t-border { border-color: var(--border-color); transition: border-color var(--transition-speed) ease; }
    .t-glass { background: var(--glass-bg); backdrop-filter: blur(12px); border-color: var(--border-color); transition: background var(--transition-speed) ease; }
    .t-text { color: var(--text-primary); transition: color var(--transition-speed) ease; }
    .t-text-secondary { color: var(--text-secondary); transition: color var(--transition-speed) ease; }
    .t-text-muted { color: var(--text-muted); transition: color var(--transition-speed) ease; }
    .t-hover:hover { background: var(--bg-hover); }

    .material-symbols-outlined {
        font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
    }
    input[type="time"], input[type="date"], input[type="number"], select { color-scheme: dark; }
    .light-mode input[type="time"], .light-mode input[type="date"], .light-mode input[type="number"], .light-mode select { color-scheme: light; }

    /* Theme toggle animation */
    .theme-toggle { position: relative; overflow: hidden; }
    .theme-toggle .icon-sun, .theme-toggle .icon-moon { transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.3s ease; position: absolute; }
    .theme-toggle .icon-sun { transform: translateY(0); opacity: 1; }
    .theme-toggle .icon-moon { transform: translateY(30px); opacity: 0; }
    .light-mode .theme-toggle .icon-sun { transform: translateY(-30px); opacity: 0; }
    .light-mode .theme-toggle .icon-moon { transform: translateY(0); opacity: 1; }
</style>

<!-- Theme initialization (runs before render to prevent flash) -->
<script>
(function() {
    const saved = localStorage.getItem('glucotrack-theme');
    if (saved === 'light') {
        document.documentElement.classList.add('light-mode');
        document.body && document.body.classList.add('light-mode');
    }
})();
</script>
