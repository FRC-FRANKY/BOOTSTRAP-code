<?php
/**
 * Check PHP Extensions and Configuration
 */

echo "<h1>PHP Extensions and Configuration Check</h1>";

echo "<h2>Current PHP Version:</h2>";
echo "<p>" . phpversion() . "</p>";

echo "<h2>Required Extensions Status:</h2>";

$requiredExtensions = [
    'zip' => 'ZipArchive class for DOCX extraction',
    'fileinfo' => 'File type detection',
    'mysqli' => 'Database connection',
    'json' => 'JSON processing',
    'mbstring' => 'String handling'
];

echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<tr><th>Extension</th><th>Status</th><th>Purpose</th></tr>";

foreach ($requiredExtensions as $ext => $purpose) {
    $status = extension_loaded($ext) ? '✅ Loaded' : '❌ Missing';
    $color = extension_loaded($ext) ? 'green' : 'red';
    echo "<tr>";
    echo "<td><strong>$ext</strong></td>";
    echo "<td style='color: $color;'>$status</td>";
    echo "<td>$purpose</td>";
    echo "</tr>";
}

echo "</table>";

echo "<h2>PHP Configuration File Location:</h2>";
echo "<p><strong>php.ini location:</strong> " . php_ini_loaded_file() . "</p>";

echo "<h2>All Loaded Extensions:</h2>";
$extensions = get_loaded_extensions();
sort($extensions);
echo "<p>" . implode(', ', $extensions) . "</p>";

echo "<h2>Steps to Enable Zip Extension:</h2>";
echo "<ol>";
echo "<li><strong>Find your php.ini file:</strong> " . php_ini_loaded_file() . "</li>";
echo "<li><strong>Open php.ini in a text editor</strong> (as Administrator)</li>";
echo "<li><strong>Find the line:</strong> <code>;extension=zip</code></li>";
echo "<li><strong>Remove the semicolon:</strong> <code>extension=zip</code></li>";
echo "<li><strong>Save the file</strong></li>";
echo "<li><strong>Restart your web server</strong> (XAMPP Control Panel → Apache → Stop → Start)</li>";
echo "</ol>";

echo "<h2>Alternative: XAMPP Control Panel Method</h2>";
echo "<ol>";
echo "<li>Open <strong>XAMPP Control Panel</strong></li>";
echo "<li>Click <strong>Config</strong> next to Apache</li>";
echo "<li>Select <strong>PHP (php.ini)</strong></li>";
echo "<li>Search for <code>extension=zip</code></li>";
echo "<li>Remove the semicolon if present</li>";
echo "<li>Save and restart Apache</li>";
echo "</ol>";

echo "<h2>Verification Commands:</h2>";
echo "<p>After enabling the extension, run these commands to verify:</p>";
echo "<pre>";
echo "php -m | findstr zip\n";
echo "php -r \"echo class_exists('ZipArchive') ? 'ZipArchive available' : 'ZipArchive not available';\"";
echo "</pre>";

echo "<h2>Test ZipArchive Availability:</h2>";
if (class_exists('ZipArchive')) {
    echo "<p style='color: green;'>✅ ZipArchive class is available!</p>";
} else {
    echo "<p style='color: red;'>❌ ZipArchive class is not available</p>";
    echo "<p>You need to enable the zip extension as described above.</p>";
}
?>
