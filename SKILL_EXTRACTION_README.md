# Skill Extraction Feature

This feature automatically extracts skills from PDF and DOCX resume files during user registration and displays them on the user's dashboard.

## Features

- **Automatic Skill Extraction**: Extracts skills from uploaded PDF, DOCX, and DOC files during registration
- **Comprehensive Skills Database**: Recognizes 100+ technical skills including programming languages, frameworks, databases, and tools
- **Dashboard Integration**: Displays extracted skills in the "Your Skills Profile" section
- **Manual Skill Updates**: Users can update their skills by uploading new resumes or adding skills manually
- **Progress Tracking**: Shows profile completion percentage based on number of skills

## How It Works

### 1. Registration Process
- Users upload their resume (PDF, DOCX, or DOC) during registration
- The system automatically extracts text from the resume
- Skills are identified using pattern matching against a comprehensive skills database
- Extracted skills are stored in the `user_skills` database table

### 2. Dashboard Display
- Extracted skills are displayed as badges in the "Your Skills Profile" section
- Profile completion percentage is calculated based on the number of skills
- Users can update their skills using the "Update Skills" button

### 3. Skill Update Process
- Users can upload a new resume to automatically extract and update skills
- Alternatively, users can manually add skills by typing them in
- The system combines both methods and updates the database

## Technical Implementation

### Database Schema
```sql
CREATE TABLE user_skills (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  skill_name VARCHAR(100) NOT NULL,
  confidence_score DECIMAL(3,2) DEFAULT 1.00,
  extracted_from VARCHAR(50) DEFAULT 'resume',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  UNIQUE KEY unique_user_skill (user_id, skill_name)
);
```

### Key Files
- `skill_extractor.php` - Main skill extraction class
- `process_registration.php` - Updated to include skill extraction during registration
- `dashboard.php` - Updated to display extracted skills
- `process_skill_update.php` - Handles skill updates via AJAX
- `vendor/smalot/pdfparser/Parser.php` - Enhanced PDF text extraction

### Skills Database
The system recognizes skills in the following categories:
- Programming Languages (PHP, JavaScript, Python, Java, etc.)
- Web Technologies (HTML, CSS, React, Angular, etc.)
- Databases (MySQL, PostgreSQL, MongoDB, etc.)
- Cloud & DevOps (AWS, Azure, Docker, Kubernetes, etc.)
- Mobile Development (React Native, Flutter, etc.)
- Data Science & Analytics (Machine Learning, TensorFlow, etc.)
- Version Control & Tools (Git, GitHub, Jira, etc.)
- Testing (Selenium, Jest, PHPUnit, etc.)
- Operating Systems (Linux, Windows, macOS, etc.)

## Usage

### For Job Seekers
1. During registration, upload your resume (PDF, DOCX, or DOC)
2. Skills will be automatically extracted and displayed on your dashboard
3. Use the "Update Skills" button to add new skills or upload an updated resume

### For Employers
- The system can be extended to match job requirements with candidate skills
- Skills data can be used for better job matching algorithms

## File Support

### PDF Files
- Uses enhanced PDF parser with fallback text extraction
- Supports both text-based and image-based PDFs (with limitations)
- Attempts to use system `pdftotext` command if available

### DOCX Files
- Extracts text from DOCX files using ZIP archive parsing
- Reads the main document.xml file and extracts text content
- Handles basic formatting and removes XML tags

### DOC Files
- Basic support through fallback text extraction
- Limited functionality compared to DOCX

## Error Handling
- Graceful fallback if skill extraction fails
- Registration continues even if skill extraction encounters errors
- User-friendly error messages for invalid file formats
- Logging of extraction errors for debugging

## Future Enhancements
- Integration with job matching algorithms
- Skill proficiency levels
- Industry-specific skill categories
- Resume parsing for additional information (experience, education)
- API endpoints for external skill validation

## Dependencies
- PHP 7.4+
- MySQL 5.7+
- File upload support
- ZIP extension for DOCX support (optional)
- Shell access for enhanced PDF parsing (optional)

## Security Considerations
- File type validation
- File size limits (5MB)
- Secure file upload handling
- SQL injection prevention
- XSS protection in skill display
