<?php
/**
 * Debug skill extraction from updated DOCX file
 */

require_once __DIR__ . '/db_connect.php';
require_once __DIR__ . '/skill_extractor.php';

echo "<h1>Debug Skill Update Issue</h1>";

// Check current skills in database
echo "<h2>Current Skills in Database:</h2>";
$result = $conn->query("SELECT skill_name FROM user_skills ORDER BY skill_name");
$currentSkills = [];
while ($row = $result->fetch_assoc()) {
    $currentSkills[] = $row['skill_name'];
}

echo "<p>Found " . count($currentSkills) . " skills in database:</p>";
echo "<ul>";
foreach ($currentSkills as $skill) {
    echo "<li>" . htmlspecialchars($skill) . "</li>";
}
echo "</ul>";

// Check the latest DOCX file
echo "<h2>Latest DOCX File Analysis:</h2>";
$uploadsPath = __DIR__ . '/uploads/resumes/';
$docxFiles = glob($uploadsPath . '*.docx');

if (empty($docxFiles)) {
    echo "<p style='color: red;'>No DOCX files found in uploads directory</p>";
    exit();
}

// Sort by modification time to get the latest
usort($docxFiles, function($a, $b) {
    return filemtime($b) - filemtime($a);
});

$latestDocx = $docxFiles[0];
echo "<p><strong>Latest DOCX file:</strong> " . basename($latestDocx) . "</p>";
echo "<p><strong>File modified:</strong> " . date('Y-m-d H:i:s', filemtime($latestDocx)) . "</p>";

// Extract text from the latest DOCX
$skillExtractor = new SkillExtractor();
$extractedText = $skillExtractor->extractTextFromDOCX($latestDocx);

echo "<h3>Extracted Text from Latest DOCX:</h3>";
echo "<pre style='background: #f5f5f5; padding: 15px; border: 1px solid #ddd; max-height: 300px; overflow-y: auto;'>";
echo htmlspecialchars($extractedText);
echo "</pre>";

// Extract skills from the text
$newSkills = $skillExtractor->extractSkills($extractedText);

echo "<h3>Skills that should be extracted from latest DOCX:</h3>";
echo "<p>Found " . count($newSkills) . " skills:</p>";
echo "<ul>";
foreach ($newSkills as $skill) {
    echo "<li>" . htmlspecialchars($skill) . "</li>";
}
echo "</ul>";

// Compare with current database skills
echo "<h3>Comparison:</h3>";
echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<tr><th>Skill</th><th>In Database</th><th>In Latest DOCX</th><th>Status</th></tr>";

$allSkills = array_unique(array_merge($currentSkills, $newSkills));
sort($allSkills);

foreach ($allSkills as $skill) {
    $inDatabase = in_array($skill, $currentSkills);
    $inLatestDocx = in_array($skill, $newSkills);
    
    if ($inDatabase && $inLatestDocx) {
        $status = "✅ Both";
        $color = "green";
    } elseif ($inDatabase && !$inLatestDocx) {
        $status = "⚠️ Only in DB (old)";
        $color = "orange";
    } elseif (!$inDatabase && $inLatestDocx) {
        $status = "🆕 Only in DOCX (new)";
        $color = "blue";
    } else {
        $status = "❌ Neither";
        $color = "red";
    }
    
    echo "<tr>";
    echo "<td>" . htmlspecialchars($skill) . "</td>";
    echo "<td>" . ($inDatabase ? "Yes" : "No") . "</td>";
    echo "<td>" . ($inLatestDocx ? "Yes" : "No") . "</td>";
    echo "<td style='color: $color;'>$status</td>";
    echo "</tr>";
}
echo "</table>";

// Check if the file was actually updated
echo "<h3>File Update Check:</h3>";
$fileStats = stat($latestDocx);
echo "<p><strong>File size:</strong> " . number_format($fileStats['size']) . " bytes</p>";
echo "<p><strong>Last modified:</strong> " . date('Y-m-d H:i:s', $fileStats['mtime']) . "</p>";

// Check if there are multiple DOCX files
if (count($docxFiles) > 1) {
    echo "<h3>All DOCX Files (sorted by modification time):</h3>";
    echo "<ul>";
    foreach ($docxFiles as $i => $file) {
        $isLatest = ($i === 0);
        $marker = $isLatest ? " (LATEST)" : "";
        echo "<li>" . basename($file) . " - " . date('Y-m-d H:i:s', filemtime($file)) . "$marker</li>";
    }
    echo "</ul>";
}

echo "<h3>Possible Issues:</h3>";
echo "<ol>";
echo "<li><strong>File not actually updated:</strong> Check if you saved the DOCX file after making changes</li>";
echo "<li><strong>Wrong file being processed:</strong> The system might be processing an older version</li>";
echo "<li><strong>Skills not in the database:</strong> The new skills might not be in the skills database</li>";
echo "<li><strong>Cache issue:</strong> The system might be using cached results</li>";
echo "</ol>";

echo "<h3>Solutions:</h3>";
echo "<ol>";
echo "<li><strong>Re-upload the DOCX:</strong> Delete the old file and upload the new one</li>";
echo "<li><strong>Clear database skills:</strong> Delete existing skills and re-extract</li>";
echo "<li><strong>Check skills database:</strong> Ensure new skills are in the skills database</li>";
echo "<li><strong>Force re-extraction:</strong> Use the dashboard's 'UPDATE SKILLS' feature</li>";
echo "</ol>";
?>
