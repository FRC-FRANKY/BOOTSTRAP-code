<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/db_connect.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { header('Location: user-management.php'); exit; }

$error = '';

// Load user
$stmt = $conn->prepare("SELECT id, name, email, role, firstname, lastname, status FROM users WHERE id=? LIMIT 1");
$stmt->bind_param("i", $id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$user) { header('Location: user-management.php'); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = trim($_POST['name'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $role = $_POST['role'] ?? 'job_seeker';
  $status = $_POST['status'] ?? 'active';
  $newPassword = $_POST['password'] ?? '';

  if ($name === '' || $email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $error = 'Please provide valid name and email.';
  } else {
    $parts = explode(' ', $name, 2);
    $firstname = $parts[0] ?? '';
    $lastname = $parts[1] ?? '';

    // ensure email unique for other users
    $chk = $conn->prepare("SELECT id FROM users WHERE email=? AND id<>? LIMIT 1");
    $chk->bind_param("si", $email, $id);
    $chk->execute();
    $chk->store_result();
    if ($chk->num_rows > 0) {
      $error = 'Email already in use by another account.';
    } else {
      if ($newPassword !== '') {
        $hash = password_hash($newPassword, PASSWORD_DEFAULT);
        $upd = $conn->prepare("UPDATE users SET email=?, password_hash=?, role=?, name=?, firstname=?, lastname=?, status=? WHERE id=?");
        $upd->bind_param("sssssssi", $email, $hash, $role, $name, $firstname, $lastname, $status, $id);
      } else {
        $upd = $conn->prepare("UPDATE users SET email=?, role=?, name=?, firstname=?, lastname=?, status=? WHERE id=?");
        $upd->bind_param("ssssssi", $email, $role, $name, $firstname, $lastname, $status, $id);
      }
      if ($upd->execute()) { header('Location: user-management.php'); exit; }
      else { $error = 'Failed to update user.'; }
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
  <title>Edit User</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="css/dashboard.css" rel="stylesheet">
  <link rel="icon" type="image/svg+xml" href="Images/log.png" />
</head>
<body class="container py-4">
  <h3>Edit User</h3>
  <?php if ($error): ?><div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
  <form method="post">
    <div class="mb-3">
      <label class="form-label">Full Name</label>
      <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($user['name']); ?>" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Email</label>
      <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
    </div>
    <div class="mb-3">
      <label class="form-label">New Password (leave blank to keep)</label>
      <input type="password" name="password" class="form-control" minlength="6">
    </div>
    <div class="mb-3">
      <label class="form-label">Role</label>
      <select name="role" class="form-select">
        <option value="job_seeker" <?php echo $user['role']==='job_seeker'?'selected':''; ?>>Job Seeker</option>
        <option value="employer" <?php echo $user['role']==='employer'?'selected':''; ?>>Employer</option>
        <option value="admin" <?php echo $user['role']==='admin'?'selected':''; ?>>Admin</option>
      </select>
    </div>
    <div class="mb-3">
      <label class="form-label">Status</label>
      <select name="status" class="form-select">
        <option value="active" <?php echo $user['status']==='active'?'selected':''; ?>>Active</option>
        <option value="suspended" <?php echo $user['status']==='suspended'?'selected':''; ?>>Suspended</option>
        <option value="pending" <?php echo $user['status']==='pending'?'selected':''; ?>>Pending</option>
      </select>
    </div>
    <div class="d-flex gap-2">
      <button class="btn btn-primary" type="submit">Update</button>
      <a href="user-management.php" class="btn btn-secondary">Cancel</a>
    </div>
  </form>
</body>
</html>

