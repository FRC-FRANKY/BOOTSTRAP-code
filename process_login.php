<?php
// Only start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']) ? true : false;
    
    // Debug: Log form data
    error_log("Form data received: email='$email', password='$password'");
    
    // Basic validation
    if (empty($email) || empty($password)) {
        $_SESSION['error'] = 'Please fill in all fields';
        header("Location: login.php");
        exit();
    }
    
    // Load users from storage (JSON). Keep demo users as fallback
    $storageFile = __DIR__ . '/data/users.json';
    $users = [];
    if (file_exists($storageFile)) {
        $json = file_get_contents($storageFile);
        $users = json_decode($json, true) ?: [];
    }
    // Append demo users (with hashed passwords) for testing
    $demoUsers = [
        [
            'email' => 'jobseeker@demo.com',
            'passwordHash' => password_hash('password123', PASSWORD_DEFAULT),
            'role' => 'job_seeker',
            'name' => 'Job Seeker',
            'firstname' => 'John',
            'lastname' => 'Doe'
        ],
        [
            'email' => 'employer@demo.com',
            'passwordHash' => password_hash('password123', PASSWORD_DEFAULT),
            'role' => 'employer',
            'name' => 'Employer',
            'firstname' => 'Jane',
            'lastname' => 'Smith'
        ],
        [
            'email' => 'admin@demo.com',
            'passwordHash' => password_hash('password123', PASSWORD_DEFAULT),
            'role' => 'admin',
            'name' => 'Administrator',
            'firstname' => 'Admin',
            'lastname' => 'User'
        ]
    ];
    // Merge only if not duplicate email
    foreach ($demoUsers as $du) {
        $exists = false;
        foreach ($users as $u) {
            if (isset($u['email']) && strcasecmp($u['email'], $du['email']) === 0) { $exists = true; break; }
        }
        if (!$exists) { $users[] = $du; }
    }
    
    // Find user
    $user = null;
    foreach ($users as $u) {
        if (isset($u['email']) && strcasecmp($u['email'], $email) === 0) {
            // Support both hashed and plain (legacy) passwords
            $ok = false;
            if (isset($u['passwordHash'])) {
                $ok = password_verify($password, $u['passwordHash']);
            } elseif (isset($u['password'])) {
                $ok = ($u['password'] === $password);
            }
            if ($ok) { $user = $u; break; }
        }
    }
    
    // Debug: Log the attempt (remove this in production)
    error_log("Login attempt: email=$email, password=$password, found=" . ($user ? 'yes' : 'no'));
    
    if ($user) {
        // Store in SESSION
        $_SESSION['isLoggedIn'] = true;
        $_SESSION['user_id'] = $user['email'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['firstname'] = $user['firstname'];
        $_SESSION['lastname'] = $user['lastname'];
        
        // Store in COOKIES if remember me is checked (valid 7 days)
        if ($remember) {
            setcookie("user_email", $user['email'], time() + (7 * 24 * 3600), "/");
            setcookie("user_role", $user['role'], time() + (7 * 24 * 3600), "/");
            setcookie("user_name", $user['name'], time() + (7 * 24 * 3600), "/");
        }
        
        // Clear any error messages
        unset($_SESSION['error']);
        
        // Redirect to dashboard
        header("Location: dashboard.php");
        exit();
    } else {
        $_SESSION['error'] = 'Invalid email or password';
        header("Location: login.php");
        exit();
    }
} else {
    // If not POST request, redirect to login
    header("Location: login.php");
    exit();
}
?>
