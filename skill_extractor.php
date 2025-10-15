<?php
/**
 * Skill Extraction Utility
 * Extracts skills from PDF and DOCX resume files
 */

class SkillExtractor {
    
    private $conn;
    private $skillsCache = [];
    private $categoriesCache = [];
    
    public function __construct($databaseConnection = null) {
        if ($databaseConnection) {
            $this->conn = $databaseConnection;
            $this->loadSkillsFromDatabase();
        } else {
            // Fallback to hardcoded skills for backward compatibility
            $this->loadHardcodedSkills();
        }
    }
    
    /**
     * Load skills from database
     */
    private function loadSkillsFromDatabase() {
        if (!$this->conn) {
            $this->loadHardcodedSkills();
            return;
        }
        
        try {
            // Load categories
            $categoriesQuery = "SELECT id, name, parent_id FROM skill_categories ORDER BY name";
            $result = $this->conn->query($categoriesQuery);
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $this->categoriesCache[$row['id']] = $row;
                }
            }
            
            // Load skills with aliases
            $skillsQuery = "SELECT s.id, s.name, s.category_id, s.aliases, c.name as category_name 
                           FROM skills s 
                           JOIN skill_categories c ON s.category_id = c.id 
                           ORDER BY s.popularity_score DESC";
            $result = $this->conn->query($skillsQuery);
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $this->skillsCache[$row['name']] = $row;
                    
                    // Add aliases to cache
                    if ($row['aliases']) {
                        $aliases = json_decode($row['aliases'], true);
                        if (is_array($aliases)) {
                            foreach ($aliases as $alias) {
                                $this->skillsCache[$alias] = $row;
                            }
                        }
                    }
                }
            }
        } catch (Exception $e) {
            // Fallback to hardcoded skills if database fails
            $this->loadHardcodedSkills();
        }
    }
    
    /**
     * Fallback to hardcoded skills
     */
    private function loadHardcodedSkills() {
        $this->skillsCache = [
            // Programming Languages
            'PHP', 'JavaScript', 'Python', 'Java', 'C#', 'C++', 'C', 'Ruby', 'Go', 'Rust', 'Swift', 'Kotlin', 'Scala', 'R', 'MATLAB', 'Perl', 'Lua', 'Dart', 'TypeScript',
            
            // Web Technologies
            'HTML', 'CSS', 'HTML5', 'CSS3', 'Bootstrap', 'Tailwind CSS', 'Sass', 'Less', 'jQuery', 'React', 'Angular', 'Vue.js', 'Node.js', 'Express.js', 'Laravel', 'Symfony', 'CodeIgniter', 'Django', 'Flask', 'Spring', 'ASP.NET', 'Ruby on Rails',
            
            // Databases
            'MySQL', 'PostgreSQL', 'MongoDB', 'SQLite', 'Oracle', 'SQL Server', 'Redis', 'Elasticsearch', 'Cassandra', 'DynamoDB', 'Firebase', 'MariaDB',
            
            // Cloud & DevOps
            'AWS', 'Azure', 'Google Cloud', 'Docker', 'Kubernetes', 'Jenkins', 'GitLab CI', 'GitHub Actions', 'Terraform', 'Ansible', 'Chef', 'Puppet', 'Vagrant',
            
            // Mobile Development
            'React Native', 'Flutter', 'Xamarin', 'Ionic', 'Cordova', 'PhoneGap', 'Android Studio', 'Xcode',
            
            // Data Science & Analytics
            'Machine Learning', 'Deep Learning', 'TensorFlow', 'PyTorch', 'Scikit-learn', 'Pandas', 'NumPy', 'Matplotlib', 'Seaborn', 'Jupyter', 'Tableau', 'Power BI', 'Apache Spark', 'Hadoop',
            
            // Version Control & Tools
            'Git', 'SVN', 'Mercurial', 'GitHub', 'GitLab', 'Bitbucket', 'Jira', 'Confluence', 'Slack', 'Trello', 'Asana', 'Figma', 'Sketch', 'Adobe XD',
            
            // Testing
            'Unit Testing', 'Integration Testing', 'Selenium', 'Jest', 'PHPUnit', 'JUnit', 'Cypress', 'TestNG', 'Mocha', 'Chai',
            
            // Operating Systems
            'Linux', 'Windows', 'macOS', 'Ubuntu', 'CentOS', 'Red Hat', 'Debian', 'Windows 10', 'Windows 11', 'Mac OS',
            
            // Cybersecurity & Security Tools
            'McAfee', 'SIEM', 'EPO', 'NSM', 'FireEye', 'CMS', 'ETP', 'Wireshark', 'TCPView', 'OllyDbg', 'WinDbg', 'GBD', 'Nmap', 'Metasploit', 'Burp Suite', 'Nessus', 'OpenVAS', 'Snort', 'Suricata', 'Splunk', 'QRadar', 'ArcSight', 'Carbon Black', 'CrowdStrike', 'Palo Alto', 'Check Point', 'Fortinet', 'Cisco', 'Juniper',
            
            // Network & Infrastructure
            'DNS', 'DHCP', 'Active Directory', 'LDAP', 'VPN', 'Firewall', 'Router', 'Switch', 'Load Balancer', 'Proxy', 'Mail Server', 'Web Server', 'Apache', 'Nginx', 'IIS', 'Tomcat', 'JBoss', 'WebLogic',
            
            // Google & Cloud Services
            'Google Workspace', 'G Suite', 'Google Cloud Platform', 'GCP', 'Google Drive', 'Google Docs', 'Google Sheets', 'Google Slides', 'Gmail', 'Google Calendar', 'Google Meet', 'Google Chat',
            
            // Other Technologies
            'REST API', 'GraphQL', 'SOAP', 'Microservices', 'Agile', 'Scrum', 'Kanban', 'CI/CD', 'DevOps', 'Blockchain', 'IoT', 'AR/VR', 'WebRTC', 'WebSocket', 'OAuth', 'JWT', 'SSL/TLS'
        ];
    }
    
    // Common technical skills database (legacy - kept for backward compatibility)
    private $skillsDatabase = [
        // Programming Languages
        'PHP', 'JavaScript', 'Python', 'Java', 'C#', 'C++', 'C', 'Ruby', 'Go', 'Rust', 'Swift', 'Kotlin', 'Scala', 'R', 'MATLAB', 'Perl', 'Lua', 'Dart', 'TypeScript',
        
        // Web Technologies
        'HTML', 'CSS', 'HTML5', 'CSS3', 'Bootstrap', 'Tailwind CSS', 'Sass', 'Less', 'jQuery', 'React', 'Angular', 'Vue.js', 'Node.js', 'Express.js', 'Laravel', 'Symfony', 'CodeIgniter', 'Django', 'Flask', 'Spring', 'ASP.NET', 'Ruby on Rails',
        
        // Databases
        'MySQL', 'PostgreSQL', 'MongoDB', 'SQLite', 'Oracle', 'SQL Server', 'Redis', 'Elasticsearch', 'Cassandra', 'DynamoDB', 'Firebase', 'MariaDB',
        
        // Cloud & DevOps
        'AWS', 'Azure', 'Google Cloud', 'Docker', 'Kubernetes', 'Jenkins', 'GitLab CI', 'GitHub Actions', 'Terraform', 'Ansible', 'Chef', 'Puppet', 'Vagrant',
        
        // Mobile Development
        'React Native', 'Flutter', 'Xamarin', 'Ionic', 'Cordova', 'PhoneGap', 'Android Studio', 'Xcode',
        
        // Data Science & Analytics
        'Machine Learning', 'Deep Learning', 'TensorFlow', 'PyTorch', 'Scikit-learn', 'Pandas', 'NumPy', 'Matplotlib', 'Seaborn', 'Jupyter', 'Tableau', 'Power BI', 'Apache Spark', 'Hadoop',
        
        // Version Control & Tools
        'Git', 'SVN', 'Mercurial', 'GitHub', 'GitLab', 'Bitbucket', 'Jira', 'Confluence', 'Slack', 'Trello', 'Asana', 'Figma', 'Sketch', 'Adobe XD',
        
        // Testing
        'Unit Testing', 'Integration Testing', 'Selenium', 'Jest', 'PHPUnit', 'JUnit', 'Cypress', 'TestNG', 'Mocha', 'Chai',
        
        // Operating Systems
        'Linux', 'Windows', 'macOS', 'Ubuntu', 'CentOS', 'Red Hat', 'Debian', 'Windows 10', 'Windows 11', 'Mac OS',
        
        // Cybersecurity & Security Tools
        'McAfee', 'SIEM', 'EPO', 'NSM', 'FireEye', 'CMS', 'ETP', 'Wireshark', 'TCPView', 'OllyDbg', 'WinDbg', 'GBD', 'Nmap', 'Metasploit', 'Burp Suite', 'Nessus', 'OpenVAS', 'Snort', 'Suricata', 'Splunk', 'QRadar', 'ArcSight', 'Carbon Black', 'CrowdStrike', 'Palo Alto', 'Check Point', 'Fortinet', 'Cisco', 'Juniper',
        
        // Network & Infrastructure
        'DNS', 'DHCP', 'Active Directory', 'LDAP', 'VPN', 'Firewall', 'Router', 'Switch', 'Load Balancer', 'Proxy', 'Mail Server', 'Web Server', 'Apache', 'Nginx', 'IIS', 'Tomcat', 'JBoss', 'WebLogic',
        
        // Google & Cloud Services
        'Google Workspace', 'G Suite', 'Google Cloud Platform', 'GCP', 'Google Drive', 'Google Docs', 'Google Sheets', 'Google Slides', 'Gmail', 'Google Calendar', 'Google Meet', 'Google Chat',
        
        // Other Technologies
        'REST API', 'GraphQL', 'SOAP', 'Microservices', 'Agile', 'Scrum', 'Kanban', 'CI/CD', 'DevOps', 'Blockchain', 'IoT', 'AR/VR', 'WebRTC', 'WebSocket', 'OAuth', 'JWT', 'SSL/TLS'
    ];
    
    /**
     * Extract text from PDF file
     */
    public function extractTextFromPDF($filePath) {
        try {
            // Try multiple extraction methods
            $text = '';
            
            // Method 1: Try to use pdftotext if available
            if (function_exists('shell_exec')) {
                $output = @shell_exec("pdftotext -layout " . escapeshellarg($filePath) . " - 2>/dev/null");
                if ($output && !empty(trim($output))) {
                    return strtolower($output);
                }
            }
            
            // Method 2: Use the existing PDF parser
            require_once __DIR__ . '/vendor/smalot/pdfparser/Parser.php';
            $parser = new \Smalot\PdfParser\Parser();
            $pdf = $parser->parseFile($filePath);
            $text = $pdf->getText();
            
            // If we got meaningful text, return it
            if (!empty($text) && strlen($text) > 50) {
                return $text;
            }
            
            // Method 3: Enhanced PDF text extraction
            $content = @file_get_contents($filePath);
            if ($content) {
                // Look for text streams in PDF
                preg_match_all('/BT\s+(.*?)\s+ET/s', $content, $matches);
                if (!empty($matches[1])) {
                    foreach ($matches[1] as $match) {
                        // Extract text from PDF text objects
                        preg_match_all('/\((.*?)\)/s', $match, $textMatches);
                        foreach ($textMatches[1] as $textMatch) {
                            $text .= ' ' . $textMatch;
                        }
                    }
                }
                
                // If still no text, try to extract from PDF structure
                if (empty($text)) {
                    // Look for common resume keywords and extract surrounding text
                    $keywords = ['skills', 'experience', 'education', 'certifications', 'technologies', 'tools'];
                    foreach ($keywords as $keyword) {
                        $pattern = '/[^a-zA-Z0-9\s]*' . preg_quote($keyword, '/') . '[^a-zA-Z0-9\s]*([a-zA-Z0-9\s\/\-\.,]+)/i';
                        if (preg_match($pattern, $content, $matches)) {
                            $text .= ' ' . $matches[1];
                        }
                    }
                }
            }
            
            return strtolower($text);
            
        } catch (Exception $e) {
            error_log("PDF extraction error: " . $e->getMessage());
            return $this->fallbackTextExtraction($filePath);
        }
    }
    
    /**
     * Extract text from DOCX file
     */
    public function extractTextFromDOCX($filePath) {
        try {
            // Enhanced DOCX text extraction using ZIP
            if (!class_exists('ZipArchive')) {
                error_log("ZipArchive class not available for DOCX extraction");
                return $this->fallbackTextExtraction($filePath);
            }
            
            $zip = new ZipArchive();
            if ($zip->open($filePath) === TRUE) {
                $text = '';
                
                // Extract from main document
                $document = $zip->getFromName('word/document.xml');
                if ($document) {
                    // Remove XML tags and extract text
                    $text = strip_tags($document);
                    $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
                    
                    // Clean up extra whitespace
                    $text = preg_replace('/\s+/', ' ', $text);
                    $text = trim($text);
                }
                
                // Also try to extract from headers and footers if main document is empty
                if (empty($text)) {
                    $files = ['word/header1.xml', 'word/header2.xml', 'word/footer1.xml', 'word/footer2.xml'];
                    foreach ($files as $file) {
                        $content = $zip->getFromName($file);
                        if ($content) {
                            $headerText = strip_tags($content);
                            $headerText = html_entity_decode($headerText, ENT_QUOTES, 'UTF-8');
                            $text .= ' ' . $headerText;
                        }
                    }
                }
                
                $zip->close();
                
                if (!empty($text)) {
                    return strtolower($text);
                }
            }
        } catch (Exception $e) {
            error_log("DOCX extraction error: " . $e->getMessage());
        }
        
        // Try alternative extraction method
        return $this->extractTextFromDOCXAlternative($filePath);
    }
    
    /**
     * Alternative DOCX extraction using file_get_contents and regex
     */
    public function extractTextFromDOCXAlternative($filePath) {
        try {
            // Read the file as binary
            $content = file_get_contents($filePath);
            if (!$content) {
                return '';
            }
            
            // Look for document.xml content in the ZIP
            if (preg_match('/word\/document\.xml.*?PK/', $content, $matches)) {
                // Extract the document.xml content
                $docStart = strpos($content, 'word/document.xml');
                if ($docStart !== false) {
                    // Find the end of the document.xml section
                    $docEnd = strpos($content, 'PK', $docStart + 100);
                    if ($docEnd !== false) {
                        $docContent = substr($content, $docStart, $docEnd - $docStart);
                        
                        // Extract text from XML
                        $text = strip_tags($docContent);
                        $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
                        $text = preg_replace('/\s+/', ' ', $text);
                        $text = trim($text);
                        
                        if (!empty($text)) {
                            return strtolower($text);
                        }
                    }
                }
            }
            
            // Fallback: try to extract any readable text
            $text = preg_replace('/[^\x20-\x7E\x0A\x0D]/', ' ', $content);
            $text = preg_replace('/\s+/', ' ', $text);
            $text = trim($text);
            
            return strtolower($text);
            
        } catch (Exception $e) {
            error_log("Alternative DOCX extraction error: " . $e->getMessage());
            return '';
        }
    }
    
    /**
     * Fallback text extraction for unsupported files
     */
    private function fallbackTextExtraction($filePath) {
        $content = @file_get_contents($filePath);
        if ($content) {
            // Remove binary characters and extract readable text
            $content = preg_replace('/[^\x20-\x7E\x0A\x0D]/', ' ', $content);
            return $content;
        }
        return '';
    }
    
    /**
     * Extract skills from text content
     */
    public function extractSkills($text) {
        $extractedSkills = [];
        $text = strtolower($text);
        
        // Check for each skill in the cache
        foreach ($this->skillsCache as $skillName => $skillData) {
            $skillLower = strtolower($skillName);
            
            // Exact match
            if (strpos($text, $skillLower) !== false) {
                $extractedSkills[] = $skillName;
                continue;
            }
            
            // Handle variations and common misspellings
            $variations = $this->getSkillVariations($skillName);
            foreach ($variations as $variation) {
                if (strpos($text, strtolower($variation)) !== false) {
                    $extractedSkills[] = $skillName;
                    break;
                }
            }
        }
        
        // Remove duplicates and return
        return array_unique($extractedSkills);
    }
    
    /**
     * Extract skills with category information
     */
    public function extractSkillsWithCategories($text) {
        $extractedSkills = [];
        $text = strtolower($text);
        
        foreach ($this->skillsCache as $skillName => $skillData) {
            if (strpos($text, strtolower($skillName)) !== false) {
                $extractedSkills[] = [
                    'name' => $skillName,
                    'category' => is_array($skillData) ? ($skillData['category_name'] ?? 'Other') : 'Other',
                    'category_id' => is_array($skillData) ? ($skillData['category_id'] ?? 0) : 0
                ];
            }
        }
        
        return $extractedSkills;
    }
    
    /**
     * Get common variations of skill names
     */
    private function getSkillVariations($skill) {
        $variations = [];
        
        switch (strtolower($skill)) {
            case 'javascript':
                $variations = ['js', 'ecmascript', 'nodejs', 'node.js'];
                break;
            case 'html':
                $variations = ['html5', 'hypertext markup language'];
                break;
            case 'css':
                $variations = ['css3', 'cascading style sheets'];
                break;
            case 'mysql':
                $variations = ['my sql', 'mysql database'];
                break;
            case 'postgresql':
                $variations = ['postgres', 'postgresql database'];
                break;
            case 'mongodb':
                $variations = ['mongo', 'mongo db', 'mongodb database'];
                break;
            case 'react':
                $variations = ['reactjs', 'react.js', 'react js'];
                break;
            case 'angular':
                $variations = ['angularjs', 'angular.js', 'angular js'];
                break;
            case 'vue.js':
                $variations = ['vuejs', 'vue.js', 'vue js', 'vue'];
                break;
            case 'node.js':
                $variations = ['nodejs', 'node.js', 'node js'];
                break;
            case 'express.js':
                $variations = ['expressjs', 'express.js', 'express js', 'express'];
                break;
            case 'machine learning':
                $variations = ['ml', 'machinelearning', 'machine learning'];
                break;
            case 'deep learning':
                $variations = ['dl', 'deeplearning', 'deep learning'];
                break;
            case 'artificial intelligence':
                $variations = ['ai', 'artificialintelligence', 'artificial intelligence'];
                break;
            // Cybersecurity variations
            case 'mcafee':
                $variations = ['mcafee siem', 'mcafee epo', 'mcafee nsm'];
                break;
            case 'fireeye':
                $variations = ['fireeye cms', 'fireeye etp'];
                break;
            case 'wireshark':
                $variations = ['wireshark/tcpview', 'tcpview'];
                break;
            case 'ollydbg':
                $variations = ['ollydbg/windbg/gbd', 'windbg', 'gbd'];
                break;
            case 'windows':
                $variations = ['windows 10', 'windows 11', 'windows 10, 11'];
                break;
            case 'mac os':
                $variations = ['macos', 'mac os x', 'mac osx'];
                break;
            case 'google workspace':
                $variations = ['g suite', 'google apps', 'google workspace'];
                break;
            case 'dns':
                $variations = ['dns servers', 'domain name system'];
                break;
            case 'mail server':
                $variations = ['email server', 'smtp server', 'exchange server'];
                break;
        }
        
        return $variations;
    }
    
    /**
     * Process resume file and extract skills
     */
    public function processResume($filePath, $fileType = null) {
        if (!file_exists($filePath)) {
            return [];
        }
        
        // Determine file type if not provided
        if (!$fileType) {
            $fileType = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        }
        
        $text = '';
        
        switch ($fileType) {
            case 'pdf':
                $text = $this->extractTextFromPDF($filePath);
                break;
            case 'docx':
            case 'doc':
                $text = $this->extractTextFromDOCX($filePath);
                break;
            default:
                $text = $this->fallbackTextExtraction($filePath);
                break;
        }
        
        if (empty($text)) {
            return [];
        }
        
        return $this->extractSkills($text);
    }
    
    /**
     * Save extracted skills to database
     */
    public function saveSkillsToDatabase($userId, $skills, $conn) {
        if (empty($skills) || !$conn) {
            return false;
        }
        
        try {
            // Clear existing skills for this user
            $deleteStmt = $conn->prepare("DELETE FROM user_skills WHERE user_id = ?");
            $deleteStmt->bind_param('i', $userId);
            $deleteStmt->execute();
            $deleteStmt->close();
            
            // Insert new skills and add to global skills database if not exists
            $insertStmt = $conn->prepare("INSERT INTO user_skills (user_id, skill_name, confidence_score, extracted_from) VALUES (?, ?, 1.00, 'manual')");
            
            foreach ($skills as $skill) {
                $insertStmt->bind_param('is', $userId, $skill);
                $insertStmt->execute();
                
                // Add skill to global database if it doesn't exist
                $this->addSkillToGlobalDatabase($skill, $conn);
            }
            
            $insertStmt->close();
            return true;
            
        } catch (Exception $e) {
            error_log("Error saving skills to database: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Add skill to global skills database if it doesn't exist
     */
    private function addSkillToGlobalDatabase($skillName, $conn) {
        try {
            // Check if skill already exists in global database
            $checkStmt = $conn->prepare("SELECT id FROM skills WHERE name = ?");
            $checkStmt->bind_param('s', $skillName);
            $checkStmt->execute();
            $result = $checkStmt->get_result();
            
            if ($result->num_rows === 0) {
                // Skill doesn't exist, add it to "Other" category
                $otherCategoryId = $this->getOrCreateOtherCategory($conn);
                
                $insertStmt = $conn->prepare("INSERT INTO skills (name, category_id, popularity_score) VALUES (?, ?, 1)");
                $insertStmt->bind_param('si', $skillName, $otherCategoryId);
                $insertStmt->execute();
                $insertStmt->close();
                
                // Refresh skills cache
                $this->loadSkillsFromDatabase();
            }
            
            $checkStmt->close();
            return true;
            
        } catch (Exception $e) {
            error_log("Error adding skill to global database: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get or create "Other" category for new skills
     */
    private function getOrCreateOtherCategory($conn) {
        // Check if "Other" category exists
        $checkStmt = $conn->prepare("SELECT id FROM skill_categories WHERE name = 'Other'");
        $checkStmt->execute();
        $result = $checkStmt->get_result();
        
        if ($result->num_rows > 0) {
            $categoryId = $result->fetch_assoc()['id'];
            $checkStmt->close();
            return $categoryId;
        }
        
        // Create "Other" category
        $insertStmt = $conn->prepare("INSERT INTO skill_categories (name, description) VALUES ('Other', 'User-added skills that don\'t fit into standard categories')");
        $insertStmt->execute();
        $categoryId = $conn->insert_id;
        $insertStmt->close();
        $checkStmt->close();
        
        return $categoryId;
    }
    
    /**
     * Get user skills from database
     */
    public function getUserSkills($userId, $conn) {
        try {
            $stmt = $conn->prepare("SELECT skill_name FROM user_skills WHERE user_id = ? ORDER BY created_at DESC");
            $stmt->bind_param('i', $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $skills = [];
            while ($row = $result->fetch_assoc()) {
                $skills[] = $row['skill_name'];
            }
            
            $stmt->close();
            return $skills;
            
        } catch (Exception $e) {
            error_log("Error getting user skills: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Add new skill to specific category
     */
    public function addSkillToCategory($skillName, $categoryName, $conn) {
        try {
            // Get category ID
            $categoryStmt = $conn->prepare("SELECT id FROM skill_categories WHERE name = ?");
            $categoryStmt->bind_param('s', $categoryName);
            $categoryStmt->execute();
            $categoryResult = $categoryStmt->get_result();
            
            if ($categoryResult->num_rows === 0) {
                // Create category if it doesn't exist
                $createCategoryStmt = $conn->prepare("INSERT INTO skill_categories (name) VALUES (?)");
                $createCategoryStmt->bind_param('s', $categoryName);
                $createCategoryStmt->execute();
                $categoryId = $conn->insert_id;
                $createCategoryStmt->close();
            } else {
                $categoryId = $categoryResult->fetch_assoc()['id'];
            }
            $categoryStmt->close();
            
            // Add skill to category
            $skillStmt = $conn->prepare("INSERT IGNORE INTO skills (name, category_id, popularity_score) VALUES (?, ?, 1)");
            $skillStmt->bind_param('si', $skillName, $categoryId);
            $skillStmt->execute();
            $skillStmt->close();
            
            // Refresh skills cache
            $this->loadSkillsFromDatabase();
            
            return true;
            
        } catch (Exception $e) {
            error_log("Error adding skill to category: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get all available categories
     */
    public function getCategories($conn) {
        try {
            $stmt = $conn->prepare("SELECT id, name FROM skill_categories ORDER BY name");
            $stmt->execute();
            $result = $stmt->get_result();
            
            $categories = [];
            while ($row = $result->fetch_assoc()) {
                $categories[] = $row;
            }
            
            $stmt->close();
            return $categories;
            
        } catch (Exception $e) {
            error_log("Error getting categories: " . $e->getMessage());
            return [];
        }
    }
}
?>
