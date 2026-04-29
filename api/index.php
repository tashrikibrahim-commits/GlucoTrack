<?php
// Front controller for Vercel serverless deployment
// Routes requests to the correct PHP file

$uri = $_SERVER['REQUEST_URI'] ?? '/';
$path = parse_url($uri, PHP_URL_PATH);
$path = ltrim($path, '/');

// Default to index.php
if (empty($path) || $path === '/' || $path === 'api') {
    $path = 'index.php';
}

// Remove api/ prefix if present
$path = preg_replace('/^api\//', '', $path);

// Map to actual file
$file = __DIR__ . '/' . $path;

// If it's a directory, look for index.php
if (is_dir($file)) {
    $file = rtrim($file, '/') . '/index.php';
}

// Check if the file exists in current directory
if (!file_exists($file)) {
    $file = __DIR__ . '/' . basename($path);
}

if (file_exists($file) && pathinfo($file, PATHINFO_EXTENSION) === 'php') {
    require $file;
} else {
    http_response_code(404);
    echo "404 - Page not found";
}
?>
