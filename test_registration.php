<?php
// Only start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

echo "<h2>Registration Test</h2>";

// Test form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<h3>Form Data Received:</h3>";
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";
    
    $fullname = $_POST['fullname'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? '';
    
    echo "<h3>Validation Test:</h3>";
    echo "<p><strong>Full Name:</strong> '$fullname' " . (empty($fullname) ? '❌ EMPTY' : '✅ OK') . "</p>";
    echo "<p><strong>Email:</strong> '$email' " . (empty($email) ? '❌ EMPTY' : '✅ OK') . "</p>";
    echo "<p><strong>Password:</strong> '$password' " . (empty($password) ? '❌ EMPTY' : '✅ OK') . "</p>";
    echo "<p><strong>Role:</strong> '$role' " . (empty($role) ? '❌ EMPTY' : '✅ OK') . "</p>";
    
    if (!empty($fullname) && !empty($email) && !empty($password) && !empty($role)) {
        echo "<p style='color: green;'><strong>✅ All required fields are filled!</strong></p>";
        echo "<p><a href='process_registration.php' onclick='this.href+=\"?test=1\";'>Test with process_registration.php</a></p>";
    } else {
        echo "<p style='color: red;'><strong>❌ Some required fields are missing!</strong></p>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Registration Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 50px; }
        .form-group { margin: 10px 0; }
        input, select, textarea { padding: 8px; width: 300px; }
        button { padding: 10px 20px; background: #007bff; color: white; border: none; cursor: pointer; }
    </style>
</head>
<body>
    <h3>Test Registration Form:</h3>
    
    <form method="post">
        <div class="form-group">
            <label>Full Name:</label><br>
            <input type="text" name="fullname" value="John Doe" required>
        </div>
        
        <div class="form-group">
            <label>Email:</label><br>
            <input type="email" name="email" value="john@example.com" required>
        </div>
        
        <div class="form-group">
            <label>Password:</label><br>
            <input type="password" name="password" value="password123" required>
        </div>
        
        <div class="form-group">
            <label>Role:</label><br>
            <select name="role">
                <option value="employee">Employee</option>
                <option value="employer">Employer</option>
            </select>
        </div>
        
        <button type="submit">Test Registration</button>
    </form>
    
    <p><a href="Registration.php">Back to Main Registration</a></p>
</body>
</html>
