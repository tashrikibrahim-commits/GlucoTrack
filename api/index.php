<?php
// Front controller for Vercel serverless deployment
// Routes requests to the correct PHP file in the parent directory

$uri = $_SERVER['REQUEST_URI'] ?? '/';
$path = parse_url($uri, PHP_URL_PATH);
$path = ltrim($path, '/');

// Default to index.php
if (empty($path) || $path === '/') {
    $path = 'index.php';
}

// Remove api/ prefix if present
$path = preg_replace('/^api\//', '', $path);

// Look for file in the PARENT directory (root of project)
$root = dirname(__DIR__);
$file = $root . '/' . $path;

// If it's a directory, look for index.php inside it
if (is_dir($file)) {
    $file = rtrim($file, '/') . '/index.php';
}

// Fallback: try basename only
if (!file_exists($file)) {
    $file = $root . '/' . basename($path);
}

// Fallback: try same directory
if (!file_exists($file)) {
    $file = __DIR__ . '/' . basename($path);
}

if (file_exists($file) && pathinfo($file, PATHINFO_EXTENSION) === 'php') {
    // Change to root directory so relative requires work
    chdir($root);
    require $file;
} else {
    http_response_code(404);
    echo '<!DOCTYPE html><html><head><title>404</title></head><body style="background:#09090b;color:#dde4dd;font-family:Inter,sans-serif;display:flex;justify-content:center;align-items:center;height:100vh;margin:0"><div style="text-align:center"><h1 style="color:#4edea3;font-size:64px;margin:0">404</h1><p>Page not found</p><a href="/" style="color:#4edea3">← Go Home</a></div></body></html>';
}
