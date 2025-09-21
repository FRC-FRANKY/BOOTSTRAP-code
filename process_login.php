<?php
session_start();

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']) ? true : false;
    
    // Basic validation
    if (empty($email) || empty($password)) {
        $_SESSION['error'] = 'Please fill in all fields';
        header("Location: login.php");
        exit();
    }
    
    // Sample user database (in real app, this would be from database)
    $users = [
        [
            'email' => 'jobseeker@demo.com',
            'password' => 'password123',
            'role' => 'job_seeker',
            'name' => 'Job Seeker',
            'firstname' => 'John',
            'lastname' => 'Doe'
        ],
        [
            'email' => 'employer@demo.com',
            'password' => 'password123',
            'role' => 'employer',
            'name' => 'Employer',
            'firstname' => 'Jane',
            'lastname' => 'Smith'
        ],
        [
            'email' => 'admin@demo.com',
            'password' => 'password123',
            'role' => 'admin',
            'name' => 'Administrator',
            'firstname' => 'Admin',
            'lastname' => 'User'
        ]
    ];
    
    // Find user
    $user = null;
    foreach ($users as $u) {
        if ($u['email'] === $email && $u['password'] === $password) {
            $user = $u;
            break;
        }
    }
    
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
