<?php
// Only start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $subject = $_POST['subject'] ?? '';
    $message = $_POST['message'] ?? '';
    
    // Basic validation
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $_SESSION['contact_error'] = 'Please fill in all fields';
        header("Location: contact.php");
        exit();
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['contact_error'] = 'Please enter a valid email address';
        header("Location: contact.php");
        exit();
    }
    
    // Store contact data in session for demo purposes
    $_SESSION['contact_submissions'][] = [
        'name' => $name,
        'email' => $email,
        'subject' => $subject,
        'message' => $message,
        'timestamp' => date('Y-m-d H:i:s')
    ];
    
    // Store in COOKIES for demo (valid 1 hour)
    setcookie("contact_name", $name, time() + 3600, "/");
    setcookie("contact_email", $email, time() + 3600, "/");
    
    // Clear error messages
    unset($_SESSION['contact_error']);
    $_SESSION['contact_success'] = 'Thank you! Your message has been sent. We\'ll get back to you soon.';
    
    // Redirect back to contact page
    header("Location: contact.php");
    exit();
} else {
    // If not POST request, redirect to contact page
    header("Location: contact.php");
    exit();
}
?>
