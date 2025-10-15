<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include authentication check
require_once 'check_auth.php';

// Require authentication to view this page
requireAuth();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Post a Job - JobFilter</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="css/home.css" rel="stylesheet">
  <link href="css/post-job.css" rel="stylesheet">
</head>
<body data-user-role="<?php echo htmlspecialchars($_SESSION['role'] ?? 'job_seeker'); ?>">

  
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
        </ul>
        </div>
      </div>
    </div>
  </nav>


  <!-- Post Job Section -->
  <section class="py-5">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
          <div class="text-center mb-5">
            <h1 class="fw-bold">Post a New Job</h1>
            <p class="text-muted">Create a job listing and let our AI match you with qualified candidates</p>
          </div>

          <!-- Job Posting Form -->
          <div class="card shadow-lg">
            <div class="card-body p-5">
              <?php if (!empty($_SESSION['job_error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                  <?php echo htmlspecialchars($_SESSION['job_error']); ?>
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['job_error']); ?>
              <?php endif; ?>
              <?php if (!empty($_SESSION['job_success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                  <?php echo htmlspecialchars($_SESSION['job_success']); ?>
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['job_success']); ?>
              <?php endif; ?>
              <form id="jobPostForm" class="needs-validation" novalidate action="process_job.php" method="post">
                
                <!-- Basic Information -->
                <div class="row mb-4">
                  <div class="col-12">
                    <h4 class="mb-3">Basic Information</h4>
                  </div>
                  
                  <div class="col-12 col-md-6 mb-3">
                    <label for="jobTitle" class="form-label">Job Title *</label>
                    <input type="text" class="form-control" id="jobTitle" name="jobTitle" required>
                    <div class="invalid-feedback">
                      Please provide a job title.
                    </div>
                  </div>
                  
                  <div class="col-12 col-md-6 mb-3">
                    <label for="companyName" class="form-label">Company Name *</label>
                    <input type="text" class="form-control" id="companyName" name="companyName" required>
                    <div class="invalid-feedback">
                      Please provide your company name.
                    </div>
                  </div>
                  
                  <div class="col-12 col-md-6 mb-3">
                    <label for="location" class="form-label">Location *</label>
                    <input type="text" class="form-control" id="location" name="location" placeholder="City, State or Remote" required>
                    <div class="invalid-feedback">
                      Please provide a location.
                    </div>
                  </div>
                  
                  <div class="col-12 col-md-6 mb-3">
                    <label for="jobCategory" class="form-label">Job Category *</label>
                    <select class="form-select" id="jobCategory" name="jobCategory" required>
                      <option value="">Select Category</option>
                      <option value="technology">Technology</option>
                      <option value="marketing">Marketing</option>
                      <option value="sales">Sales</option>
                      <option value="design">Design</option>
                      <option value="finance">Finance</option>
                      <option value="healthcare">Healthcare</option>
                      <option value="education">Education</option>
                      <option value="other">Other</option>
                    </select>
                    <div class="invalid-feedback">
                      Please select a job category.
                    </div>
                  </div>
                </div>

                <!-- Job Details -->
                <div class="row mb-4">
                  <div class="col-12">
                    <h4 class="mb-3">Job Details</h4>
                  </div>
                  
                  <div class="col-12 col-md-6 mb-3">
                    <label for="jobType" class="form-label">Job Type *</label>
                    <select class="form-select" id="jobType" name="jobType" required>
                      <option value="">Select Job Type</option>
                      <option value="full-time">Full Time</option>
                      <option value="part-time">Part Time</option>
                      <option value="contract">Contract</option>
                      <option value="internship">Internship</option>
                      <option value="remote">Remote</option>
                    </select>
                    <div class="invalid-feedback">
                      Please select a job type.
                    </div>
                  </div>
                  
                  <div class="col-12 col-md-6 mb-3">
                    <label for="experienceLevel" class="form-label">Experience Level *</label>
                    <select class="form-select" id="experienceLevel" name="experienceLevel" required>
                      <option value="">Select Experience Level</option>
                      <option value="entry">Entry Level (0-2 years)</option>
                      <option value="mid">Mid Level (3-5 years)</option>
                      <option value="senior">Senior Level (5+ years)</option>
                      <option value="executive">Executive Level</option>
                    </select>
                    <div class="invalid-feedback">
                      Please select an experience level.
                    </div>
                  </div>
                  
                  <div class="col-12 col-md-6 mb-3">
                    <label for="salaryMin" class="form-label">Salary Range (Min) *</label>
                    <div class="input-group">
                      <span class="input-group-text">$</span>
                      <input type="number" class="form-control" id="salaryMin" name="salaryMin" placeholder="50000" required>
                    </div>
                    <div class="invalid-feedback">
                      Please provide a minimum salary.
                    </div>
                  </div>
                  
                  <div class="col-12 col-md-6 mb-3">
                    <label for="salaryMax" class="form-label">Salary Range (Max) *</label>
                    <div class="input-group">
                      <span class="input-group-text">$</span>
                      <input type="number" class="form-control" id="salaryMax" name="salaryMax" placeholder="80000" required>
                    </div>
                    <div class="invalid-feedback">
                      Please provide a maximum salary.
                    </div>
                  </div>
                </div>

                <!-- Skills & Requirements -->
                <div class="row mb-4">
                  <div class="col-12">
                    <h4 class="mb-3">Skills & Requirements</h4>
                    <p class="text-muted">Add skills that are required for this position. Our AI will use these to match candidates.</p>
                  </div>
                  
                  <div class="col-12 mb-3">
                    <label for="requiredSkills" class="form-label">Required Skills *</label>
                    <input type="text" class="form-control" id="requiredSkills" name="requiredSkills" placeholder="e.g., JavaScript, React, Node.js, SQL" required>
                    <div class="form-text">Separate skills with commas</div>
                    <div class="invalid-feedback">
                      Please provide required skills.
                    </div>
                  </div>
                  
                  <div class="col-12 mb-3">
                    <label for="preferredSkills" class="form-label">Preferred Skills (Optional)</label>
                    <input type="text" class="form-control" id="preferredSkills" name="preferredSkills" placeholder="e.g., TypeScript, AWS, Docker">
                    <div class="form-text">Additional skills that would be nice to have</div>
                  </div>
                  
                  <div class="col-12 mb-3">
                    <label for="jobDescription" class="form-label">Job Description *</label>
                    <textarea class="form-control" id="jobDescription" name="jobDescription" rows="6" placeholder="Describe the role, responsibilities, and what you're looking for in a candidate..." required></textarea>
                    <div class="invalid-feedback">
                      Please provide a job description.
                    </div>
                  </div>
                </div>

                <!-- Company Information -->
                <div class="row mb-4">
                  <div class="col-12">
                    <h4 class="mb-3">Company Information</h4>
                  </div>
                  
                  <div class="col-12 mb-3">
                    <label for="companyDescription" class="form-label">Company Description</label>
                    <textarea class="form-control" id="companyDescription" name="companyDescription" rows="3" placeholder="Tell candidates about your company culture, mission, and what makes you unique..."></textarea>
                  </div>
                  
                  <div class="col-12 col-md-6 mb-3">
                    <label for="companyWebsite" class="form-label">Company Website</label>
                    <input type="url" class="form-control" id="companyWebsite" name="companyWebsite" placeholder="https://www.yourcompany.com">
                  </div>
                  
                  <div class="col-12 col-md-6 mb-3">
                    <label for="contactEmail" class="form-label">Contact Email *</label>
                    <input type="email" class="form-control" id="contactEmail" name="contactEmail" required>
                    <div class="invalid-feedback">
                      Please provide a valid email address.
                    </div>
                  </div>
                </div>

                <!-- Benefits & Perks -->
                <div class="row mb-4">
                  <div class="col-12">
                    <h4 class="mb-3">Benefits & Perks</h4>
                  </div>
                  
                  <div class="col-12 mb-3">
                    <label class="form-label">Select Benefits (Optional)</label>
                    <div class="row">
                      <div class="col-12 col-md-6">
                        <div class="form-check">
                          <input class="form-check-input" type="checkbox" id="healthInsurance" name="healthInsurance">
                          <label class="form-check-label" for="healthInsurance">
                            Health Insurance
                          </label>
                        </div>
                        <div class="form-check">
                          <input class="form-check-input" type="checkbox" id="dentalInsurance" name="dentalInsurance">
                          <label class="form-check-label" for="dentalInsurance">
                            Dental Insurance
                          </label>
                        </div>
                        <div class="form-check">
                          <input class="form-check-input" type="checkbox" id="visionInsurance" name="visionInsurance">
                          <label class="form-check-label" for="visionInsurance">
                            Vision Insurance
                          </label>
                        </div>
                        <div class="form-check">
                          <input class="form-check-input" type="checkbox" id="retirementPlan" name="retirementPlan">
                          <label class="form-check-label" for="retirementPlan">
                            401(k) / Retirement Plan
                          </label>
                        </div>
                      </div>
                      <div class="col-12 col-md-6">
                        <div class="form-check">
                          <input class="form-check-input" type="checkbox" id="paidTimeOff" name="paidTimeOff">
                          <label class="form-check-label" for="paidTimeOff">
                            Paid Time Off
                          </label>
                        </div>
                        <div class="form-check">
                          <input class="form-check-input" type="checkbox" id="flexibleSchedule" name="flexibleSchedule">
                          <label class="form-check-label" for="flexibleSchedule">
                            Flexible Schedule
                          </label>
                        </div>
                        <div class="form-check">
                          <input class="form-check-input" type="checkbox" id="remoteWork" name="remoteWork">
                          <label class="form-check-label" for="remoteWork">
                            Remote Work Options
                          </label>
                        </div>
                        <div class="form-check">
                          <input class="form-check-input" type="checkbox" id="professionalDevelopment" name="professionalDevelopment">
                          <label class="form-check-label" for="professionalDevelopment">
                            Professional Development
                          </label>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Submit Buttons -->
                <div class="row">
                  <div class="col-12 text-center">
                    <button type="button" class="btn btn-outline-secondary me-3" id="saveDraft">Save as Draft</button>
                    <button type="submit" class="btn btn-primary btn-lg">Post Job</button>
                  </div>
                </div>
              </form>
            </div>
          </div>

          <!-- Preview Section -->
          <div class="card mt-4" id="jobPreview" style="display: none;">
            <div class="card-header">
              <h5 class="mb-0">Job Preview</h5>
            </div>
            <div class="card-body">
              <div id="previewContent">
                <!-- Preview content will be populated here -->
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
  <!-- Authentication Service -->
  <script src="js/common.js"></script>
  <script src="js/role-control.js"></script>
  
  <script src="js/post-job.js"></script>
</body>
</html>
