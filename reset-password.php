<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (empty($_SESSION['reset_token']) || empty($_SESSION['reset_email'])) {
    header('Location: forgot-password.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Reset Password</title>
  <link rel="icon" type="image/svg+xml" href="Images/log.png" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="css/forgot-password.css" rel="stylesheet" />
</head>
<body>
  <div class="bg-overlay"></div>
  <main class="container">
    <div class="row justify-content-center">
      <div class="col-12 col-sm-8 col-md-6 col-lg-5">
        <div class="card shadow-sm forgot-password-card">
          <div class="card-header border-0 pt-4 pb-0">
            <div class="d-flex align-items-center justify-content-center">
              <img src="Images/log.png" alt="Brand Logo" class="brand-logo">
              <h5 class="mb-0">Reset Password</h5>
            </div>
            <p class="text-muted mb-0 mt-2">Create a new password for your account.</p>
          </div>

          <div class="card-body">
            <?php if (!empty($_SESSION['reset_error'])): ?>
              <div class="alert alert-danger py-2 mb-3 small" role="alert">
                <?php echo htmlspecialchars($_SESSION['reset_error']); unset($_SESSION['reset_error']); ?>
              </div>
            <?php endif; ?>

            <form method="post" action="process_reset_password.php" novalidate>
              <div class="mb-3">
                <label class="form-label">New password</label>
                <input type="password" name="password" class="form-control" minlength="6" required />
                <div class="form-text">At least 6 characters.</div>
              </div>
              <div class="mb-3">
                <label class="form-label">Confirm password</label>
                <input type="password" name="confirm" class="form-control" minlength="6" required />
              </div>
              <div class="d-grid">
                <button class="btn btn-success btn-lg" type="submit">Update Password</button>
              </div>
              <div class="text-center mt-3">
                <a href="login.php" class="back">← Back to Login</a>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </main>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


