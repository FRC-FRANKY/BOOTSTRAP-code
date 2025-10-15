<?php
/**
 * Test ZipArchive after enabling the extension
 * Run this after enabling the zip extension and restarting Apache
 */

echo "<h1>ZipArchive Extension Test</h1>";

echo "<h2>Checking ZipArchive Availability:</h2>";

if (class_exists('ZipArchive')) {
    echo "<p style='color: green; font-size: 18px;'>✅ ZipArchive class is available!</p>";
    
    // Test creating a ZipArchive object
    try {
        $zip = new ZipArchive();
        echo "<p style='color: green;'>✅ ZipArchive object created successfully</p>";
        
        // Test basic functionality
        echo "<h3>Testing DOCX Extraction:</h3>";
        
        // Check if there are any DOCX files to test with
        $uploadsPath = __DIR__ . '/uploads/resumes/';
        if (is_dir($uploadsPath)) {
            $docxFiles = glob($uploadsPath . '*.docx');
            if (!empty($docxFiles)) {
                $testFile = $docxFiles[0];
                echo "<p>Testing with: " . basename($testFile) . "</p>";
                
                if ($zip->open($testFile) === TRUE) {
                    echo "<p style='color: green;'>✅ DOCX file opened successfully</p>";
                    
                    // Try to extract document.xml
                    $document = $zip->getFromName('word/document.xml');
                    if ($document) {
                        echo "<p style='color: green;'>✅ document.xml extracted successfully</p>";
                        
                        // Show a preview of the extracted text
                        $text = strip_tags($document);
                        $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
                        $text = preg_replace('/\s+/', ' ', $text);
                        $text = trim($text);
                        
                        echo "<h4>Extracted Text Preview (first 200 characters):</h4>";
                        echo "<pre style='background: #f5f5f5; padding: 10px; border: 1px solid #ddd;'>";
                        echo htmlspecialchars(substr($text, 0, 200)) . "...";
                        echo "</pre>";
                        
                        if (strlen($text) > 200) {
                            echo "<p>Total text length: " . strlen($text) . " characters</p>";
                        }
                        
                    } else {
                        echo "<p style='color: red;'>❌ Could not extract document.xml</p>";
                    }
                    
                    $zip->close();
                } else {
                    echo "<p style='color: red;'>❌ Could not open DOCX file</p>";
                }
            } else {
                echo "<p>No DOCX files found in uploads/resumes/ directory</p>";
                echo "<p>Upload a DOCX file and try again</p>";
            }
        } else {
            echo "<p>Uploads directory not found</p>";
        }
        
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Error creating ZipArchive: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
    
} else {
    echo "<p style='color: red; font-size: 18px;'>❌ ZipArchive class is still not available</p>";
    echo "<h3>Next Steps:</h3>";
    echo "<ol>";
    echo "<li>Make sure you edited the correct php.ini file: <code>E:\\4thYear\\XAMPPSOFT\\php\\php.ini</code></li>";
    echo "<li>Look for the line: <code>;extension=zip</code></li>";
    echo "<li>Remove the semicolon: <code>extension=zip</code></li>";
    echo "<li>Save the file</li>";
    echo "<li>Restart Apache in XAMPP Control Panel</li>";
    echo "<li>Refresh this page</li>";
    echo "</ol>";
}

echo "<hr>";
echo "<h2>Current PHP Configuration:</h2>";
echo "<p><strong>PHP Version:</strong> " . phpversion() . "</p>";
echo "<p><strong>php.ini location:</strong> " . php_ini_loaded_file() . "</p>";

echo "<h2>All Loaded Extensions:</h2>";
$extensions = get_loaded_extensions();
sort($extensions);
echo "<p>" . implode(', ', $extensions) . "</p>";

if (in_array('zip', $extensions)) {
    echo "<p style='color: green;'>✅ 'zip' extension is in the loaded extensions list</p>";
} else {
    echo "<p style='color: red;'>❌ 'zip' extension is NOT in the loaded extensions list</p>";
}
?>
