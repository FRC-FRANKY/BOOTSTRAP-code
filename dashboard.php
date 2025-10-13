<?php
session_start();

// Include authentication check
require_once 'check_auth.php';
require_once __DIR__ . '/db_connect.php';

// Require authentication to view this page
requireAuth();

// Prepare jobs for dashboards
$currentUserId = $_SESSION['user_id'] ?? null;

// Employer: jobs posted by current user
$employerJobs = [];
if ($currentUserId) {
  $stmt = $conn->prepare('SELECT id, title, company_name, location, IFNULL(created_at, NOW()) AS created_at FROM jobs WHERE posted_by = ? ORDER BY created_at DESC');
  if ($stmt) {
    $stmt->bind_param('i', $currentUserId);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
      $employerJobs[] = $row;
    }
    $stmt->close();
  }
}

// Job Seeker: latest jobs (limit 10)
$latestJobs = [];
$result = $conn->query('SELECT id, title, company_name, location, IFNULL(created_at, NOW()) AS created_at FROM jobs ORDER BY created_at DESC LIMIT 10');
if ($result) {
  while ($row = $result->fetch_assoc()) {
    $latestJobs[] = $row;
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Dashboard - JobFilter</title>
  <link rel="icon" type="image/svg+xml" href="Images/log.png" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="css/home.css" rel="stylesheet">
  <link href="css/dashboard.css" rel="stylesheet">
</head>
<body data-user-role="<?php echo htmlspecialchars($_SESSION['role'] ?? 'job_seeker'); ?>">

  <!-- Navbar -->
  <nav class="navbar navbar-light bg-light sticky-top shadow-sm navbar-glass">
    <div class="container">
      <a class="navbar-brand fw-bold" href="login.php">JobFilter</a>
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
          <h5 class="fw-bold mb-2" id="offcanvasName"><?php echo htmlspecialchars($_SESSION['name'] ?? 'Guest'); ?></h5>
          <p class="text-muted small mb-2"><?php echo htmlspecialchars($_SESSION['email'] ?? ''); ?></p>
          <p class="text-muted small mb-2">Role: <?php 
            $displayRole = $_SESSION['role'] ?? 'user';
            if ($displayRole === 'job_seeker') {
                echo 'Job Seeker';
            } elseif ($displayRole === 'employer') {
                echo 'Employer';
            } elseif ($displayRole === 'admin') {
                echo 'Administrator';
            } else {
                echo htmlspecialchars($displayRole);
            }
          ?></p>
          <a href="process_logout.php" class="btn btn-outline-danger btn-sm">Logout</a>
          <hr class="mt-3">
        </div>
        <ul class="navbar-nav ms-auto mb-2 mb-lg-0" id="navMenuList">
          <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
          <?php $role = $_SESSION['role'] ?? 'job_seeker'; ?>
          <?php if ($role === 'job_seeker' || $role === 'admin') : ?>
            <li class="nav-item"><a class="nav-link" href="jobs.php">Find Jobs</a></li>
          <?php endif; ?>
          <?php if ($role === 'employer' || $role === 'admin') : ?>
            <li class="nav-item"><a class="nav-link" href="post-job.php">Post Job</a></li>
          <?php endif; ?>
          <li class="nav-item"><a class="nav-link" href="about.php">About</a></li>
          <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
          <?php if ($role === 'admin') : ?>
            <li class="nav-item"><a class="nav-link" href="user-management.php">Users</a></li>
          <?php endif; ?>
          <li class="nav-item"><a class="nav-link" href="user-profile.php">Profile</a></li>
        </ul>
        </div>
      </div>
    </div>
  </nav>

  <!-- Dashboard Header -->
  <section class="py-4 bg-primary text-white">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-12 col-md-8">
          <h1 class="fw-bold mb-2">Dashboard</h1>
          <p class="mb-0">Here's your personalized dashboard with job insights and recommendations.</p>
        </div>
        <div class="col-12 col-md-4 text-md-end">
          <div class="d-flex justify-content-md-end gap-2">
            <button class="btn btn-light btn-sm" id="switchRoleBtn">Switch Role</button>
            <button class="btn btn-outline-light btn-sm" id="refreshBtn">Refresh</button>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Dashboard Content -->
  <section class="py-5">
    <div class="container">
      
      <!-- Role Selector -->
      <div class="row mb-4">
        <div class="col-12">
          <div class="card">
            <div class="card-body">
              <div class="d-flex justify-content-center">
                <div class="btn-group" role="group">
                  <input type="radio" class="btn-check" name="userRole" id="jobSeeker" checked>
                  <label class="btn btn-outline-primary" for="jobSeeker">Job Seeker</label>
                  <input type="radio" class="btn-check" name="userRole" id="employer">
                  <label class="btn btn-outline-primary" for="employer">Employer</label>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Job Seeker Dashboard -->
      <div id="jobSeekerDashboard">
        <!-- Stats Cards -->
        <div class="row mb-4">
          <div class="col-12 col-md-3 mb-3">
            <div class="card stat-card">
              <div class="card-body text-center">
                <div class="stat-icon mb-2">📊</div>
                <h3 class="stat-number" id="applicationsCount">12</h3>
                <p class="stat-label">Applications</p>
              </div>
            </div>
          </div>
          <div class="col-12 col-md-3 mb-3">
            <div class="card stat-card">
              <div class="card-body text-center">
                <div class="stat-icon mb-2">🎯</div>
                <h3 class="stat-number" id="matchesCount">8</h3>
                <p class="stat-label">Job Matches</p>
              </div>
            </div>
          </div>
          <div class="col-12 col-md-3 mb-3">
            <div class="card stat-card">
              <div class="card-body text-center">
                <div class="stat-icon mb-2">📈</div>
                <h3 class="stat-number" id="avgMatchScore">85%</h3>
                <p class="stat-label">Avg. Match Score</p>
              </div>
            </div>
          </div>
          <div class="col-12 col-md-3 mb-3">
            <div class="card stat-card">
              <div class="card-body text-center">
                <div class="stat-icon mb-2">📧</div>
                <h3 class="stat-number" id="responsesCount">3</h3>
                <p class="stat-label">Responses</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Skills Profile -->
        <div class="row mb-4">
          <div class="col-12 col-lg-8 mb-4">
            <div class="card">
              <div class="card-header">
                <h5 class="mb-0">Your Skills Profile</h5>
              </div>
              <div class="card-body">
                <div class="skills-container mb-3">
                  <span class="badge bg-primary me-2 mb-2">JavaScript</span>
                  <span class="badge bg-primary me-2 mb-2">React</span>
                  <span class="badge bg-primary me-2 mb-2">Node.js</span>
                  <span class="badge bg-primary me-2 mb-2">Python</span>
                  <span class="badge bg-primary me-2 mb-2">SQL</span>
                  <span class="badge bg-secondary me-2 mb-2">+ Add Skill</span>
                </div>
                <div class="progress mb-3">
                  <div class="progress-bar" role="progressbar" style="width: 85%">Profile Complete: 85%</div>
                </div>
                <button class="btn btn-outline-primary btn-sm">Update Skills</button>
              </div>
            </div>
          </div>
          <div class="col-12 col-lg-4 mb-4">
            <div class="card">
              <div class="card-header">
                <h5 class="mb-0">Recommended Skills</h5>
              </div>
              <div class="card-body">
                <div class="recommendation-item mb-2">
                  <span class="badge bg-warning me-2">TypeScript</span>
                  <small class="text-muted">High demand in your area</small>
                </div>
                <div class="recommendation-item mb-2">
                  <span class="badge bg-warning me-2">AWS</span>
                  <small class="text-muted">Popular with employers</small>
                </div>
                <div class="recommendation-item mb-2">
                  <span class="badge bg-warning me-2">Docker</span>
                  <small class="text-muted">Growing trend</small>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Recent Applications -->
        <div class="row mb-4">
          <div class="col-12">
            <div class="card">
              <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Recent Applications</h5>
                <a href="jobs.php" class="btn btn-primary btn-sm">View All</a>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-hover">
                    <thead>
                      <tr>
                        <th>Job Title</th>
                        <th>Company</th>
                        <th>Applied Date</th>
                        <th>Status</th>
                        <th>Match Score</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody id="applicationsTable">
                      <tr>
                        <td>Senior Frontend Developer</td>
                        <td>TechCorp Inc.</td>
                        <td>2024-01-15</td>
                        <td><span class="badge bg-warning">Under Review</span></td>
                        <td><span class="badge bg-success">95%</span></td>
                        <td><button class="btn btn-sm btn-outline-primary">View</button></td>
                      </tr>
                      <tr>
                        <td>Python Developer</td>
                        <td>StartupXYZ</td>
                        <td>2024-01-12</td>
                        <td><span class="badge bg-info">Interview Scheduled</span></td>
                        <td><span class="badge bg-warning">75%</span></td>
                        <td><button class="btn btn-sm btn-outline-primary">View</button></td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Employer Dashboard -->
      <div id="employerDashboard" style="display: none;">
        <!-- Stats Cards -->
        <div class="row mb-4">
          <div class="col-12 col-md-3 mb-3">
            <div class="card stat-card">
              <div class="card-body text-center">
                <div class="stat-icon mb-2">📋</div>
                <h3 class="stat-number" id="activeJobsCount">5</h3>
                <p class="stat-label">Active Jobs</p>
              </div>
            </div>
          </div>
          <div class="col-12 col-md-3 mb-3">
            <div class="card stat-card">
              <div class="card-body text-center">
                <div class="stat-icon mb-2">👥</div>
                <h3 class="stat-number" id="totalApplicants">24</h3>
                <p class="stat-label">Total Applicants</p>
              </div>
            </div>
          </div>
          <div class="col-12 col-md-3 mb-3">
            <div class="card stat-card">
              <div class="card-body text-center">
                <div class="stat-icon mb-2">⭐</div>
                <h3 class="stat-number" id="avgApplicantScore">78%</h3>
                <p class="stat-label">Avg. Applicant Score</p>
              </div>
            </div>
          </div>
          <div class="col-12 col-md-3 mb-3">
            <div class="card stat-card">
              <div class="card-body text-center">
                <div class="stat-icon mb-2">📈</div>
                <h3 class="stat-number" id="viewsCount">156</h3>
                <p class="stat-label">Job Views</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Job Management -->
        <div class="row mb-4">
          <div class="col-12">
            <div class="card">
              <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Job Management</h5>
                <a href="post-job.php" class="btn btn-primary btn-sm">Post New Job</a>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-hover">
                    <thead>
                      <tr>
                        <th>Job Title</th>
                        <th>Company</th>
                        <th>Posted Date</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody id="jobsTable" data-server-rendered="true">
                      <?php if (count($employerJobs) > 0) : ?>
                        <?php foreach ($employerJobs as $job): ?>
                          <tr>
                            <td><?php echo htmlspecialchars($job['title']); ?></td>
                            <td><?php echo htmlspecialchars($job['company_name']); ?></td>
                            <td><?php echo htmlspecialchars(date('Y-m-d', strtotime($job['created_at']))); ?></td>
                            <td>
                              <a href="jobs.php" class="btn btn-sm btn-outline-primary me-1">View</a>
                              <a href="#" class="btn btn-sm btn-outline-secondary disabled">Edit</a>
                            </td>
                          </tr>
                        <?php endforeach; ?>
                      <?php elseif (count($latestJobs) > 0) : ?>
                        <?php foreach ($latestJobs as $job): ?>
                          <tr>
                            <td><?php echo htmlspecialchars($job['title']); ?></td>
                            <td><?php echo htmlspecialchars($job['company_name']); ?></td>
                            <td><?php echo htmlspecialchars(date('Y-m-d', strtotime($job['created_at']))); ?></td>
                            <td>
                              <a href="jobs.php" class="btn btn-sm btn-outline-primary">View</a>
                            </td>
                          </tr>
                        <?php endforeach; ?>
                      <?php else: ?>
                        <tr><td colspan="4" class="text-muted">No jobs posted yet.</td></tr>
                      <?php endif; ?>
                    </tbody>
                  </table>
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

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <!-- Common JavaScript -->
  <script src="js/common.js"></script>
  
  <!-- Role Control -->
  <script src="js/role-control.js"></script>
  
  <script src="js/dashboard.js"></script>
</body>
</html>
