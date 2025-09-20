<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Resume - Frank Oliver Bentoy</title>
  <link rel="icon" type="image/svg+xml" href="Images/log.png" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  
</head>
<body>
  <nav class="navbar navbar-light bg-light shadow-sm">
    <div class="container">
      <a class="navbar-brand fw-bold" href="about.php">← Back to About</a>
    </div>
  </nav>

  <main class="py-5">
    <div class="container">
      <div class="row g-4">
        <div class="col-12 col-lg-4 text-center">
          <img src="Images/Frank Oliver Bentoy..jpg" alt="Frank Oliver Bentoy" class="rounded-circle mb-3" width="160" height="160">
          <h2 class="fw-bold mb-1">Frank Oliver Bentoy</h2>
          <p class="text-muted">Software Engineer</p>
          <div class="d-grid gap-2">
            <a class="btn btn-primary" href="#">Contact</a>
          </div>
        </div>
        <div class="col-12 col-lg-8">
          <h4 class="fw-bold">Summary</h4>
          <p>Software Engineer focusing on frontend and backend web development with modern frameworks.</p>

          <h5 class="fw-bold">EDUCATIONAL  BACKGROUND</h5>
          <ul>
            <li>Elementary: Tongan-Tongan Elementary School </li>
            <li>HighSchool: Labogon National HighSchool</li>
            <li>College: University of Cebu Banilad</li>
          </ul>

          <h4 class="fw-bold mt-4">Experience</h4>
          <ul>
            <li>Built responsive web apps with Bootstrap and JavaScript.</li>
            <li>Skilled and detailed-oriented software developer with a strong background in ASP.NET, SQL SERVER, MySQL</li>
          <li>Creating a Enrollment System using ASP.NET web application</li>
            <li>Built 2D game using java</li>
          </ul>

          <h4 class="fw-bold mt-4">Skills</h4>
          <span class="badge bg-secondary me-1 mb-1">JavaScript</span>
          <span class="badge bg-secondary me-1 mb-1">C++</span>
          <span class="badge bg-secondary me-1 mb-1">Bootstrap</span>
          <span class="badge bg-secondary me-1 mb-1">java</span>
          <span class="badge bg-secondary me-1 mb-1"> c#</span>
          <span class="badge bg-secondary me-1 mb-1"> C</span>

          <h4 id="contact" class="fw-bold mt-4">Contact</h4>
          <p class="mb-0">Email: frankoliverbentoy23@gmail.com</p>
          <p class="mb-0">mobile number: 09560772456</p>
          <p>Linkedin: www.linkedin.com/in/frank-oliver-bentoy-33b116238</p>
        </div>
      </div>
    </div>
  </main>

  <footer class="footer py-4">
    <div class="container d-flex justify-content-between align-items-center">
      <p class="mb-0 text-muted">&copy; 2025 JobFilter</p>
      <a href="about.php" class="text-decoration-none">Back to About</a>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
