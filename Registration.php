<?php
session_start();
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
<body class="bg-light">
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

                        <!-- Registration Form -->
                        <form id="registrationForm" novalidate enctype="multipart/form-data">
                            <input type="hidden" id="role" value="employee" />

                            <div class="mb-3">
                                <label for="fullname" class="form-label">Full Name</label>
                                <input type="text"
                                       class="form-control"
                                       id="fullname"
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
                                       id="email"
                                       placeholder="Enter email"
                                       required />
                                <div class="invalid-feedback">
                                    Please enter a valid email address.
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password"
                                       class="form-control"
                                       id="password"
                                       placeholder="Enter password"
                                       required />
                                <div class="invalid-feedback">
                                    Please enter a password.
                                </div>
                            </div>

                            <!-- Employee Fields -->
                            <div id="employeeFields">
                                <div class="mb-3">
                                    <label for="jobTitle" class="form-label">Job Title</label>
                                    <input type="text"
                                           class="form-control"
                                           id="jobTitle"
                                           placeholder="e.g. Web Developer" />
                                </div>
                                <div class="mb-3">
                                    <label for="employeePhone" class="form-label">Phone Number</label>
                                    <input type="tel"
                                           class="form-control"
                                           id="employeePhone"
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
                                           id="dob"
                                           required />
                                    <div class="invalid-feedback">
                                        Please enter your date of birth.
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="bio" class="form-label">Short Bio</label>
                                    <textarea class="form-control"
                                              id="bio"
                                              rows="3"
                                              placeholder="Tell us a little about yourself"></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="resume" class="form-label">Upload Resume</label>
                                    <input type="file"
                                           class="form-control"
                                           id="resume"
                                           accept=".pdf,.doc,.docx"
                                           required />
                                    <div class="invalid-feedback">
                                        Please upload your resume (PDF, DOC, or DOCX).
                                    </div>
                                </div>
                            </div>

                            <!-- Employer Fields -->
                            <div id="employerFields" style="display: none">
                                <div class="mb-3">
                                    <label for="companyName" class="form-label">Company Name</label>
                                    <input type="text"
                                           class="form-control"
                                           id="companyName"
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
                                           id="companyRegNumber"
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
                                           id="companyAddress"
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
                                           id="companyPhone"
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
                                           id="companyLinkedIn"
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

    <!-- Your custom JS with defer to run after DOM parsed -->
    <script src="js/registration.js" defer></script>
</body>
</html>
