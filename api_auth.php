<?php
session_start();
header('Content-Type: application/json');

// Get the action from the request
$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'login':
        handleLogin();
        break;
    case 'logout':
        handleLogout();
        break;
    case 'check_auth':
        handleCheckAuth();
        break;
    case 'register':
        handleRegister();
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}

function handleLogin() {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Please fill in all fields']);
        return;
    }
    
    // Sample user database
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
        
        echo json_encode([
            'success' => true, 
            'message' => 'Login successful',
            'user' => [
                'email' => $user['email'],
                'name' => $user['name'],
                'role' => $user['role'],
                'firstname' => $user['firstname'],
                'lastname' => $user['lastname']
            ]
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid email or password']);
    }
}

function handleLogout() {
    // Clear all session variables
    $_SESSION = array();
    
    // Destroy the session
    session_destroy();
    
    echo json_encode(['success' => true, 'message' => 'Logged out successfully']);
}

function handleCheckAuth() {
    $isAuthenticated = isset($_SESSION['isLoggedIn']) && $_SESSION['isLoggedIn'] === true;
    
    if ($isAuthenticated) {
        echo json_encode([
            'success' => true,
            'authenticated' => true,
            'user' => [
                'email' => $_SESSION['email'] ?? '',
                'name' => $_SESSION['name'] ?? '',
                'role' => $_SESSION['role'] ?? 'job_seeker',
                'firstname' => $_SESSION['firstname'] ?? '',
                'lastname' => $_SESSION['lastname'] ?? ''
            ]
        ]);
    } else {
        echo json_encode([
            'success' => true,
            'authenticated' => false,
            'user' => null
        ]);
    }
}

function handleRegister() {
    $fullname = $_POST['fullname'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'employee';
    
    if (empty($fullname) || empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Please fill in all required fields']);
        return;
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Please enter a valid email address']);
        return;
    }
    
    if (strlen($password) < 6) {
        echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters long']);
        return;
    }
    
    // Split fullname into first and last name
    $nameParts = explode(' ', trim($fullname), 2);
    $firstname = $nameParts[0];
    $lastname = isset($nameParts[1]) ? $nameParts[1] : '';
    
    // Store in SESSION
    $_SESSION['isLoggedIn'] = true;
    $_SESSION['user_id'] = $email;
    $_SESSION['email'] = $email;
    $_SESSION['name'] = $fullname;
    $_SESSION['firstname'] = $firstname;
    $_SESSION['lastname'] = $lastname;
    $_SESSION['role'] = $role;
    
    echo json_encode([
        'success' => true,
        'message' => 'Registration successful',
        'user' => [
            'email' => $email,
            'name' => $fullname,
            'role' => $role,
            'firstname' => $firstname,
            'lastname' => $lastname
        ]
    ]);
}
?>
