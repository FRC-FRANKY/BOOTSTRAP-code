<?php
// Only start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/db_connect.php';

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
    
    // Check duplicate email
    $check = $conn->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
    $check->bind_param('s', $email);
    $check->execute();
    $check->store_result();
    if ($check->num_rows > 0) {
        $_SESSION['registration_error'] = 'An account with that email already exists.';
        header('Location: Registration.php');
        exit();
    }
    $check->close();
    
    // Insert user
    $sql = 'INSERT INTO users (email, password_hash, role, name, firstname, lastname, job_title, phone, dob, bio, company_name, company_reg_number, company_address, company_phone, company_linkedin) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        'sssssssssssssss',
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
