<?php
// Only start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $fullname = $_POST['fullname'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'employee';
    
    // Debug: Log all form data
    error_log("Registration attempt: fullname='$fullname', email='$email', password='$password', role='$role'");
    error_log("POST data: " . print_r($_POST, true));
    
    // Additional fields based on role
    if ($role === 'employee') {
        $jobTitle = $_POST['jobTitle'] ?? '';
        $phone = $_POST['employeePhone'] ?? '';
        $dob = $_POST['dob'] ?? '';
        $bio = $_POST['bio'] ?? '';
    } else {
        $companyName = $_POST['companyName'] ?? '';
        $companyRegNumber = $_POST['companyRegNumber'] ?? '';
        $companyAddress = $_POST['companyAddress'] ?? '';
        $companyPhone = $_POST['companyPhone'] ?? '';
        $companyLinkedIn = $_POST['companyLinkedIn'] ?? '';
    }
    
    // Basic validation
    if (empty($fullname) || empty($email) || empty($password)) {
        $_SESSION['registration_error'] = 'Please fill in all required fields';
        header("Location: Registration.php");
        exit();
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['registration_error'] = 'Please enter a valid email address';
        header("Location: Registration.php");
        exit();
    }
    
    if (strlen($password) < 6) {
        $_SESSION['registration_error'] = 'Password must be at least 6 characters long';
        header("Location: Registration.php");
        exit();
    }
    
    // Split fullname into first and last name
    $nameParts = explode(' ', trim($fullname), 2);
    $firstname = $nameParts[0];
    $lastname = isset($nameParts[1]) ? $nameParts[1] : '';
    
    // Persist user to storage (JSON file). In production, use a database
    $storageFile = __DIR__ . '/data/users.json';
    if (!is_dir(__DIR__ . '/data')) {
        mkdir(__DIR__ . '/data', 0777, true);
    }
    $users = [];
    if (file_exists($storageFile)) {
        $json = file_get_contents($storageFile);
        $users = json_decode($json, true) ?: [];
    }
    // Prevent duplicate emails
    foreach ($users as $u) {
        if (isset($u['email']) && strcasecmp($u['email'], $email) === 0) {
            $_SESSION['registration_error'] = 'An account with that email already exists.';
            header("Location: Registration.php");
            exit();
        }
    }
    // Map registration roles to system roles & hash password
    $systemRole = ($role === 'employee') ? 'job_seeker' : 'employer';
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    $newUser = [
        'email' => $email,
        'passwordHash' => $passwordHash,
        'role' => $systemRole,
        'name' => $fullname,
        'firstname' => $firstname,
        'lastname' => $lastname,
        'job_title' => $jobTitle ?? null,
        'phone' => $phone ?? null,
        'dob' => $dob ?? null,
        'bio' => $bio ?? null,
        'company_name' => $companyName ?? null,
        'company_reg_number' => $companyRegNumber ?? null,
        'company_address' => $companyAddress ?? null,
        'company_phone' => $companyPhone ?? null,
        'company_linkedin' => $companyLinkedIn ?? null,
        'created_at' => date('c')
    ];
    $users[] = $newUser;
    file_put_contents($storageFile, json_encode($users, JSON_PRETTY_PRINT));

    // Store in SESSION
    $_SESSION['isLoggedIn'] = true;
    $_SESSION['user_id'] = $email;
    $_SESSION['email'] = $email;
    $_SESSION['name'] = $fullname;
    $_SESSION['firstname'] = $firstname;
    $_SESSION['lastname'] = $lastname;
    $_SESSION['role'] = $systemRole;
    
    // Store additional role-specific data
    if ($role === 'employee') {
        $_SESSION['job_title'] = $jobTitle;
        $_SESSION['phone'] = $phone;
        $_SESSION['dob'] = $dob;
        $_SESSION['bio'] = $bio;
    } else {
        $_SESSION['company_name'] = $companyName;
        $_SESSION['company_reg_number'] = $companyRegNumber;
        $_SESSION['company_address'] = $companyAddress;
        $_SESSION['company_phone'] = $companyPhone;
        $_SESSION['company_linkedin'] = $companyLinkedIn;
    }
    
    // Store in COOKIES (valid 7 days)
    setcookie("user_email", $email, time() + (7 * 24 * 3600), "/");
    setcookie("user_role", $role, time() + (7 * 24 * 3600), "/");
    setcookie("user_name", $fullname, time() + (7 * 24 * 3600), "/");
    setcookie("firstname", $firstname, time() + (7 * 24 * 3600), "/");
    setcookie("lastname", $lastname, time() + (7 * 24 * 3600), "/");
    
    // Clear any error messages
    unset($_SESSION['registration_error']);
    
    // Set success message
    $_SESSION['registration_success'] = "Registration successful! Welcome, $fullname! Redirecting to dashboard...";
    
    // Redirect to dashboard
    header("Location: dashboard.php");
    exit();
} else {
    // If not POST request, redirect to registration
    header("Location: Registration.php");
    exit();
}
?>
