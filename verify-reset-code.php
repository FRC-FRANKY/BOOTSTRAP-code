<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$token = $_GET['token'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Verify Reset Code</title>
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
              <h5 class="mb-0">Verify Code</h5>
            </div>
            <p class="text-muted mb-0 mt-2">Enter the 6-digit code we sent.</p>
          </div>

          <div class="card-body">
            <?php if (!empty($_SESSION['reset_notice'])): ?>
              <div class="alert alert-info py-2 mb-3 small" role="alert">
                <?php echo htmlspecialchars($_SESSION['reset_notice']); unset($_SESSION['reset_notice']); ?>
              </div>
            <?php endif; ?>
            <?php if (!empty($_SESSION['reset_error'])): ?>
              <div class="alert alert-danger py-2 mb-3 small" role="alert">
                <?php echo htmlspecialchars($_SESSION['reset_error']); unset($_SESSION['reset_error']); ?>
              </div>
            <?php endif; ?>

            <form method="post" action="process_verify_code.php" novalidate>
              <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>" />
              <div class="mb-3">
                <label class="form-label">6-digit code</label>
                <input type="text" name="code" class="form-control" maxlength="6" pattern="^[0-9]{6}$" placeholder="e.g. 123456" required />
                <div class="invalid-feedback">Enter the 6-digit code.</div>
              </div>
              <div class="d-grid">
                <button class="btn btn-primary btn-lg" type="submit">Verify</button>
              </div>
              <div class="text-center mt-3">
                <a href="forgot-password.php" class="back">← Back</a>
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


