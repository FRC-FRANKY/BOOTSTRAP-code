<?php
session_start();

// Check if user is authenticated
if (!isset($_SESSION['isLoggedIn']) || $_SESSION['isLoggedIn'] !== true) {
    header("Location: login.php");
    exit();
}

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $jobTitle = $_POST['jobTitle'] ?? '';
    $companyName = $_POST['companyName'] ?? '';
    $location = $_POST['location'] ?? '';
    $jobCategory = $_POST['jobCategory'] ?? '';
    $jobType = $_POST['jobType'] ?? '';
    $experienceLevel = $_POST['experienceLevel'] ?? '';
    $salaryMin = $_POST['salaryMin'] ?? '';
    $salaryMax = $_POST['salaryMax'] ?? '';
    $requiredSkills = $_POST['requiredSkills'] ?? '';
    $preferredSkills = $_POST['preferredSkills'] ?? '';
    $jobDescription = $_POST['jobDescription'] ?? '';
    $companyDescription = $_POST['companyDescription'] ?? '';
    $companyWebsite = $_POST['companyWebsite'] ?? '';
    $contactEmail = $_POST['contactEmail'] ?? '';
    
    // Benefits checkboxes
    $benefits = [];
    $benefitFields = ['healthInsurance', 'dentalInsurance', 'visionInsurance', 'retirementPlan', 
                     'paidTimeOff', 'flexibleSchedule', 'remoteWork', 'professionalDevelopment'];
    foreach ($benefitFields as $benefit) {
        if (isset($_POST[$benefit])) {
            $benefits[] = $benefit;
        }
    }
    
    // Basic validation
    $requiredFields = ['jobTitle', 'companyName', 'location', 'jobCategory', 'jobType', 
                      'experienceLevel', 'salaryMin', 'salaryMax', 'requiredSkills', 
                      'jobDescription', 'contactEmail'];
    
    foreach ($requiredFields as $field) {
        if (empty($_POST[$field])) {
            $_SESSION['job_error'] = 'Please fill in all required fields';
            header("Location: post-job.php");
            exit();
        }
    }
    
    if (!filter_var($contactEmail, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['job_error'] = 'Please enter a valid contact email address';
        header("Location: post-job.php");
        exit();
    }
    
    // Store job data in session for demo purposes
    $jobData = [
        'id' => uniqid(),
        'title' => $jobTitle,
        'company' => $companyName,
        'location' => $location,
        'category' => $jobCategory,
        'type' => $jobType,
        'experience_level' => $experienceLevel,
        'salary_min' => $salaryMin,
        'salary_max' => $salaryMax,
        'required_skills' => $requiredSkills,
        'preferred_skills' => $preferredSkills,
        'description' => $jobDescription,
        'company_description' => $companyDescription,
        'company_website' => $companyWebsite,
        'contact_email' => $contactEmail,
        'benefits' => $benefits,
        'posted_by' => $_SESSION['email'],
        'posted_date' => date('Y-m-d H:i:s'),
        'status' => 'active'
    ];
    
    // Initialize jobs array if not exists
    if (!isset($_SESSION['jobs'])) {
        $_SESSION['jobs'] = [];
    }
    
    // Add job to session
    $_SESSION['jobs'][] = $jobData;
    
    // Store in COOKIES for demo (valid 1 hour)
    setcookie("last_job_title", $jobTitle, time() + 3600, "/");
    setcookie("last_company", $companyName, time() + 3600, "/");
    
    // Clear error messages
    unset($_SESSION['job_error']);
    $_SESSION['job_success'] = 'Job posted successfully!';
    
    // Redirect to dashboard
    header("Location: dashboard.php");
    exit();
} else {
    // If not POST request, redirect to post job page
    header("Location: post-job.php");
    exit();
}
?>
