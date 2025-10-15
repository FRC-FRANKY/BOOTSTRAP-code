<?php
// Only start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Function to check if user is authenticated
function isAuthenticated() {
    return isset($_SESSION['isLoggedIn']) && $_SESSION['isLoggedIn'] === true;
}

// Function to get current user data
function getCurrentUser() {
    if (isAuthenticated()) {
        return [
            'id' => $_SESSION['user_id'] ?? '',
            'email' => $_SESSION['email'] ?? '',
            'name' => $_SESSION['name'] ?? '',
            'firstname' => $_SESSION['firstname'] ?? '',
            'lastname' => $_SESSION['lastname'] ?? '',
            'role' => $_SESSION['role'] ?? 'job_seeker'
        ];
    }
    return null;
}

// Function to require authentication
function requireAuth() {
    if (!isAuthenticated()) {
        header("Location: login.php");
        exit();
    }
}

// Function to check role permissions
function hasRole($requiredRole) {
    if (!isAuthenticated()) {
        return false;
    }
    
    $userRole = $_SESSION['role'] ?? 'job_seeker';
    
    // Admin has access to everything
    if ($userRole === 'admin') {
        return true;
    }
    
    // Check specific role
    return $userRole === $requiredRole;
}

// Function to require specific role
function requireRole($requiredRole) {
    requireAuth();
    
    if (!hasRole($requiredRole)) {
        $_SESSION['error'] = 'Access denied. Insufficient permissions.';
        header("Location: dashboard.php");
        exit();
    }
}

// Auto-check authentication on page load
if (isset($_GET['check_auth'])) {
    header('Content-Type: application/json');
    echo json_encode([
        'authenticated' => isAuthenticated(),
        'user' => getCurrentUser()
    ]);
    exit();
}
?>
