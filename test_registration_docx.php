<?php
/**
 * Test registration with DOCX file upload
 */

echo "<h1>Registration with DOCX Test</h1>";

echo "<h2>Registration Form Test</h2>";
echo "<p>Let's verify that the registration form accepts DOCX files:</p>";

// Check the registration form
$registrationFile = __DIR__ . '/Registration.php';
if (file_exists($registrationFile)) {
    $content = file_get_contents($registrationFile);
    
    // Check if DOCX is accepted
    if (strpos($content, '.docx') !== false) {
        echo "<p style='color: green;'>✅ Registration form accepts DOCX files</p>";
    } else {
        echo "<p style='color: red;'>❌ Registration form does not accept DOCX files</p>";
    }
    
    // Check if DOC is accepted
    if (strpos($content, '.doc') !== false) {
        echo "<p style='color: green;'>✅ Registration form accepts DOC files</p>";
    } else {
        echo "<p style='color: red;'>❌ Registration form does not accept DOC files</p>";
    }
    
    // Check MIME types
    if (strpos($content, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') !== false) {
        echo "<p style='color: green;'>✅ Registration form accepts DOCX MIME type</p>";
    } else {
        echo "<p style='color: red;'>❌ Registration form does not accept DOCX MIME type</p>";
    }
} else {
    echo "<p style='color: red;'>❌ Registration.php file not found</p>";
}

echo "<h2>Process Registration Test</h2>";
$processFile = __DIR__ . '/process_registration.php';
if (file_exists($processFile)) {
    $content = file_get_contents($processFile);
    
    // Check if DOCX is handled
    if (strpos($content, 'docx') !== false) {
        echo "<p style='color: green;'>✅ Process registration handles DOCX files</p>";
    } else {
        echo "<p style='color: red;'>❌ Process registration does not handle DOCX files</p>";
    }
    
    // Check if skill extraction is called
    if (strpos($content, 'SkillExtractor') !== false) {
        echo "<p style='color: green;'>✅ Process registration includes skill extraction</p>";
    } else {
        echo "<p style='color: red;'>❌ Process registration does not include skill extraction</p>";
    }
} else {
    echo "<p style='color: red;'>❌ process_registration.php file not found</p>";
}

echo "<h2>Step-by-Step Instructions for DOCX Testing:</h2>";
echo "<ol>";
echo "<li><strong>Convert your PDF to DOCX:</strong>";
echo "<ul>";
echo "<li>Open your PDF resume in Microsoft Word</li>";
echo "<li>Save As → Word Document (.docx)</li>";
echo "<li>Or use Google Docs: Upload PDF → Open with Google Docs → Download as DOCX</li>";
echo "</ul>";
echo "</li>";

echo "<li><strong>Clear existing data:</strong>";
echo "<ul>";
echo "<li>Go to MySQL Workbench</li>";
echo "<li>Run: <code>DELETE FROM user_skills;</code></li>";
echo "<li>Run: <code>DELETE FROM users;</code></li>";
echo "</ul>";
echo "</li>";

echo "<li><strong>Register with DOCX:</strong>";
echo "<ul>";
echo "<li>Go to Registration.php</li>";
echo "<li>Fill out the form</li>";
echo "<li>Upload your DOCX resume</li>";
echo "<li>Submit registration</li>";
echo "</ul>";
echo "</li>";

echo "<li><strong>Check results:</strong>";
echo "<ul>";
echo "<li>Go to dashboard.php</li>";
echo "<li>Check 'Your Skills Profile' section</li>";
echo "<li>You should see all your cybersecurity skills displayed</li>";
echo "</ul>";
echo "</li>";
echo "</ol>";

echo "<h2>Expected Skills from Your Resume:</h2>";
echo "<div style='background: #f0f8ff; padding: 15px; border-left: 4px solid #007bff;'>";
echo "<p><strong>When you upload your DOCX resume, these skills should be extracted:</strong></p>";
echo "<ul>";
echo "<li>McAfee, SIEM, EPO, NSM</li>";
echo "<li>FireEye, CMS, ETP</li>";
echo "<li>OllyDbg, WinDbg, GBD</li>";
echo "<li>Wireshark, TCPView</li>";
echo "<li>DNS, Mail Server</li>";
echo "<li>Windows 10, Windows 11, Linux, Mac OS</li>";
echo "<li>Google Workspace</li>";
echo "</ul>";
echo "<p><em>Total: ~22 skills should be extracted and displayed on your dashboard</em></p>";
echo "</div>";

echo "<h2>Troubleshooting:</h2>";
echo "<ul>";
echo "<li><strong>If no skills are extracted:</strong> Check if the DOCX file contains readable text (not just images)</li>";
echo "<li><strong>If some skills are missing:</strong> The skill database might need updates for specific tools</li>";
echo "<li><strong>If upload fails:</strong> Check file size (max 5MB) and file format</li>";
echo "<li><strong>If skills don't appear on dashboard:</strong> Check database connection and user_skills table</li>";
echo "</ul>";
?>
