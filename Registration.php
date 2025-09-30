<?php
session_start();

// If user is already logged in, redirect to dashboard
if (isset($_SESSION['isLoggedIn']) && $_SESSION['isLoggedIn'] === true) {
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Registration Form</title>
    <link rel="icon" type="image/svg+xml" href="Images/log.png" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
          rel="stylesheet" />

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css"
          rel="stylesheet" />

    <!-- Your custom CSS -->
    <link href="css/registration.css" rel="stylesheet" />
</head>
<body>
    <div class="bg-overlay"></div>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card shadow">
                    <div class="card-body p-4">
                        <h3 class="card-title text-center mb-4">Sign Up to JobFilter</h3>

                        <!-- Role Selection as Cards -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-center gap-4">
                                <div id="employeeCard" class="role-card selected" onclick="selectRole('employee')">
                                    <i class="bi bi-person"></i>
                                    <div>Employee</div>
                                </div>
                                <div id="employerCard" class="role-card" onclick="selectRole('employer')">
                                    <i class="bi bi-building"></i>
                                    <div>Employer</div>
                                </div>
                            </div>
                        </div>

                        <!-- Success/Error Messages -->
                        <?php if (isset($_SESSION['registration_success'])): ?>
                            <div class="alert alert-success py-2 mb-3 small" role="alert">
                                <?php echo htmlspecialchars($_SESSION['registration_success']); ?>
                                <script>
                                    setTimeout(function() {
                                        window.location.href = 'dashboard.php';
                                    }, 2000);
                                </script>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (isset($_SESSION['registration_error'])): ?>
                            <div class="alert alert-danger py-2 mb-3 small" role="alert">
                                <?php echo htmlspecialchars($_SESSION['registration_error']); ?>
                            </div>
                        <?php endif; ?>

                        <!-- Registration Form -->
                                               <!-- Registration Form -->
                                               <form id="registrationForm" novalidate enctype="multipart/form-data" method="post" action="process_registration.php">
                            <input type="hidden" id="role" name="role" value="employee" />

                            <div class="mb-3">
                                <label for="fullname" class="form-label">Full Name</label>
                                <input type="text"
                                       class="form-control"
                                       id="fullname" name="fullname"
                                       placeholder="Enter your name"
                                       required />
                                <div class="invalid-feedback">
                                    Please enter your full name.
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email address</label>
                                <input type="email"
                                       class="form-control"
                                       id="email" name="email"
                                       placeholder="Enter email"
                                       required />
                                <div class="invalid-feedback">
                                    Please enter a valid email address.
                                </div>
                            </div>

                            <div class="row g-3 align-items-center">
                                <div class="col-auto">
                                    <label for="inputPassword6" class="col-form-label">Password</label>
                                </div>
                                <div class="col-auto">
                                    <input type="password" id="inputPassword6" name="password" class="form-control" aria-describedby="passwordHelpInline" required>
                                </div>
                                <div class="col-auto">
                                    <span id="passwordHelpInline" class="form-text"> Must be 6 characters long.
                                    </span>
                                </div>
                            </div>

                            <!-- Employee Fields -->
                            <div id="employeeFields">
                                <div class="mb-3">
                                    <label for="jobTitle" class="form-label">Job Title</label>
                                    <input type="text"
                                           class="form-control"
                                           id="jobTitle" name="jobTitle"
                                           placeholder="e.g. Web Developer" />
                                </div>
                                <div class="mb-3">
                                    <label for="employeePhone" class="form-label">Phone Number</label>
                                    <input type="tel"
                                           class="form-control"
                                           id="employeePhone" name="employeePhone"
                                           placeholder="Enter your phone number"
                                           pattern="^\+?[0-9\s\-]{7,15}$"
                                           required />
                                    <div class="invalid-feedback">
                                        Please enter a valid phone number.
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="dob" class="form-label">Date of Birth</label>
                                    <input type="date"
                                           class="form-control"
                                           id="dob" name="dob"
                                           required />
                                    <div class="invalid-feedback">
                                        Please enter your date of birth.
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="bio" class="form-label">Short Bio</label>
                                    <textarea class="form-control"
                                              id="bio" name="bio"
                                              rows="3"
                                              placeholder="Tell us a little about yourself"></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="resume" class="form-label">Upload Resume (PDF only)</label>
                                    <input type="file"
                                           class="form-control"
                                           id="resume" name="resume"
                                           accept=".pdf,application/pdf"
                                           required />
                                    <div class="invalid-feedback">
                                        Please upload your resume as a PDF file.
                                    </div>
                                </div>
                            </div>

                            <!-- Employer Fields -->
                            <div id="employerFields" style="display: none">
                                <div class="mb-3">
                                    <label for="companyName" class="form-label">Company Name</label>
                                    <input type="text"
                                           class="form-control"
                                           id="companyName" name="companyName"
                                           placeholder="Enter your company name"
                                           required />
                                    <div class="invalid-feedback">
                                        Please enter the company name.
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="companyRegNumber" class="form-label">Company Registration Number</label>
                                    <input type="text"
                                           class="form-control"
                                           id="companyRegNumber" name="companyRegNumber"
                                           placeholder="Enter company registration or tax ID"
                                           required />
                                    <div class="invalid-feedback">
                                        Please enter a valid registration number.
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="companyAddress" class="form-label">Company Address</label>
                                    <input type="text"
                                           class="form-control"
                                           id="companyAddress" name="companyAddress"
                                           placeholder="Enter company address"
                                           required />
                                    <div class="invalid-feedback">
                                        Please enter the company address.
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="companyPhone" class="form-label">Company Phone Number</label>
                                    <input type="tel"
                                           class="form-control"
                                           id="companyPhone" name="companyPhone"
                                           placeholder="Enter company phone number"
                                           pattern="^\+?[0-9\s\-]{7,15}$"
                                           required />
                                    <div class="invalid-feedback">
                                        Please enter a valid phone number.
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="companyLinkedIn" class="form-label">LinkedIn Profile (Optional)</label>
                                    <input type="url"
                                           class="form-control"
                                           id="companyLinkedIn" name="companyLinkedIn"
                                           placeholder="https://linkedin.com/company/your-company" />
                                    <div class="invalid-feedback">
                                        Please enter a valid URL.
                                    </div>
                                </div>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Register</button>
                            </div>
                        </form>

                        <div class="text-center mt-3">
                            <small>
                                Already have an account?
                                <a href="login.php">Login</a>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Common JavaScript -->
    <script src="js/common.js"></script>

    <!-- Your custom JS with defer to run after DOM parsed -->
    <script src="js/registration.js" defer></script>
</body>
</html>
