<?php
$path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
if ($path === '/') $path = '/index.html';
$filePath = __DIR__ . $path;
if (file_exists($filePath)) {
    $ext = pathinfo($path, PATHINFO_EXTENSION);
    if ($ext === 'php') { include $filePath; return true; }
    $mime = ['html'=>'text/html', 'css'=>'text/css', 'js'=>'application/javascript', 'json'=>'application/json', 'csv'=>'text/csv'];
    if (isset($mime[$ext])) header("Content-Type: " . $mime[$ext]);
    readfile($filePath); return true;
}
http_response_code(404);
if (file_exists(__DIR__ . '/404.html')) include __DIR__ . '/404.html';
else echo "404 Not Found";
return true;
?>