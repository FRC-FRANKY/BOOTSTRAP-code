<?php
session_start();

// Include authentication check
require_once 'check_auth.php';
require_once __DIR__ . '/db_connect.php';
require_once __DIR__ . '/skill_extractor.php';

// Require authentication to view this page
requireAuth();

// Prepare jobs for dashboards
$currentUserId = $_SESSION['user_id'] ?? null;

// Get user skills for job seekers
$userSkills = [];
$userSkillsWithCategories = [];
if ($currentUserId) {
    // $currentUserId already contains the user ID from session
    $skillExtractor = new SkillExtractor($conn);
    $userSkills = $skillExtractor->getUserSkills($currentUserId, $conn);
    // Sanitize legacy/bad data: drop numeric-only or empty entries
    $userSkills = array_values(array_filter($userSkills, function($s){
      $s = trim((string)$s);
      return $s !== '' && !is_numeric($s);
    }));
    
    // Get skills with categories for enhanced display
    $userSkillsWithCategories = $skillExtractor->extractSkillsWithCategories(implode(' ', $userSkills));
}

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

// Recent applications for current user
$recentApplications = [];
$applicationsCount = 0;
if ($currentUserId) {
  $stmt = $conn->prepare('SELECT a.id, a.applied_at, IFNULL(a.status, \'Pending\') AS app_status, j.title, j.company_name, j.required_skills
                          FROM applications a
                          JOIN jobs j ON j.id = a.job_id
                          WHERE a.applicant_id = ?
                          ORDER BY a.applied_at DESC
                          LIMIT 10');
  if ($stmt) {
    $stmt->bind_param('i', $currentUserId);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
      $recentApplications[] = $row;
    }
    $stmt->close();
  }
  // Count total applications for stats
  $cnt = $conn->prepare('SELECT COUNT(*) AS c FROM applications WHERE applicant_id = ?');
  if ($cnt) {
    $cnt->bind_param('i', $currentUserId);
    $cnt->execute();
    $cRes = $cnt->get_result()->fetch_assoc();
    $applicationsCount = (int)($cRes['c'] ?? 0);
    $cnt->close();
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
          <?php if ($role === 'employer' || $role === 'admin') : ?>
            <li class="nav-item"><a class="nav-link" href="view_resume.php">View Resumes</a></li>
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
                <h3 class="stat-number" id="applicationsCount" data-server-rendered="true"><?php echo (int)$applicationsCount; ?></h3>
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
                <div class="d-flex justify-content-between align-items-center mb-3">
                  <h6 class="mb-0 text-white-50">Grouped by category</h6>
                  <button class="btn btn-light btn-sm rounded-pill fw-semibold" onclick="showSkillUpdateModal()">+ Add Skill</button>
                </div>

                <div class="skills-container" style="max-height: 260px; overflow: auto;">
                  <?php if (!empty($userSkillsWithCategories)): ?>
                    <?php 
                      // Group skills by category and sort categories alphabetically
                      $skillsByCategory = [];
                      foreach ($userSkillsWithCategories as $skill) {
                        $category = $skill['category'];
                        if (!isset($skillsByCategory[$category])) {
                          $skillsByCategory[$category] = [];
                        }
                        $skillsByCategory[$category][] = $skill['name'];
                      }
                      ksort($skillsByCategory);
                    ?>
                    <?php foreach ($skillsByCategory as $category => $skills): ?>
                      <div class="mb-2">
                        <div class="px-3 py-2 bg-dark bg-opacity-25 border border-secondary rounded d-flex justify-content-between align-items-center">
                          <span class="fw-semibold text-white"><?php echo htmlspecialchars($category); ?></span>
                          <span class="badge bg-secondary text-light border-0"><?php echo count($skills); ?></span>
                        </div>
                        <div class="pt-2 px-1">
                          <?php foreach ($skills as $skill): ?>
                            <span class="badge bg-primary me-1 mb-1"><?php echo htmlspecialchars($skill); ?></span>
                          <?php endforeach; ?>
                        </div>
                      </div>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <div class="alert alert-info mb-0">
                      No skills extracted. Add your skills here.
                    </div>
                  <?php endif; ?>
                </div>

                <div class="progress my-3">
                  <?php 
                    $skillCount = count($userSkills);
                    $completionPercentage = min(100, ($skillCount * 10)); // 10% per skill, max 100%
                  ?>
                  <div class="progress-bar" role="progressbar" style="width: <?php echo $completionPercentage; ?>%">
                    Profile Complete: <?php echo $completionPercentage; ?>%
                  </div>
                </div>
                <div class="d-flex gap-2">
                  <button class="btn btn-outline-primary btn-sm" onclick="showSkillUpdateModal()">Update Skills</button>
                </div>
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
                    <tbody id="applicationsTable" data-server-rendered="true">
                      <?php if (count($recentApplications) > 0): ?>
                        <?php foreach ($recentApplications as $app): ?>
                          <?php 
                            // Match score: overlap of user's skills and job required_skills
                            $reqCsv = $app['required_skills'] ?? '';
                            $reqList = array_values(array_filter(array_map('trim', explode(',', (string)$reqCsv))));
                            $userSet = array_map('strtolower', $userSkills);
                            $reqSet = array_map('strtolower', $reqList);
                            $overlap = 0;
                            foreach ($reqSet as $r) { if ($r !== '' && in_array($r, $userSet, true)) { $overlap++; } }
                            $den = max(count($reqSet), 1);
                            $score = (int) round(($overlap / $den) * 100);
                            $scoreColor = $score >= 90 ? 'success' : ($score >= 80 ? 'warning' : ($score >= 70 ? 'info' : 'secondary'));
                            $status = trim((string)($app['app_status'] ?? ''));
                            if ($status === '') { $status = 'Pending'; }
                            $statusColor = (
                              $status==='Under Review' ? 'warning' : (
                              $status==='Interview Scheduled' ? 'info' : (
                              $status==='Hired' ? 'success' : (
                              $status==='Rejected' ? 'danger' : (
                              $status==='Pending' ? 'secondary' : 'secondary'))))
                            );
                          ?>
                          <tr>
                            <td><?php echo htmlspecialchars($app['title']); ?></td>
                            <td><?php echo htmlspecialchars($app['company_name']); ?></td>
                            <td><?php echo htmlspecialchars(date('Y-m-d', strtotime($app['applied_at']))); ?></td>
                            <td><span class="badge bg-<?php echo $statusColor; ?>"><?php echo htmlspecialchars($status); ?></span></td>
                            <td><span class="badge bg-<?php echo $scoreColor; ?>"><?php echo $score; ?>%</span></td>
                            <td><button class="btn btn-sm btn-outline-primary">View</button></td>
                          </tr>
                        <?php endforeach; ?>
                      <?php else: ?>
                        <tr>
                          <td colspan="6" class="text-center text-muted py-4">No applications yet. <a href="jobs.php">Start applying for jobs!</a></td>
                        </tr>
                      <?php endif; ?>
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

  <!-- Skill Update Modal -->
  <div class="modal fade" id="skillUpdateModal" tabindex="-1" aria-labelledby="skillUpdateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="skillUpdateModalLabel">Update Your Skills</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="skillUpdateForm" enctype="multipart/form-data">
            <div class="mb-3">
              <label for="newResume" class="form-label">Upload New Resume</label>
              <input type="file" class="form-control" id="newResume" name="resume" accept=".pdf,.docx,.doc,application/pdf,application/vnd.openxmlformats-officedocument.wordprocessingml.document">
              <div class="form-text">Upload a new resume to automatically extract and update your skills.</div>
            </div>
            <div class="mb-3">
              <label for="manualSkills" class="form-label">Or Add Skills Manually</label>
              <input type="text" class="form-control" id="manualSkills" name="manualSkills" placeholder="e.g., JavaScript, React, Python">
              <div class="form-text">Separate multiple skills with commas.</div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-primary" onclick="updateSkills()">Update Skills</button>
        </div>
      </div>
    </div>
  </div>

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
  
  <script>
    // Skill update modal functions
    function showSkillUpdateModal() {
      const modal = new bootstrap.Modal(document.getElementById('skillUpdateModal'));
      modal.show();
    }
    
    function updateSkills() {
      const form = document.getElementById('skillUpdateForm');
      const formData = new FormData(form);
      
      // Show loading state
      const updateBtn = document.querySelector('#skillUpdateModal .btn-primary');
      const originalText = updateBtn.textContent;
      updateBtn.textContent = 'Updating...';
      updateBtn.disabled = true;
      
      fetch('process_skill_update.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          // Show success message
          showNotification('Skills updated successfully!', 'success');
          
          // Close modal
          const modal = bootstrap.Modal.getInstance(document.getElementById('skillUpdateModal'));
          modal.hide();
          
          // Reload page to show updated skills
          setTimeout(() => {
            window.location.reload();
          }, 1000);
        } else {
          showNotification(data.message || 'Failed to update skills', 'error');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred while updating skills', 'error');
      })
      .finally(() => {
        // Reset button state
        updateBtn.textContent = originalText;
        updateBtn.disabled = false;
      });
    }
    
    function showNotification(message, type) {
      // Create notification element
      const notification = document.createElement('div');
      notification.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show position-fixed`;
      notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
      notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      `;
      
      document.body.appendChild(notification);
      
      // Auto remove after 5 seconds
      setTimeout(() => {
        if (notification.parentNode) {
          notification.remove();
        }
      }, 5000);
    }
  </script>
</body>
</html>
