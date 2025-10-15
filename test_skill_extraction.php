<?php
/**
 * Test file for skill extraction functionality
 * This file demonstrates how the skill extraction works
 */

require_once __DIR__ . '/skill_extractor.php';

echo "<h1>Skill Extraction Test</h1>";

// Test the skill extractor with sample text
$skillExtractor = new SkillExtractor();

// Sample resume text (simulating extracted text from a resume)
$sampleResumeText = "
John Doe
Software Developer

Skills:
- JavaScript, React, Node.js
- Python, Django, Flask
- MySQL, PostgreSQL, MongoDB
- AWS, Docker, Git
- HTML, CSS, Bootstrap
- Machine Learning, TensorFlow
- Agile, Scrum methodologies

Experience:
- 3 years of PHP development
- Worked with Laravel framework
- Experience with REST APIs
- Knowledge of Linux systems
- Used Jenkins for CI/CD
";

echo "<h2>Sample Resume Text:</h2>";
echo "<pre>" . htmlspecialchars($sampleResumeText) . "</pre>";

// Extract skills from the sample text
$extractedSkills = $skillExtractor->extractSkills($sampleResumeText);

echo "<h2>Extracted Skills:</h2>";
echo "<p>Found " . count($extractedSkills) . " skills:</p>";
echo "<ul>";
foreach ($extractedSkills as $skill) {
    echo "<li>" . htmlspecialchars($skill) . "</li>";
}
echo "</ul>";

// Test with a real PDF file if available
$testPdfPath = __DIR__ . '/uploads/resumes/';
if (is_dir($testPdfPath)) {
    $pdfFiles = glob($testPdfPath . '*.pdf');
    if (!empty($pdfFiles)) {
        $testFile = $pdfFiles[0];
        echo "<h2>Testing with Real PDF File:</h2>";
        echo "<p>Testing with: " . basename($testFile) . "</p>";
        
        try {
            $realSkills = $skillExtractor->processResume($testFile, 'pdf');
            echo "<p>Extracted " . count($realSkills) . " skills from real PDF:</p>";
            echo "<ul>";
            foreach ($realSkills as $skill) {
                echo "<li>" . htmlspecialchars($skill) . "</li>";
            }
            echo "</ul>";
        } catch (Exception $e) {
            echo "<p style='color: red;'>Error processing PDF: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
    } else {
        echo "<p>No PDF files found in uploads/resumes/ directory for testing.</p>";
    }
} else {
    echo "<p>Uploads directory not found.</p>";
}

echo "<h2>Skills Database</h2>";
echo "<p>The system recognizes " . count($skillExtractor->skillsDatabase ?? []) . " different skills.</p>";

// Show some sample skills from the database
$reflection = new ReflectionClass($skillExtractor);
$property = $reflection->getProperty('skillsDatabase');
$property->setAccessible(true);
$skillsDb = $property->getValue($skillExtractor);

echo "<h3>Sample Skills from Database:</h3>";
echo "<ul>";
$sampleSkills = array_slice($skillsDb, 0, 20); // Show first 20 skills
foreach ($sampleSkills as $skill) {
    echo "<li>" . htmlspecialchars($skill) . "</li>";
}
echo "</ul>";
echo "<p>... and " . (count($skillsDb) - 20) . " more skills</p>";

echo "<hr>";
echo "<p><strong>Note:</strong> This is a test file. In production, skill extraction happens automatically during user registration.</p>";
?>
