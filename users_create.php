<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/db_connect.php';

$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = trim($_POST['name'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $role = $_POST['role'] ?? 'job_seeker';
  $password = $_POST['password'] ?? '';

  if ($name === '' || $email === '' || $password === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $error = 'Please provide valid name, email, and password.';
  } else {
    $firstname = '';
    $lastname = '';
    $parts = explode(' ', $name, 2);
    $firstname = $parts[0] ?? '';
    $lastname = $parts[1] ?? '';
    $hash = password_hash($password, PASSWORD_DEFAULT);

    $chk = $conn->prepare("SELECT id FROM users WHERE email=? LIMIT 1");
    $chk->bind_param("s", $email);
    $chk->execute();
    $chk->store_result();
    if ($chk->num_rows > 0) {
      $error = 'Email already exists.';
    } else {
      $ins = $conn->prepare("INSERT INTO users (email, password_hash, role, name, firstname, lastname) VALUES (?, ?, ?, ?, ?, ?)");
      $ins->bind_param("ssssss", $email, $hash, $role, $name, $firstname, $lastname);
      if ($ins->execute()) {
        $success = 'User created successfully.';
        header('Location: user-management.php');
        exit;
      } else {
        $error = 'Failed to create user.';
      }
    }
    $chk->close();
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Create User</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="css/dashboard.css" rel="stylesheet">
  <link rel="icon" type="image/svg+xml" href="Images/log.png" />
</head>
<body class="container py-4">
  <h3>Create User</h3>
  <?php if ($error): ?><div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
  <form method="post">
    <div class="mb-3">
      <label class="form-label">Full Name</label>
      <input type="text" name="name" class="form-control" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Email</label>
      <input type="email" name="email" class="form-control" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Password</label>
      <input type="password" name="password" class="form-control" minlength="6" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Role</label>
      <select name="role" class="form-select">
        <option value="job_seeker">Job Seeker</option>
        <option value="employer">Employer</option>
        <option value="admin">Admin</option>
      </select>
    </div>
    <div class="d-flex gap-2">
      <button class="btn btn-primary" type="submit">Save</button>
      <a href="user-management.php" class="btn btn-secondary">Cancel</a>
    </div>
  </form>
</body>
</html>

