<?php
// Only start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Simple login test without JavaScript
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Simple test - just check if it's the demo user
    if ($email === 'jobseeker@demo.com' && $password === 'password123') {
        $_SESSION['isLoggedIn'] = true;
        $_SESSION['email'] = $email;
        $_SESSION['name'] = 'Job Seeker';
        $_SESSION['role'] = 'job_seeker';
        
        echo "<h2>✅ Login Successful!</h2>";
        echo "<p>Email: $email</p>";
        echo "<p>Password: $password</p>";
        echo "<p><a href='dashboard.php'>Go to Dashboard</a></p>";
        exit();
    } else {
        echo "<h2>❌ Login Failed!</h2>";
        echo "<p>Email: '$email'</p>";
        echo "<p>Password: '$password'</p>";
        echo "<p>Expected: jobseeker@demo.com / password123</p>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Simple Login Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 50px; }
        .form-group { margin: 10px 0; }
        input { padding: 8px; width: 200px; }
        button { padding: 10px 20px; background: #007bff; color: white; border: none; cursor: pointer; }
    </style>
</head>
<body>
    <h1>Simple Login Test (No JavaScript)</h1>
    
    <form method="post">
        <div class="form-group">
            <label>Email:</label><br>
            <input type="text" name="email" value="jobseeker@demo.com" required>
        </div>
        
        <div class="form-group">
            <label>Password:</label><br>
            <input type="password" name="password" value="password123" required>
        </div>
        
        <button type="submit">Login</button>
    </form>
    
    <p><a href="login.php">Back to Main Login</a></p>
</body>
</html>
