<?php
/**
 * Debug PDF extraction to see what text is being extracted
 */

require_once __DIR__ . '/skill_extractor.php';

echo "<h1>PDF Text Extraction Debug</h1>";

// Test with the resume sample PDF
$pdfPath = 'E:/4thYear/ELPHP/resumeSample.pdf';

if (file_exists($pdfPath)) {
    echo "<h2>Testing PDF: " . basename($pdfPath) . "</h2>";
    
    $skillExtractor = new SkillExtractor();
    
    // Extract raw text first
    echo "<h3>Raw Text Extraction:</h3>";
    $rawText = $skillExtractor->extractTextFromPDF($pdfPath);
    echo "<pre style='background: #f5f5f5; padding: 10px; max-height: 300px; overflow-y: auto;'>";
    echo htmlspecialchars($rawText);
    echo "</pre>";
    
    // Extract skills
    echo "<h3>Extracted Skills:</h3>";
    $skills = $skillExtractor->extractSkills($rawText);
    echo "<p>Found " . count($skills) . " skills:</p>";
    echo "<ul>";
    foreach ($skills as $skill) {
        echo "<li>" . htmlspecialchars($skill) . "</li>";
    }
    echo "</ul>";
    
    // Test with processResume method
    echo "<h3>Using processResume Method:</h3>";
    $processSkills = $skillExtractor->processResume($pdfPath, 'pdf');
    echo "<p>Found " . count($processSkills) . " skills:</p>";
    echo "<ul>";
    foreach ($processSkills as $skill) {
        echo "<li>" . htmlspecialchars($skill) . "</li>";
    }
    echo "</ul>";
    
} else {
    echo "<p style='color: red;'>PDF file not found: " . htmlspecialchars($pdfPath) . "</p>";
    
    // List available PDFs in uploads directory
    $uploadsPath = __DIR__ . '/uploads/resumes/';
    if (is_dir($uploadsPath)) {
        $pdfFiles = glob($uploadsPath . '*.pdf');
        echo "<h3>Available PDF files in uploads/resumes/:</h3>";
        echo "<ul>";
        foreach ($pdfFiles as $file) {
            echo "<li>" . basename($file) . "</li>";
        }
        echo "</ul>";
        
        if (!empty($pdfFiles)) {
            echo "<h3>Testing with first available PDF:</h3>";
            $testFile = $pdfFiles[0];
            echo "<p>Testing with: " . basename($testFile) . "</p>";
            
            $skillExtractor = new SkillExtractor();
            
            // Extract raw text
            echo "<h4>Raw Text Extraction:</h4>";
            $rawText = $skillExtractor->extractTextFromPDF($testFile);
            echo "<pre style='background: #f5f5f5; padding: 10px; max-height: 300px; overflow-y: auto;'>";
            echo htmlspecialchars($rawText);
            echo "</pre>";
            
            // Extract skills
            echo "<h4>Extracted Skills:</h4>";
            $skills = $skillExtractor->extractSkills($rawText);
            echo "<p>Found " . count($skills) . " skills:</p>";
            echo "<ul>";
            foreach ($skills as $skill) {
                echo "<li>" . htmlspecialchars($skill) . "</li>";
            }
            echo "</ul>";
        }
    }
}

echo "<hr>";
echo "<h2>Skills Database Check</h2>";

// Check if our new skills are in the database
$skillExtractor = new SkillExtractor();
$reflection = new ReflectionClass($skillExtractor);
$property = $reflection->getProperty('skillsDatabase');
$property->setAccessible(true);
$skillsDb = $property->getValue($skillExtractor);

$expectedSkills = ['McAfee', 'SIEM', 'EPO', 'NSM', 'FireEye', 'CMS', 'ETP', 'Wireshark', 'TCPView', 'OllyDbg', 'WinDbg', 'GBD', 'Windows 10', 'Windows 11', 'Mac OS', 'Google Workspace', 'DNS', 'Mail Server'];

echo "<h3>Checking for expected skills in database:</h3>";
echo "<ul>";
foreach ($expectedSkills as $expectedSkill) {
    $found = in_array($expectedSkill, $skillsDb);
    $status = $found ? '<span style="color: green;">✓ Found</span>' : '<span style="color: red;">✗ Missing</span>';
    echo "<li>$expectedSkill: $status</li>";
}
echo "</ul>";

echo "<h3>Total skills in database: " . count($skillsDb) . "</h3>";
?>
