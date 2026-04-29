<?php
session_start();

// Parse .env file
$envFile = __DIR__ . '/../.env';
if (!file_exists($envFile)) $envFile = __DIR__ . '/.env';
$env = [];
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line) || $line[0] === '#') continue;
        $pos = strpos($line, ':');
        if ($pos !== false) {
            $key = trim(substr($line, 0, $pos));
            $val = trim(substr($line, $pos + 1));
            $env[$key] = $val;
        }
    }
}

// Also check environment variables (for Vercel)
$db_url = $env['DATABASE'] ?? getenv('DATABASE') ?: '';
$parsed = parse_url($db_url);

$db_host = $parsed['host'] ?? 'localhost';
$db_port = $parsed['port'] ?? 4000;
$db_user = isset($parsed['user']) ? urldecode($parsed['user']) : 'root';
$db_pass = isset($parsed['pass']) ? urldecode($parsed['pass']) : '';

// Google OAuth credentials
define('GOOGLE_CLIENT_ID', $env['GOOGLE_CLIENT_ID'] ?? getenv('GOOGLE_CLIENT_ID') ?: '');
define('GOOGLE_CLIENT_SECRET', $env['GOOGLE_CLIENT_SECRECT'] ?? getenv('GOOGLE_CLIENT_SECRECT') ?: '');

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$base_path = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
define('GOOGLE_REDIRECT_URI', $protocol . '://' . $host . $base_path . '/google_callback.php');

// Connect to TiDB Cloud with SSL
$conn = mysqli_init();
mysqli_ssl_set($conn, null, null, null, null, null);
mysqli_report(MYSQLI_REPORT_OFF);

if (!mysqli_real_connect($conn, $db_host, $db_user, $db_pass, '', $db_port, null, MYSQLI_CLIENT_SSL)) {
    die("Connection failed: " . mysqli_connect_error());
}

$conn->query("CREATE DATABASE IF NOT EXISTS diabetes_tracker");
$conn->select_db("diabetes_tracker");

// Load glucose thresholds from DB (cached in session)
function getGlucoseThresholds($conn) {
    if (isset($_SESSION['glucose_thresholds']) && isset($_SESSION['thresholds_loaded'])) {
        return $_SESSION['glucose_thresholds'];
    }
    
    $defaults = [
        ['level_name' => 'Dangerous', 'min_value' => 0, 'max_value' => 2.7, 'color_bg' => 'bg-red-950', 'color_text' => 'text-red-400', 'color_border' => 'border-red-500/50', 'color_hex' => '#ef4444', 'sort_order' => 1, 'extra_class' => 'font-bold uppercase tracking-wider'],
        ['level_name' => 'Low', 'min_value' => 2.8, 'max_value' => 3.9, 'color_bg' => 'bg-secondary-container/20', 'color_text' => 'text-secondary', 'color_border' => 'border-secondary-container/30', 'color_hex' => '#3b82f6', 'sort_order' => 2, 'extra_class' => ''],
        ['level_name' => 'Normal', 'min_value' => 4.0, 'max_value' => 7.8, 'color_bg' => 'bg-primary-container/10', 'color_text' => 'text-primary-fixed-dim', 'color_border' => 'border-primary-container/20', 'color_hex' => '#4edea3', 'sort_order' => 3, 'extra_class' => ''],
        ['level_name' => 'Borderline', 'min_value' => 7.9, 'max_value' => 10.0, 'color_bg' => 'bg-amber-500/20', 'color_text' => 'text-amber-500', 'color_border' => 'border-amber-500/30', 'color_hex' => '#f59e0b', 'sort_order' => 4, 'extra_class' => ''],
        ['level_name' => 'High', 'min_value' => 10.1, 'max_value' => 99.9, 'color_bg' => 'bg-error-container/20', 'color_text' => 'text-error', 'color_border' => 'border-error-container/30', 'color_hex' => '#ef4444', 'sort_order' => 5, 'extra_class' => ''],
    ];

    $result = $conn->query("SELECT * FROM glucose_thresholds ORDER BY sort_order ASC");
    if ($result && $result->num_rows > 0) {
        $thresholds = $result->fetch_all(MYSQLI_ASSOC);
    } else {
        $thresholds = $defaults;
    }
    
    $_SESSION['glucose_thresholds'] = $thresholds;
    $_SESSION['thresholds_loaded'] = true;
    return $thresholds;
}

function getGlucoseStatus($level, $conn_ref = null) {
    global $conn;
    $c = $conn_ref ?? $conn;
    $thresholds = getGlucoseThresholds($c);
    
    foreach ($thresholds as $t) {
        if ($level >= $t['min_value'] && $level <= $t['max_value']) {
            $extra = $t['extra_class'] ?? '';
            return [
                'label' => $t['level_name'],
                'class' => $t['color_bg'] . ' ' . $t['color_text'] . ' ' . $t['color_border'] . ($extra ? ' ' . $extra : ''),
                'color' => $t['color_hex']
            ];
        }
    }
    
    // Fallback
    return ['label' => 'Unknown', 'class' => 'bg-zinc-800 text-zinc-400 border-zinc-700', 'color' => '#71717a'];
}

function getStatusColor($level) {
    $status = getGlucoseStatus($level);
    return $status['color'];
}

// Clear threshold cache (called after admin updates)
function clearThresholdCache() {
    unset($_SESSION['glucose_thresholds']);
    unset($_SESSION['thresholds_loaded']);
}
?>