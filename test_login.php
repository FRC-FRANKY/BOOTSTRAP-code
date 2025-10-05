<?php
// Only start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Test login functionality
echo "<h2>Login Test Page</h2>";

// Test the sample users
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

echo "<h3>Sample Users:</h3>";
foreach ($users as $user) {
    echo "<p><strong>Email:</strong> {$user['email']} | <strong>Password:</strong> {$user['password']} | <strong>Role:</strong> {$user['role']}</p>";
}

// Test form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    echo "<h3>Form Submission Test:</h3>";
    echo "<p><strong>Email received:</strong> '$email'</p>";
    echo "<p><strong>Password received:</strong> '$password'</p>";
    
    // Find user
    $user = null;
    foreach ($users as $u) {
        if ($u['email'] === $email && $u['password'] === $password) {
            $user = $u;
            break;
        }
    }
    
    if ($user) {
        echo "<p style='color: green;'><strong>✅ Login successful!</strong> User found: {$user['name']}</p>";
        
        // Set session
        $_SESSION['isLoggedIn'] = true;
        $_SESSION['user_id'] = $user['email'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['firstname'] = $user['firstname'];
        $_SESSION['lastname'] = $user['lastname'];
        
        echo "<p><a href='dashboard.php'>Go to Dashboard</a></p>";
    } else {
        echo "<p style='color: red;'><strong>❌ Login failed!</strong> User not found.</p>";
    }
}

echo "<hr>";
?>

<h3>Test Login Form:</h3>
<form method="post">
    <div>
        <label>Email:</label><br>
        <input type="text" name="email" value="jobseeker@demo.com" style="width: 300px;">
    </div>
    <br>
    <div>
        <label>Password:</label><br>
        <input type="password" name="password" value="password123" style="width: 300px;">
    </div>
    <br>
    <button type="submit">Test Login</button>
</form>

<p><a href="login.php">Back to Login Page</a></p>
