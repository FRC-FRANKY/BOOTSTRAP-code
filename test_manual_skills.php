<?php
/**
 * Test manual skill extraction with the actual skills from the PDF
 */

require_once __DIR__ . '/skill_extractor.php';

echo "<h1>Manual Skills Test</h1>";

// The actual skills from your PDF resume
$actualSkillsFromPDF = [
    'McAfee SIEM/EPO/NSM',
    'FireEye CMS/ETP', 
    'OllyDbg/WinDbg/GBD',
    'Wireshark/TCPView',
    'DNS Servers',
    'Mail Server',
    'Windows 10, 11',
    'Linux',
    'Mac OS',
    'Google Workspace'
];

echo "<h2>Actual Skills from Your PDF:</h2>";
echo "<ul>";
foreach ($actualSkillsFromPDF as $skill) {
    echo "<li>" . htmlspecialchars($skill) . "</li>";
}
echo "</ul>";

$skillExtractor = new SkillExtractor();

echo "<h2>Testing Skill Extraction:</h2>";

// Test each skill individually
$extractedSkills = [];
foreach ($actualSkillsFromPDF as $skill) {
    $found = $skillExtractor->extractSkills($skill);
    if (!empty($found)) {
        $extractedSkills = array_merge($extractedSkills, $found);
        echo "<p><strong>" . htmlspecialchars($skill) . "</strong> → Found: " . implode(', ', $found) . "</p>";
    } else {
        echo "<p><strong>" . htmlspecialchars($skill) . "</strong> → No skills found</p>";
    }
}

echo "<h2>Final Extracted Skills:</h2>";
$finalSkills = array_unique($extractedSkills);
echo "<p>Total: " . count($finalSkills) . " skills</p>";
echo "<ul>";
foreach ($finalSkills as $skill) {
    echo "<li>" . htmlspecialchars($skill) . "</li>";
}
echo "</ul>";

echo "<h2>Testing with Combined Text:</h2>";
$combinedText = implode(' ', $actualSkillsFromPDF);
echo "<p>Combined text: " . htmlspecialchars($combinedText) . "</p>";

$combinedSkills = $skillExtractor->extractSkills($combinedText);
echo "<p>Skills found from combined text: " . count($combinedSkills) . "</p>";
echo "<ul>";
foreach ($combinedSkills as $skill) {
    echo "<li>" . htmlspecialchars($skill) . "</li>";
}
echo "</ul>";
?>
