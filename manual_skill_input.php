<?php
/**
 * Manual Skill Input Solution
 * Since DOCX extraction is having issues, this provides a direct way to add skills
 */

require_once __DIR__ . '/db_connect.php';
require_once __DIR__ . '/skill_extractor.php';

echo "<h1>Manual Skill Input Solution</h1>";

// Your cybersecurity skills from the image
$yourSkills = [
    'McAfee SIEM/EPO/NSM',
    'FireEye CMS/ETP', 
    'OllyDbg/WinDbg/GBD',
    'Wireshark/TCPView',
    'DNS Servers, Mail Server',
    'Windows 10, 11, Linux, Mac OS',
    'Google Workspace'
];

echo "<h2>Your Skills (from the image):</h2>";
echo "<ul>";
foreach ($yourSkills as $skill) {
    echo "<li>" . htmlspecialchars($skill) . "</li>";
}
echo "</ul>";

// Process each skill
$processedSkills = [];
foreach ($yourSkills as $skillGroup) {
    // Split by common separators
    $skills = preg_split('/[,/]/', $skillGroup);
    foreach ($skills as $skill) {
        $skill = trim($skill);
        if (!empty($skill)) {
            $processedSkills[] = $skill;
        }
    }
}

echo "<h2>Processed Individual Skills:</h2>";
echo "<ul>";
foreach ($processedSkills as $skill) {
    echo "<li>" . htmlspecialchars($skill) . "</li>";
}
echo "</ul>";

// Test skill extraction with the processed skills
$skillExtractor = new SkillExtractor();
$extractedSkills = $skillExtractor->extractSkills(implode(' ', $processedSkills));

echo "<h2>Skills that would be extracted:</h2>";
echo "<p>Found " . count($extractedSkills) . " skills:</p>";
echo "<ul>";
foreach ($extractedSkills as $skill) {
    echo "<li>" . htmlspecialchars($skill) . "</li>";
}
echo "</ul>";

// Check if we have a user to add skills to
$userId = null;
$result = $conn->query("SELECT id FROM users ORDER BY id DESC LIMIT 1");
if ($result && $result->num_rows > 0) {
    $userId = $result->fetch_assoc()['id'];
    echo "<h2>Adding Skills to User ID: " . $userId . "</h2>";
    
    // Clear existing skills for this user
    $stmt = $conn->prepare("DELETE FROM user_skills WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->close();
    
    // Add new skills
    $addedCount = 0;
    foreach ($extractedSkills as $skill) {
        $stmt = $conn->prepare("INSERT INTO user_skills (user_id, skill_name, confidence_score, extracted_from) VALUES (?, ?, 1.00, 'manual')");
        $stmt->bind_param("is", $userId, $skill);
        if ($stmt->execute()) {
            $addedCount++;
        }
        $stmt->close();
    }
    
    echo "<p style='color: green;'>✅ Added " . $addedCount . " skills to user " . $userId . "</p>";
    
    // Verify the skills were added
    $result = $conn->query("SELECT skill_name FROM user_skills WHERE user_id = $userId ORDER BY skill_name");
    echo "<h3>Skills in Database:</h3>";
    echo "<ul>";
    while ($row = $result->fetch_assoc()) {
        echo "<li>" . htmlspecialchars($row['skill_name']) . "</li>";
    }
    echo "</ul>";
    
} else {
    echo "<h2>No users found in database</h2>";
    echo "<p>Please register a user first, then run this script again.</p>";
}

echo "<hr>";
echo "<h2>Alternative Solutions:</h2>";

echo "<h3>Option 1: Use Dashboard Manual Input</h3>";
echo "<p>Go to your dashboard and use the 'Update Skills' feature to manually add your skills.</p>";

echo "<h3>Option 2: Create a Simple Text Resume</h3>";
echo "<p>Create a simple .txt file with your skills and upload it:</p>";
echo "<pre>";
echo "Skills:\n";
echo "McAfee SIEM/EPO/NSM\n";
echo "FireEye CMS/ETP\n";
echo "OllyDbg/WinDbg/GBD\n";
echo "Wireshark/TCPView\n";
echo "DNS Servers, Mail Server\n";
echo "Windows 10, 11, Linux, Mac OS\n";
echo "Google Workspace";
echo "</pre>";

echo "<h3>Option 3: Fix ZipArchive Issue</h3>";
echo "<p>The main issue is that ZipArchive class is not available. To fix this:</p>";
echo "<ol>";
echo "<li>Enable the zip extension in your PHP configuration</li>";
echo "<li>Restart your web server</li>";
echo "<li>Or use a different server environment</li>";
echo "</ol>";

echo "<h3>Option 4: Use Online DOCX to Text Converter</h3>";
echo "<p>Convert your DOCX to plain text and upload as .txt file:</p>";
echo "<ol>";
echo "<li>Open your DOCX in Microsoft Word</li>";
echo "<li>Save As → Plain Text (.txt)</li>";
echo "<li>Upload the .txt file during registration</li>";
echo "</ol>";
?>
