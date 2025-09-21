<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>User Record - JobFilter</title>
  <link rel="icon" type="image/svg+xml" href="Images/log.png" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="css/dashboard.css" rel="stylesheet">
</head>
<body>

   <!-- Navbar -->
   <nav class="navbar navbar-light bg-light sticky-top shadow-sm navbar-glass">
    <div class="container">
      <a class="navbar-brand fw-bold" href="dashboard.php">JobFilter</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#navMenu" aria-controls="navMenu" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="offcanvas offcanvas-end" tabindex="-1" id="navMenu" aria-labelledby="navMenuLabel">
        <div class="offcanvas-header">
          <h5 class="offcanvas-title" id="navMenuLabel">Menu</h5>
          <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
        <div class="text-center mb-3">
          <img id="offcanvasAvatar" src="Images/log.png" class="rounded-circle mb-2" width="72" height="72" alt="Avatar">
          <h5 class="fw-bold mb-2" id="offcanvasName">Guest</h5>
          <button type="button" class="btn btn-outline-secondary btn-sm" data-action="logout">Logout</button>
          <hr class="mt-3">
        </div>
        <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
          <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
          <li class="nav-item"><a class="nav-link" href="jobs.php">Find Jobs</a></li>
          <li class="nav-item"><a class="nav-link" href="post-job.php">Post Job</a></li>
          <li class="nav-item"><a class="nav-link" href="about.php">About</a></li>
          <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
        </ul>
        </div>
      </div>
    </div>
  </nav>

  <!-- Header -->
  <section class="py-4 bg-primary text-white">
    <div class="container">
      <div class="d-flex justify-content-between align-items-center flex-wrap">
        <div>
          <h1 class="fw-bold mb-1">User Record</h1>
          <p class="mb-0">Detailed information for a single user.</p>
        </div>
        <div class="d-flex gap-2">
          <a class="btn btn-light btn-sm" href="user-management.php">Back to Users</a>
        </div>
      </div>
    </div>
  </section>

  <!-- Content -->
  <section class="py-5">
    <div class="container">
      <div class="row g-4">
        <div class="col-12 col-lg-4">
          <div class="card h-100">
            <div class="card-body text-center">
              <img src="Images/icon.png" class="rounded-circle mb-3" width="120" height="120" alt="Avatar">
              <h5 class="fw-bold mb-1" id="recordName">John Doe</h5>
              <p class="text-muted mb-2" id="recordRole">Job Seeker</p>
              <span class="badge bg-success" id="recordStatus">Active</span>
              <hr>
              <div class="text-start small">
                <p class="mb-1"><strong>Email:</strong> <span id="recordEmail">john@example.com</span></p>
                <p class="mb-1"><strong>Phone:</strong> <span id="recordPhone">(+63) 912 345 6789</span></p>
                <p class="mb-1"><strong>Joined:</strong> <span id="recordJoined">2024-01-10</span></p>
              </div>
              <div class="d-grid gap-2 mt-3">
                <a href="user-profile.php" class="btn btn-outline-primary btn-sm">Impersonate/Profile</a>
                <button class="btn btn-outline-secondary btn-sm" id="resetPasswordBtn">Reset Password</button>
              </div>
            </div>
          </div>
        </div>

        <div class="col-12 col-lg-8">
          <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
              <h5 class="mb-0">Activity</h5>
              <button class="btn btn-sm btn-outline-secondary" id="refreshActivityBtn">Refresh</button>
            </div>
            <div class="card-body">
              <ul class="list-group list-group-flush" id="activityList">
                <li class="list-group-item">2024-02-01 • Applied to "Senior Frontend Developer"</li>
                <li class="list-group-item">2024-01-20 • Updated profile skills</li>
                <li class="list-group-item">2024-01-10 • Account created</li>
              </ul>
            </div>
          </div>

          <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
              <h5 class="mb-0">Permissions</h5>
              <button class="btn btn-sm btn-primary" id="savePermissionsBtn">Save</button>
            </div>
            <div class="card-body">
              <div class="row g-3">
                <div class="col-12 col-md-6">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="permViewJobs" checked>
                    <label class="form-check-label" for="permViewJobs">View Jobs</label>
                  </div>
                </div>
                <div class="col-12 col-md-6">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="permApplyJobs" checked>
                    <label class="form-check-label" for="permApplyJobs">Apply for Jobs</label>
                  </div>
                </div>
                <div class="col-12 col-md-6">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="permPostJobs">
                    <label class="form-check-label" for="permPostJobs">Post Jobs</label>
                  </div>
                </div>
                <div class="col-12 col-md-6">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="permAdmin">
                    <label class="form-check-label" for="permAdmin">Admin Access</label>
                  </div>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer class="footer py-4">
    <div class="container d-flex flex-wrap justify-content-between align-items-center">
      <p class="mb-0 text-muted">&copy; 2025 JobFilter. All rights reserved.</p>
      <div class="d-flex flex-wrap gap-3">
        <a href="#" class="text-decoration-none text-muted">Privacy</a>
        <a href="#" class="text-decoration-none text-muted">Terms</a>
        <a href="#" class="text-decoration-none text-muted">Support</a>
      </div>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="js/auth.js"></script>
  <script>
    function getParam(name){
      const url = new URL(window.location.href);
      return url.searchParams.get(name);
    }
    document.getElementById('resetPasswordBtn').addEventListener('click', function(){
      alert('Password reset link sent (demo)');
    });
    document.getElementById('savePermissionsBtn').addEventListener('click', function(){
      alert('Permissions saved (demo)');
    });
    document.getElementById('refreshActivityBtn').addEventListener('click', function(){
      alert('Activity refreshed (demo)');
    });
    const id = getParam('id');
    if(id){
      document.getElementById('recordName').textContent = id === '2' ? 'Jane Smith' : 'John Doe';
      document.getElementById('recordEmail').textContent = id === '2' ? 'jane@company.com' : 'john@example.com';
      document.getElementById('recordRole').textContent = id === '2' ? 'Employer' : 'Job Seeker';
    }
  </script>
</body>
</html>
