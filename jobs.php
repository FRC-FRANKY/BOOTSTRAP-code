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
  <title>Find Jobs - JobFilter</title>
  <link rel="icon" type="image/svg+xml" href="Images/log.png" />
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <link href="css/home.css" rel="stylesheet">
  <link href="css/jobs.css" rel="stylesheet">
  <style>
    .input-group .input-group-text {
      border-right: none;
      border-radius: 0.375rem 0 0 0.375rem !important;
    }
    .input-group .form-control {
      border-left: none;
      border-right: none;
      border-radius: 0 !important;
    }
    .input-group .btn {
      border-left: none;
      border-radius: 0 0.375rem 0.375rem 0 !important;
    }
    .input-group .form-control:focus {
      border-left: none;
      border-right: none;
      box-shadow: none;
      border-color: #86b7fe;
    }
    .input-group:focus-within .input-group-text {
      border-color: #86b7fe;
    }
    .input-group:focus-within .btn {
      border-color: #86b7fe;
    }
    .input-group {
      border-radius: 0.375rem;
    }
    .salary-dropdown {
      transition: all 0.3s ease;
    }
    .btn-warning {
      background-color: #ffc107;
      border-color: #ffc107;
      color: #000;
    }
    .btn-warning:hover {
      background-color: #e0a800;
      border-color: #d39e00;
      color: #000;
    }
    /* Hide number input spinners */
    input[type="number"]::-webkit-outer-spin-button,
    input[type="number"]::-webkit-inner-spin-button {
      -webkit-appearance: none;
      margin: 0;
    }
    input[type="number"] {
      -moz-appearance: textfield;
    }
    /* Remove any processing indicators */
    .processing {
      display: none !important;
    }
    /* Salary dropdown styling to match image */
    .salary-menu {
      border-radius: 6px !important;
      padding: 0 !important;
      margin-top: 2px !important;
      z-index: 2000; /* ensure visible above elements */
      width: auto !important; /* allow to grow to fit content */
      min-width: 280px;       /* readable width for long prices */
      max-height: 240px;      /* enable vertical scrolling when long */
      overflow-y: auto;
      overflow-x: hidden;     /* never scroll horizontally */
    }
    /* Smooth scrolling feel */
    .salary-menu::-webkit-scrollbar {
      width: 8px;
      height: 8px;
    }
    .salary-menu::-webkit-scrollbar-thumb {
      background-color: rgba(0,0,0,0.2);
      border-radius: 4px;
    }
    .salary-menu::-webkit-scrollbar-track {
      background-color: transparent;
    }
    .salary-menu .dropdown-item {
      border: none !important;
      transition: background-color 0.2s ease;
    }
    .salary-menu .dropdown-item:hover {
      background-color: #FFF0C8 !important;
      color: #000 !important;
    }
    .salary-menu .dropdown-item:focus {
      background-color: #FFF0C8 !important;
      color: #000 !important;
      outline: none !important;
    }
    .salary-menu .dropdown-header {
      font-size: 0.875rem !important;
      text-transform: none !important;
      letter-spacing: normal !important;
    }
    .input-group .btn {
      background-color: #f8f9fa !important;
      border-color: #dee2e6 !important;
    }
    .input-group .btn:hover {
      background-color: #e9ecef !important;
      border-color: #dee2e6 !important;
    }
  </style>
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

  <!-- Search Section -->
  <section class="py-5 bg-light">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
          <h1 class="text-center mb-4">Find Your Perfect Job Match</h1>
          
          <!-- Search Form -->
          <div class="card shadow-sm">
            <div class="card-body p-4">
              <form id="jobSearchForm">
                <div class="row g-3">
                  <div class="col-12 col-md-3">
                    <input type="text" class="form-control" id="jobTitle" placeholder="Job title or keywords">
                  </div>
                  <div class="col-12 col-md-3">
                    <input type="text" class="form-control" id="location" placeholder="Location">
                  </div>
                  <div class="col-12 col-md-2">
                    <select class="form-select" id="category">
                      <option value="">All Categories</option>
                      <option value="technology">Technology</option>
                      <option value="marketing">Marketing</option>
                      <option value="sales">Sales</option>
                      <option value="design">Design</option>
                      <option value="finance">Finance</option>
                      <option value="healthcare">Healthcare</option>
                    </select>
                  </div>
                  <div class="col-12 col-md-2">
                    <div class="dropdown w-100">
                      <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 rounded-0">$</span>
                        <input type="text" class="form-control border-start-0 rounded-0" id="salaryRange" placeholder="Salary Range" inputmode="numeric">
                        <button class="btn btn-outline-secondary border-start-0 dropdown-toggle" type="button" id="salaryToggle" data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false"></button>
                      </div>
                      <ul class="dropdown-menu w-100 salary-menu" aria-labelledby="salaryToggle" id="salaryMenu" style="min-width: 200px; border: 1px solid #e0e0e0; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
                        <li><h6 class="dropdown-header" style="color: #696969; font-weight: 600; padding: 8px 16px 4px 16px; margin: 0;">Salary Range</h6></li>
                        <li><a class="dropdown-item salary-option" data-range="0-30000" href="#" style="padding: 8px 16px; color: #000;">$0 - $30,000</a></li>
                        <li><a class="dropdown-item salary-option" data-range="30000-60000" href="#" style="padding: 8px 16px; color: #000;">$30,000 - $60,000</a></li>
                        <li><a class="dropdown-item salary-option" data-range="60000-100000" href="#" style="padding: 8px 16px; color: #000;">$60,000 - $100,000</a></li>
                        <li><a class="dropdown-item salary-option" data-range="100000-150000" href="#" style="padding: 8px 16px; color: #000;">$100,000 - $150,000</a></li>
                        <li><a class="dropdown-item salary-option" data-range="150000+" href="#" style="padding: 8px 16px; color: #000;">$150,000+</a></li>
                        <li><hr class="dropdown-divider" style="margin: 4px 0; border-color: #e0e0e0;"></li>
                        <li><a class="dropdown-item" id="salaryClear" href="#" style="padding: 8px 16px; color: #000;">Clear</a></li>
                      </ul>
                    </div>
                  </div>
                  <div class="col-12 col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Search</button>
                  </div>
                </div>
                
                <!-- Advanced Filters -->
                <div class="row mt-3" id="advancedFilters" style="display: none;">
                  <div class="col-12">
                    <div class="row g-3">
                      <div class="col-12 col-md-3">
                        <select class="form-select" id="experience">
                          <option value="">Experience Level</option>
                          <option value="entry">Entry Level</option>
                          <option value="mid">Mid Level</option>
                          <option value="senior">Senior Level</option>
                        </select>
                      </div>
                      <div class="col-12 col-md-3">
                        <select class="form-select" id="jobType">
                          <option value="">Job Type</option>
                          <option value="full-time">Full Time</option>
                          <option value="part-time">Part Time</option>
                          <option value="contract">Contract</option>
                          <option value="remote">Remote</option>
                        </select>
                      </div>
                      <div class="col-12 col-md-3">
                        <button type="button" class="btn btn-warning w-100 fw-bold" id="clearFilters">
                          <i class="fas fa-eraser me-2"></i>Clear All Filters
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
                
                <div class="text-center mt-3">
                  <button type="button" class="btn btn-link" id="toggleFilters">Advanced Filters</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Job Listings -->
  <section class="py-5">
    <div class="container">
      <div class="row">
        <!-- Filters Sidebar -->
        <div class="col-12 col-lg-3">
          <div class="card shadow-sm mb-4">
            <div class="card-header">
              <h5 class="mb-0">Quick Filters</h5>
            </div>
            <div class="card-body">
              <h6>Skills Match</h6>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="skillMatch">
                <label class="form-check-label" for="skillMatch">
                  High skill match only
                </label>
              </div>
              
              <hr>
              
              <h6>Experience</h6>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="entryLevel">
                <label class="form-check-label" for="entryLevel">
                  Entry Level
                </label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="midLevel">
                <label class="form-check-label" for="midLevel">
                  Mid Level
                </label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="seniorLevel">
                <label class="form-check-label" for="seniorLevel">
                  Senior Level
                </label>
              </div>
              
              <hr>
              
              <h6>Job Type</h6>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="fullTime">
                <label class="form-check-label" for="fullTime">
                  Full Time
                </label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="partTime">
                <label class="form-check-label" for="partTime">
                  Part Time
                </label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="remote">
                <label class="form-check-label" for="remote">
                  Remote
                </label>
              </div>
            </div>
          </div>
        </div>

        <!-- Job Results -->
        <div class="col-12 col-lg-9">
          <div class="d-flex justify-content-between align-items-center mb-4">
            <h4>Job Results (<span id="jobCount">5</span>)</h4>
            <select class="form-select w-auto" id="sortBy">
              <option value="relevance">Sort by Relevance</option>
              <option value="date">Sort by Date</option>
              <option value="salary">Sort by Salary</option>
            </select>
          </div>

          <!-- Job Cards -->
          <div id="jobListings">
            <!-- Job Card 1 -->
            <div class="card shadow-sm mb-3 job-card" data-skills="javascript,react,node.js" data-experience="mid" data-salary="85000-120000" data-salary-max="120000" data-type="full-time" data-category="technology" data-date="2">
              <div class="card-body">
                <div class="row">
                  <div class="col-12 col-md-8">
                    <h5 class="card-title">Senior Frontend Developer</h5>
                    <p class="text-muted mb-2">TechCorp Inc. • New York, NY</p>
                    <div class="mb-2">
                      <span class="badge bg-primary me-1">JavaScript</span>
                      <span class="badge bg-primary me-1">React</span>
                      <span class="badge bg-primary me-1">Node.js</span>
                      <span class="badge bg-success me-1">95% Match</span>
                    </div>
                    <p class="card-text">We're looking for a talented frontend developer to join our growing team. Experience with modern JavaScript frameworks required.</p>
                  </div>
                  <div class="col-12 col-md-4 text-md-end">
                    <p class="text-success fw-bold mb-2">$85,000 - $120,000</p>
                    <p class="text-muted small mb-2">Posted 2 days ago</p>
                    <button class="btn btn-primary btn-sm">Apply Now</button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Job Card 2 -->
            <div class="card shadow-sm mb-3 job-card" data-skills="python,django,sql" data-experience="entry" data-salary="50000-70000" data-salary-max="70000" data-type="full-time" data-category="technology" data-date="7">
              <div class="card-body">
                <div class="row">
                  <div class="col-12 col-md-8">
                    <h5 class="card-title">Python Developer</h5>
                    <p class="text-muted mb-2">StartupXYZ • San Francisco, CA</p>
                    <div class="mb-2">
                      <span class="badge bg-primary me-1">Python</span>
                      <span class="badge bg-primary me-1">Django</span>
                      <span class="badge bg-primary me-1">SQL</span>
                      <span class="badge bg-warning me-1">75% Match</span>
                    </div>
                    <p class="card-text">Join our dynamic startup team! Looking for a Python developer with Django experience and strong problem-solving skills.</p>
                  </div>
                  <div class="col-12 col-md-4 text-md-end">
                    <p class="text-success fw-bold mb-2">$50,000 - $70,000</p>
                    <p class="text-muted small mb-2">Posted 1 week ago</p>
                    <button class="btn btn-primary btn-sm">Apply Now</button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Job Card 3 -->
            <div class="card shadow-sm mb-3 job-card" data-skills="ui,ux,figma" data-experience="mid" data-salary="70000-95000" data-salary-max="95000" data-type="remote" data-category="design" data-date="3">
              <div class="card-body">
                <div class="row">
                  <div class="col-12 col-md-8">
                    <h5 class="card-title">UI/UX Designer</h5>
                    <p class="text-muted mb-2">DesignStudio • Remote</p>
                    <div class="mb-2">
                      <span class="badge bg-primary me-1">UI Design</span>
                      <span class="badge bg-primary me-1">UX Design</span>
                      <span class="badge bg-primary me-1">Figma</span>
                      <span class="badge bg-info me-1">85% Match</span>
                    </div>
                    <p class="card-text">Remote opportunity for a creative UI/UX designer. Must have experience with Figma and user-centered design principles.</p>
                  </div>
                  <div class="col-12 col-md-4 text-md-end">
                    <p class="text-success fw-bold mb-2">$70,000 - $95,000</p>
                    <p class="text-muted small mb-2">Posted 3 days ago</p>
                    <button class="btn btn-primary btn-sm">Apply Now</button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Job Card 4 - High Salary -->
            <div class="card shadow-sm mb-3 job-card" data-skills="machine learning,ai,python,tensorflow" data-experience="senior" data-salary="150000-200000" data-salary-max="200000" data-type="full-time" data-category="technology" data-date="1">
              <div class="card-body">
                <div class="row">
                  <div class="col-12 col-md-8">
                    <h5 class="card-title">Senior AI/ML Engineer</h5>
                    <p class="text-muted mb-2">TechGiant Corp. • Seattle, WA</p>
                    <div class="mb-2">
                      <span class="badge bg-primary me-1">Machine Learning</span>
                      <span class="badge bg-primary me-1">AI</span>
                      <span class="badge bg-primary me-1">Python</span>
                      <span class="badge bg-primary me-1">TensorFlow</span>
                      <span class="badge bg-success me-1">98% Match</span>
                    </div>
                    <p class="card-text">Lead our AI initiatives and develop cutting-edge machine learning solutions. PhD preferred with 5+ years experience in AI/ML.</p>
                  </div>
                  <div class="col-12 col-md-4 text-md-end">
                    <p class="text-success fw-bold mb-2">$150,000 - $200,000</p>
                    <p class="text-muted small mb-2">Posted 1 day ago</p>
                    <button class="btn btn-primary btn-sm">Apply Now</button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Job Card 5 - Very High Salary -->
            <div class="card shadow-sm mb-3 job-card" data-skills="blockchain,ethereum,solidity,web3" data-experience="senior" data-salary="180000-250000" data-salary-max="250000" data-type="full-time" data-category="technology" data-date="4">
              <div class="card-body">
                <div class="row">
                  <div class="col-12 col-md-8">
                    <h5 class="card-title">Blockchain Solutions Architect</h5>
                    <p class="text-muted mb-2">CryptoInnovation Ltd. • New York, NY</p>
                    <div class="mb-2">
                      <span class="badge bg-primary me-1">Blockchain</span>
                      <span class="badge bg-primary me-1">Ethereum</span>
                      <span class="badge bg-primary me-1">Solidity</span>
                      <span class="badge bg-primary me-1">Web3</span>
                      <span class="badge bg-success me-1">92% Match</span>
                    </div>
                    <p class="card-text">Architect and implement blockchain solutions for enterprise clients. Deep understanding of DeFi protocols and smart contracts required.</p>
                  </div>
                  <div class="col-12 col-md-4 text-md-end">
                    <p class="text-success fw-bold mb-2">$180,000 - $250,000</p>
                    <p class="text-muted small mb-2">Posted 4 days ago</p>
                    <button class="btn btn-primary btn-sm">Apply Now</button>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Pagination -->
          <nav aria-label="Job listings pagination">
            <ul class="pagination justify-content-center">
              <li class="page-item disabled">
                <a class="page-link" href="#" tabindex="-1">Previous</a>
              </li>
              <li class="page-item active"><a class="page-link" href="#">1</a></li>
              <li class="page-item"><a class="page-link" href="#">2</a></li>
              <li class="page-item"><a class="page-link" href="#">3</a></li>
              <li class="page-item">
                <a class="page-link" href="#">Next</a>
              </li>
            </ul>
          </nav>
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
  
  <script src="js/jobs.js"></script>
</body>
</html>
