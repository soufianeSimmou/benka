<?php
// Test file to check if build assets are accessible
echo "<h1>Asset Test</h1>";
echo "<h2>Manifest Content:</h2>";
$manifestPath = __DIR__ . '/build/manifest.json';
if (file_exists($manifestPath)) {
    echo "<pre>";
    echo htmlspecialchars(file_get_contents($manifestPath));
    echo "</pre>";

    $manifest = json_decode(file_get_contents($manifestPath), true);
    $cssFile = $manifest['resources/css/app.css']['file'] ?? 'NOT FOUND';
    $jsFile = $manifest['resources/js/app.js']['file'] ?? 'NOT FOUND';

    echo "<h2>CSS File:</h2>";
    echo "<p>$cssFile</p>";
    echo "<p><a href='/build/$cssFile' target='_blank'>Test CSS Link</a></p>";

    echo "<h2>JS File:</h2>";
    echo "<p>$jsFile</p>";
    echo "<p><a href='/build/$jsFile' target='_blank'>Test JS Link</a></p>";

    echo "<h2>Build Directory Contents:</h2>";
    echo "<pre>";
    system('ls -laR build/');
    echo "</pre>";
} else {
    echo "<p style='color: red;'>Manifest file not found at: $manifestPath</p>";
}

echo "<h2>Environment:</h2>";
echo "<p>APP_ENV: " . env('APP_ENV') . "</p>";
echo "<p>APP_URL: " . env('APP_URL') . "</p>";
