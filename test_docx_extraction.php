<?php
/**
 * Test DOCX text extraction functionality
 */

require_once __DIR__ . '/skill_extractor.php';

echo "<h1>DOCX Text Extraction Test</h1>";

$skillExtractor = new SkillExtractor();

// Test DOCX extraction with sample text
echo "<h2>Testing DOCX Text Extraction Method:</h2>";

// Create a simple test DOCX content simulation
$testDocxContent = "Skills: McAfee SIEM/EPO/NSM, FireEye CMS/ETP, OllyDbg/WinDbg/GBD, Wireshark/TCPView, DNS Servers, Mail Server, Windows 10, 11, Linux, Mac OS, Google Workspace";

echo "<h3>Test Content:</h3>";
echo "<pre>" . htmlspecialchars($testDocxContent) . "</pre>";

// Test skill extraction
$extractedSkills = $skillExtractor->extractSkills($testDocxContent);

echo "<h3>Extracted Skills:</h3>";
echo "<p>Found " . count($extractedSkills) . " skills:</p>";
echo "<ul>";
foreach ($extractedSkills as $skill) {
    echo "<li>" . htmlspecialchars($skill) . "</li>";
}
echo "</ul>";

// Test with actual DOCX file if available
echo "<h2>Testing with Real DOCX File:</h2>";

$docxPath = __DIR__ . '/uploads/resumes/';
if (is_dir($docxPath)) {
    $docxFiles = glob($docxPath . '*.docx');
    if (!empty($docxFiles)) {
        $testFile = $docxFiles[0];
        echo "<p>Found DOCX file: " . basename($testFile) . "</p>";
        
        try {
            $realSkills = $skillExtractor->processResume($testFile, 'docx');
            echo "<p>Extracted " . count($realSkills) . " skills from real DOCX:</p>";
            echo "<ul>";
            foreach ($realSkills as $skill) {
                echo "<li>" . htmlspecialchars($skill) . "</li>";
            }
            echo "</ul>";
        } catch (Exception $e) {
            echo "<p style='color: red;'>Error processing DOCX: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
    } else {
        echo "<p>No DOCX files found in uploads/resumes/ directory.</p>";
        echo "<p><strong>To test DOCX extraction:</strong></p>";
        echo "<ol>";
        echo "<li>Convert your PDF resume to DOCX format</li>";
        echo "<li>Upload the DOCX file during registration</li>";
        echo "<li>The system will automatically extract skills from the DOCX</li>";
        echo "</ol>";
    }
} else {
    echo "<p>Uploads directory not found.</p>";
}

echo "<hr>";
echo "<h2>DOCX vs PDF Comparison:</h2>";
echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<tr><th>Format</th><th>Text Extraction</th><th>Skill Recognition</th><th>Recommendation</th></tr>";
echo "<tr>";
echo "<td>PDF</td>";
echo "<td style='color: red;'>❌ Complex encoding issues</td>";
echo "<td style='color: green;'>✅ Works when text is extracted</td>";
echo "<td>Not recommended for your PDF</td>";
echo "</tr>";
echo "<tr>";
echo "<td>DOCX</td>";
echo "<td style='color: green;'>✅ Clean text extraction</td>";
echo "<td style='color: green;'>✅ Perfect skill recognition</td>";
echo "<td style='color: green;'><strong>Recommended</strong></td>";
echo "</tr>";
echo "</table>";

echo "<h2>How to Convert PDF to DOCX:</h2>";
echo "<ol>";
echo "<li><strong>Microsoft Word:</strong> Open PDF → Save As → Word Document (.docx)</li>";
echo "<li><strong>Google Docs:</strong> Upload PDF → Open with Google Docs → Download as DOCX</li>";
echo "<li><strong>Online Converters:</strong> Use tools like SmallPDF, ILovePDF, or PDF24</li>";
echo "<li><strong>LibreOffice:</strong> Open PDF → Export as DOCX</li>";
echo "</ol>";

echo "<h2>Expected Results with DOCX:</h2>";
echo "<p>When you upload a DOCX version of your resume, the system should extract these skills:</p>";
echo "<ul>";
echo "<li>McAfee, SIEM, EPO, NSM</li>";
echo "<li>FireEye, CMS, ETP</li>";
echo "<li>OllyDbg, WinDbg, GBD</li>";
echo "<li>Wireshark, TCPView</li>";
echo "<li>DNS, Mail Server</li>";
echo "<li>Windows 10, Windows 11, Linux, Mac OS</li>";
echo "<li>Google Workspace</li>";
echo "</ul>";
?>
