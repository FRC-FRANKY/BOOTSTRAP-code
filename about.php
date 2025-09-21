<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>About Us - JobFilter</title>
  <link rel="icon" type="image/svg+xml" href="Images/log.png" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="css/about.css" rel="stylesheet">
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
  
  <!-- Hero Section -->
  <section class="py-5 text-center bg-light">
    <div class="container">
      <h1 class="fw-bold mb-3">About JobFilter</h1>
      <p class="text-muted mb-0">Revolutionizing job matching through intelligent skill-based algorithms</p>
    </div>
  </section>

  <!-- Mission Section -->
  <section class="py-5">
    <div class="container">
      <div class="row align-items-center g-4">
        <div class="col-12 col-lg-6">
          <h2 class="fw-bold mb-3">Our Mission</h2>
          <p class="text-muted mb-4">At JobFilter, we believe that finding the right job or the perfect candidate shouldn't be a time-consuming process. Our platform leverages advanced skill-matching algorithms to connect talented professionals with opportunities that truly match their expertise and career goals.</p>
          <p class="text-muted mb-0">We're committed to reducing recruitment time, improving job matching efficiency, and helping both job seekers and employers achieve their goals faster and more effectively.</p>
        </div>
        <div class="col-12 col-lg-6">
          <div class="card shadow-sm">
            <div class="card-body text-center p-4">
              <div class="display-4 text-primary mb-3">🎯</div>
              <h5 class="fw-bold">Smart Matching</h5>
              <p class="text-muted mb-0">Our AI-powered system analyzes skills, experience, and preferences to deliver the most relevant matches.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Values Section -->
  <section class="py-5 bg-light">
    <div class="container">
      <h2 class="text-center fw-bold mb-5">Our Values</h2>
      <div class="row g-4">
        <div class="col-12 col-md-4">
          <div class="card shadow-sm h-100">
            <div class="card-body text-center p-4">
              <div class="display-6 text-primary mb-3">⚡</div>
              <h5 class="fw-bold">Efficiency</h5>
              <p class="text-muted mb-0">Streamlining the recruitment process to save time for both employers and job seekers.</p>
            </div>
          </div>
        </div>
        <div class="col-12 col-md-4">
          <div class="card shadow-sm h-100">
            <div class="card-body text-center p-4">
              <div class="display-6 text-primary mb-3">🎯</div>
              <h5 class="fw-bold">Accuracy</h5>
              <p class="text-muted mb-0">Providing precise skill-based matches to ensure the best possible connections.</p>
            </div>
          </div>
        </div>
        <div class="col-12 col-md-4">
          <div class="card shadow-sm h-100">
            <div class="card-body text-center p-4">
              <div class="display-6 text-primary mb-3">🤝</div>
              <h5 class="fw-bold">Transparency</h5>
              <p class="text-muted mb-0">Clear and honest communication throughout the entire matching process.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Team Section -->
  <section class="py-5">
    <div class="container">
      <h2 class="text-center fw-bold mb-5">Our Team</h2>
      <div class="row g-4">
        <div class="col-12 col-md-6 col-lg-3">
          <div class="card shadow-sm text-center">
            <div class="card-body p-4">
              <a href="resume-christian.php" data-bs-toggle="modal" data-bs-target="#resumeChristian">
                <img src="Images/Christian Crisostomo.jpg" alt="Project Manager" class="rounded-circle mb-3" width="120" height="120">
              </a>
              <h5 class="fw-bold mb-1">Christian Crisostomo</h5>
              <p class="text-muted mb-0">Project Manager</p>
            </div>
          </div>
        </div>
        <div class="col-12 col-md-6 col-lg-3">
          <div class="card shadow-sm text-center">
            <div class="card-body p-4">
              <a href="resume-alyssa.php" data-bs-toggle="modal" data-bs-target="#resumeAlyssa">
                <img src="Images/Alyssa jane prak.jpg" alt="System Analyst/Database Designer" class="rounded-circle mb-3" width="120" height="120">
              </a>
              <h5 class="fw-bold mb-1">Alyssa Jane Prak</h5>
              <p class="text-muted mb-0">System Analyst/Database Designer</p>
            </div>
          </div>
        </div>
        <div class="col-12 col-md-6 col-lg-3">
          <div class="card shadow-sm text-center">
            <div class="card-body p-4">
              <a href="resume-frank.php" data-bs-toggle="modal" data-bs-target="#resumeFrank">
                <img src="Images/Frank Oliver Bentoy..jpg" alt="Software Engineer" class="rounded-circle mb-3" width="120" height="120">
              </a>
              <h5 class="fw-bold mb-1">Frank Oliver Bentoy</h5>
              <p class="text-muted mb-0">Software Engineer</p>
            </div>
          </div>
        </div>
        <div class="col-12 col-md-6 col-lg-3">
          <div class="card shadow-sm text-center">
            <div class="card-body p-4">
              <a href="resume-shine.php" data-bs-toggle="modal" data-bs-target="#resumeShine">
                <img src="Images/Shine Florence Padillo.jpg" alt="Software Tester/Technical Writer" class="rounded-circle mb-3" width="120" height="120">
              </a>
              <h5 class="fw-bold mb-1">Shine Florence Padillo</h5>
              <p class="text-muted mb-0">Software Tester/Technical Writer</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Team Resumes Modals -->
  <div class="modal fade" id="resumeChristian" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Christian Crisostomo - Resume</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p class="mb-3">Project Manager with experience in agile delivery, stakeholder management, and cross-functional leadership.</p>
          <ul>
            <li><strong>Skills:</strong> Agile, Scrum, Roadmapping, Risk Management, Jira</li>
            <li><strong>Experience:</strong> 5+ years leading software projects</li>
            <li><strong>Education:</strong> B.S. in Information Technology</li>
          </ul>
          <p class="mb-0"><a href="resume-christian.php" target="_blank">Open full resume page</a></p>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="resumeAlyssa" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Alyssa Jane Prak - Resume</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p class="mb-3">System Analyst and Database Designer specializing in requirements analysis and relational data modeling.</p>
          <ul>
            <li><strong>Skills:</strong> SQL, ERD, Normalization, UML, Documentation</li>
            <li><strong>Experience:</strong> 3+ years in system analysis and DB design</li>
            <li><strong>Education:</strong> B.S. in Computer Science</li>
          </ul>
          <p class="mb-0"><a href="resume-alyssa.php" target="_blank">Open full resume page</a></p>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="resumeFrank" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Frank Oliver Bentoy - Resume</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p class="mb-3">Software Engineer focusing on frontend and backend web development with modern frameworks.</p>
          <ul>
            <li><strong>Skills:</strong> JavaScript, C#, Java, C, C++, nodejs</li>
            <li><strong>Experience:</strong> Built responsive web apps and dashboards</li>
            <li><strong>Education:</strong> B.S. in Information Systems</li>
          </ul>
          <p class="mb-0"><a href="resume-frank.php" target="_blank">Open full resume page</a></p>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="resumeShine" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Shine Florence Padillo - Resume</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p class="mb-3">Software Tester and Technical Writer with experience in QA processes and documentation.</p>
          <ul>
            <li><strong>Skills:</strong> Test Planning, Manual Testing, Bug Reporting, Technical Writing</li>
            <li><strong>Experience:</strong> Contributed to multiple product releases</li>
            <li><strong>Education:</strong> B.S. in Information Technology</li>
          </ul>
          <p class="mb-0"><a href="resume-shine.php" target="_blank">Open full resume page</a></p>
        </div>
      </div>
    </div>
  </div>

  <!-- Stats Section -->
  <section class="py-5 bg-light">
    <div class="container">
      <div class="row g-4 text-center">
        <div class="col-12 col-md-3">
          <div class="card shadow-sm">
            <div class="card-body p-4">
              <div class="display-4 fw-bold text-primary mb-2">10K+</div>
              <p class="text-muted mb-0">Active Users</p>
            </div>
          </div>
        </div>
        <div class="col-12 col-md-3">
          <div class="card shadow-sm">
            <div class="card-body p-4">
              <div class="display-4 fw-bold text-primary mb-2">5K+</div>
              <p class="text-muted mb-0">Jobs Posted</p>
            </div>
          </div>
        </div>
        <div class="col-12 col-md-3">
          <div class="card shadow-sm">
            <div class="card-body p-4">
              <div class="display-4 fw-bold text-primary mb-2">95%</div>
              <p class="text-muted mb-0">Match Accuracy</p>
            </div>
          </div>
        </div>
        <div class="col-12 col-md-3">
          <div class="card shadow-sm">
            <div class="card-body p-4">
              <div class="display-4 fw-bold text-primary mb-2">24/7</div>
              <p class="text-muted mb-0">Support Available</p>
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
  
  <!-- Authentication Service -->
  <script src="js/auth.js"></script>
  
  <script src="js/about.js"></script>
</body>
</html>

