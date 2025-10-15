<?php
// Only start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/db_connect.php';
require_once __DIR__ . '/skill_extractor.php';

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $fullname = trim($_POST['fullname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'employee';
    
    // Additional fields based on role
    $jobTitle = $_POST['jobTitle'] ?? null;
    $phone = $_POST['employeePhone'] ?? null;
    $dob = $_POST['dob'] ?? null;
    $bio = $_POST['bio'] ?? null;
    $companyName = $_POST['companyName'] ?? null;
    $companyRegNumber = $_POST['companyRegNumber'] ?? null;
    $companyAddress = $_POST['companyAddress'] ?? null;
    $companyPhone = $_POST['companyPhone'] ?? null;
    $companyLinkedIn = $_POST['companyLinkedIn'] ?? null;

    // Handle resume upload (PDF, DOCX, DOC)
    $resumePath = null;
    $extractedSkills = [];
    
    if (isset($_FILES['resume']) && is_array($_FILES['resume']) && ($_FILES['resume']['error'] === UPLOAD_ERR_OK)) {
        $allowedMime = ['application/pdf', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/msword'];
        $allowedExt = ['pdf', 'docx', 'doc'];
        $tmpPath = $_FILES['resume']['tmp_name'];
        $originalName = $_FILES['resume']['name'];
        $fileSize = (int)$_FILES['resume']['size'];

        $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($tmpPath) ?: 'application/octet-stream';

        if (!in_array($ext, $allowedExt, true) || !in_array($mime, $allowedMime, true)) {
            $_SESSION['registration_error'] = 'Please upload a valid PDF, DOCX, or DOC resume.';
            header('Location: Registration.php');
            exit();
        }
        if ($fileSize > 5 * 1024 * 1024) { // 5MB limit
            $_SESSION['registration_error'] = 'Resume file is too large (max 5MB).';
            header('Location: Registration.php');
            exit();
        }

        $uploadDir = __DIR__ . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'resumes';
        if (!is_dir($uploadDir)) {
            if (!mkdir($uploadDir, 0775, true) && !is_dir($uploadDir)) {
                $_SESSION['registration_error'] = 'Failed to prepare upload directory.';
                header('Location: Registration.php');
                exit();
            }
        }

        $safeBase = preg_replace('/[^a-zA-Z0-9._-]/', '_', $originalName);
        $uniqueName = time() . '_' . bin2hex(random_bytes(4)) . '_' . $safeBase;
        $destPath = $uploadDir . DIRECTORY_SEPARATOR . $uniqueName;

        if (!move_uploaded_file($tmpPath, $destPath)) {
            $_SESSION['registration_error'] = 'Failed to save uploaded resume.';
            header('Location: Registration.php');
            exit();
        }

        // Store relative path for serving later
        $resumePath = 'uploads/resumes/' . $uniqueName;
        
        // Extract skills from resume
        try {
            $skillExtractor = new SkillExtractor();
            $extractedSkills = $skillExtractor->processResume($destPath, $ext);
        } catch (Exception $e) {
            error_log("Skill extraction error: " . $e->getMessage());
            // Continue registration even if skill extraction fails
        }
        
    } elseif (($role === 'employee') && (!isset($_FILES['resume']) || $_FILES['resume']['error'] !== UPLOAD_ERR_NO_FILE)) {
        // If employee role, require a resume when a file was attempted but failed
        if (!isset($_FILES['resume']) || $_FILES['resume']['error'] === UPLOAD_ERR_NO_FILE) {
            $_SESSION['registration_error'] = 'Please upload your resume as PDF, DOCX, or DOC.';
            header('Location: Registration.php');
            exit();
        }
    }
    
    // Basic validation
    if ($fullname === '' || $email === '' || $password === '') {
        $_SESSION['registration_error'] = 'Please fill in all required fields';
        header('Location: Registration.php');
        exit();
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['registration_error'] = 'Please enter a valid email address';
        header('Location: Registration.php');
        exit();
    }
    if (strlen($password) < 6) {
        $_SESSION['registration_error'] = 'Password must be at least 6 characters long';
        header('Location: Registration.php');
        exit();
    }
    
    // Split fullname into first and last name
    $nameParts = explode(' ', trim($fullname), 2);
    $firstname = $nameParts[0];
    $lastname = isset($nameParts[1]) ? $nameParts[1] : '';
    
    // Map UI role to system role
    $systemRole = ($role === 'employee') ? 'job_seeker' : (($role === 'employer') ? 'employer' : 'job_seeker');
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    
    // Check duplicate email and role combination
    $check = $conn->prepare('SELECT id FROM users WHERE email = ? AND role = ? LIMIT 1');
    $check->bind_param('ss', $email, $systemRole);
    $check->execute();
    $check->store_result();
    if ($check->num_rows > 0) {
        $roleDisplay = ($systemRole === 'job_seeker') ? 'Employee' : 'Employer';
        $_SESSION['registration_error'] = "An account with that email already exists for the $roleDisplay role. You can register with a different role or use a different email.";
        header('Location: Registration.php');
        exit();
    }
    $check->close();
    
    // Insert user
    $sql = 'INSERT INTO users (email, password_hash, role, name, firstname, lastname, job_title, phone, dob, bio, resume_path, company_name, company_reg_number, company_address, company_phone, company_linkedin) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        'ssssssssssssssss',
        $email,
        $passwordHash,
        $systemRole,
        $fullname,
        $firstname,
        $lastname,
        $jobTitle,
        $phone,
        $dob,
        $bio,
        $resumePath,
        $companyName,
        $companyRegNumber,
        $companyAddress,
        $companyPhone,
        $companyLinkedIn
    );
    
    if (!$stmt->execute()) {
        $_SESSION['registration_error'] = 'Registration failed: ' . $stmt->error;
        header('Location: Registration.php');
        exit();
    }
    
    // Get the inserted user ID
    $userId = $conn->insert_id;
    
    // Save extracted skills to database if any were found
    if (!empty($extractedSkills) && $userId) {
        try {
            $skillExtractor = new SkillExtractor();
            $skillExtractor->saveSkillsToDatabase($userId, $extractedSkills, $conn);
        } catch (Exception $e) {
            error_log("Error saving skills during registration: " . $e->getMessage());
            // Continue registration even if skill saving fails
        }
    }
    
    // Store in SESSION
    $_SESSION['isLoggedIn'] = true;
    $_SESSION['user_id'] = $email;
    $_SESSION['email'] = $email;
    $_SESSION['name'] = $fullname;
    $_SESSION['firstname'] = $firstname;
    $_SESSION['lastname'] = $lastname;
    $_SESSION['role'] = $systemRole;
    
    // Cookies (7 days)
    setcookie('user_email', $email, time() + (7 * 24 * 3600), '/');
    setcookie('user_role', $systemRole, time() + (7 * 24 * 3600), '/');
    setcookie('user_name', $fullname, time() + (7 * 24 * 3600), '/');
    setcookie('firstname', $firstname, time() + (7 * 24 * 3600), '/');
    setcookie('lastname', $lastname, time() + (7 * 24 * 3600), '/');
    
    unset($_SESSION['registration_error']);
    $_SESSION['registration_success'] = "Registration successful! Welcome, $fullname! Redirecting to dashboard...";
    header('Location: dashboard.php');
    exit();
} else {
    header('Location: Registration.php');
    exit();
}
?>
