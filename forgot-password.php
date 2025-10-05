<?php
session_start();
$resetError = $_SESSION['reset_error'] ?? '';
$resetNotice = $_SESSION['reset_notice'] ?? '';
unset($_SESSION['reset_error'], $_SESSION['reset_notice']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Forgot Password</title>
  <link rel="icon" type="image/svg+xml" href="Images/log.png" />
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="css/forgot-password.css" rel="stylesheet">
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
              <h5 class="mb-0">Forgot Password</h5>
            </div>
            <p class="text-muted mb-0 mt-2">Enter your email to reset your password.</p>
            
            <?php if ($resetError !== ''): ?>
              <div class="alert alert-danger py-2 mb-3 small" role="alert">
                <?php echo htmlspecialchars($resetError); ?>
              </div>
            <?php elseif ($resetNotice !== ''): ?>
              <div class="alert alert-success py-2 mb-3 small" role="alert">
                <?php echo htmlspecialchars($resetNotice); ?>
              </div>
            <?php endif; ?>
            
            <form class="needs-validation" novalidate action="process_forgot_password.php" method="post" autocomplete="on">
              <!-- Email -->
              <div class="mb-3">
                <label for="email" class="form-label">Email Address</label>
                <input
                  type="email"
                  class="form-control"
                  id="email"
                  name="email"
                  placeholder="Enter your email address"
                  value="<?php echo htmlspecialchars($_GET['email'] ?? ''); ?>"
                  required
                  aria-describedby="emailHelp"
                />
                <div id="emailHelp" class="form-text">We'll send a password reset link to this email.</div>
                <div class="invalid-feedback">
                  Please enter a valid email address.
                </div>
              </div>

              <!-- Submit -->
              <div class="d-grid">
                <button class="btn btn-primary btn-lg" type="submit">Send Reset Link</button>
              </div>

              <!-- Back to login link -->
              <div class="text-center mt-3">
                        <span class="text-muted">New here?</span>
                        <a href="login.php" class="back">← Back to Login</a>
                    </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </main>

  <!-- Bootstrap JS (for optional interactions) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="js/forgot-password.js"></script>
</body>
</html>
