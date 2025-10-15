<?php
/**
 * Debug DOCX text extraction to see what's actually being extracted
 */

require_once __DIR__ . '/skill_extractor.php';

echo "<h1>DOCX Text Extraction Debug</h1>";

// Check if there are any DOCX files in uploads
$uploadsPath = __DIR__ . '/uploads/resumes/';
$docxFiles = [];

if (is_dir($uploadsPath)) {
    $docxFiles = glob($uploadsPath . '*.docx');
}

if (empty($docxFiles)) {
    echo "<h2>No DOCX files found</h2>";
    echo "<p>Please upload a DOCX file first, then run this debug script.</p>";
    echo "<p>Expected skills from your resume:</p>";
    echo "<ul>";
    echo "<li>McAfee SIEM/EPO/NSM</li>";
    echo "<li>FireEye CMS/ETP</li>";
    echo "<li>OllyDbg/WinDbg/GBD</li>";
    echo "<li>Wireshark/TCPView</li>";
    echo "<li>DNS Servers, Mail Server</li>";
    echo "<li>Windows 10, 11, Linux, Mac OS</li>";
    echo "<li>Google Workspace</li>";
    echo "</ul>";
    exit();
}

$skillExtractor = new SkillExtractor();

foreach ($docxFiles as $docxFile) {
    echo "<h2>Testing DOCX: " . basename($docxFile) . "</h2>";
    
    // Test raw text extraction
    echo "<h3>Raw Text Extraction:</h3>";
    $rawText = $skillExtractor->extractTextFromDOCX($docxFile);
    echo "<pre style='background: #f5f5f5; padding: 10px; max-height: 300px; overflow-y: auto; border: 1px solid #ddd;'>";
    echo htmlspecialchars($rawText);
    echo "</pre>";
    
    if (empty($rawText)) {
        echo "<p style='color: red;'>❌ No text extracted from DOCX file</p>";
        continue;
    }
    
    // Test skill extraction
    echo "<h3>Extracted Skills:</h3>";
    $skills = $skillExtractor->extractSkills($rawText);
    echo "<p>Found " . count($skills) . " skills:</p>";
    echo "<ul>";
    foreach ($skills as $skill) {
        echo "<li>" . htmlspecialchars($skill) . "</li>";
    }
    echo "</ul>";
    
    // Check if expected skills are in the text
    echo "<h3>Expected Skills Check:</h3>";
    $expectedSkills = [
        'McAfee', 'SIEM', 'EPO', 'NSM',
        'FireEye', 'CMS', 'ETP',
        'OllyDbg', 'WinDbg', 'GBD',
        'Wireshark', 'TCPView',
        'DNS', 'Mail Server',
        'Windows 10', 'Windows 11', 'Linux', 'Mac OS',
        'Google Workspace'
    ];
    
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Expected Skill</th><th>Found in Text</th><th>Status</th></tr>";
    
    foreach ($expectedSkills as $expectedSkill) {
        $found = stripos($rawText, $expectedSkill) !== false;
        $status = $found ? '✅ Found' : '❌ Missing';
        $color = $found ? 'green' : 'red';
        echo "<tr>";
        echo "<td>" . htmlspecialchars($expectedSkill) . "</td>";
        echo "<td>" . ($found ? 'Yes' : 'No') . "</td>";
        echo "<td style='color: $color;'>$status</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Test with processResume method
    echo "<h3>Using processResume Method:</h3>";
    $processSkills = $skillExtractor->processResume($docxFile, 'docx');
    echo "<p>Found " . count($processSkills) . " skills:</p>";
    echo "<ul>";
    foreach ($processSkills as $skill) {
        echo "<li>" . htmlspecialchars($skill) . "</li>";
    }
    echo "</ul>";
    
    break; // Only test the first DOCX file
}

echo "<hr>";
echo "<h2>Diagnosis:</h2>";

if (empty($rawText)) {
    echo "<p style='color: red;'><strong>Problem:</strong> DOCX text extraction is failing completely.</p>";
    echo "<p><strong>Possible causes:</strong></p>";
    echo "<ul>";
    echo "<li>DOCX file is corrupted or not a valid DOCX</li>";
    echo "<li>DOCX file contains only images (no text)</li>";
    echo "<li>ZipArchive extension not available</li>";
    echo "<li>File permissions issue</li>";
    echo "</ul>";
} else {
    $hasExpectedSkills = false;
    foreach ($expectedSkills as $skill) {
        if (stripos($rawText, $skill) !== false) {
            $hasExpectedSkills = true;
            break;
        }
    }
    
    if (!$hasExpectedSkills) {
        echo "<p style='color: orange;'><strong>Problem:</strong> Text is extracted but doesn't contain expected skills.</p>";
        echo "<p><strong>Possible causes:</strong></p>";
        echo "<ul>";
        echo "<li>DOCX file doesn't contain the skills section</li>";
        echo "<li>Skills are in images (not extractable text)</li>";
        echo "<li>Skills are in a different format than expected</li>";
        echo "</ul>";
    } else {
        echo "<p style='color: green;'><strong>Good:</strong> Text extraction is working and contains expected skills.</p>";
        echo "<p><strong>Issue:</strong> Skill matching algorithm needs improvement.</p>";
    }
}

echo "<h2>Solutions:</h2>";
echo "<ol>";
echo "<li><strong>Check DOCX content:</strong> Open your DOCX file and verify it contains readable text (not just images)</li>";
echo "<li><strong>Try manual input:</strong> Use the dashboard 'Update Skills' feature to manually add your skills</li>";
echo "<li><strong>Test with simple DOCX:</strong> Create a simple DOCX with just the skills list</li>";
echo "<li><strong>Check file format:</strong> Ensure the file is actually a DOCX (not renamed PDF)</li>";
echo "</ol>";
?>
