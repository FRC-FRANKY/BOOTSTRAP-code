<?php
// Only start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/db_connect.php';

// Check if user is authenticated
if (!isset($_SESSION['isLoggedIn']) || $_SESSION['isLoggedIn'] !== true) {
    header("Location: login.php");
    exit();
}

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $jobTitle = trim($_POST['jobTitle'] ?? '');
    $companyName = trim($_POST['companyName'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $jobDescription = trim($_POST['jobDescription'] ?? '');
    $salaryMin = $_POST['salaryMin'] ?? '';
    $salaryMax = $_POST['salaryMax'] ?? '';
    $requiredSkills = trim($_POST['requiredSkills'] ?? '');
    $preferredSkills = trim($_POST['preferredSkills'] ?? '');

    // Basic validation (use only fields that map to DB now)
    if ($jobTitle === '' || $companyName === '' || $location === '' || $jobDescription === '') {
        $_SESSION['job_error'] = 'Please fill in all required fields';
        header("Location: post-job.php");
        exit();
    }

    // Choose a salary value to store (simple approach): use max if set, else min; else NULL
    $salaryNumeric = null;
    if ($salaryMax !== '') {
        $salaryNumeric = (float)$salaryMax;
    } elseif ($salaryMin !== '') {
        $salaryNumeric = (float)$salaryMin;
    }

    $postedBy = $_SESSION['user_id'] ?? null;
    if (!$postedBy) {
        $_SESSION['job_error'] = 'Unable to identify employer account.';
        header("Location: post-job.php");
        exit();
    }

    // Prevent duplicates for same employer posting same company + title (case-insensitive)
    $dupSql = 'SELECT id FROM jobs WHERE posted_by = ? AND LOWER(company_name) = LOWER(?) AND LOWER(title) = LOWER(?) LIMIT 1';
    $dupStmt = $conn->prepare($dupSql);
    if ($dupStmt) {
        $dupStmt->bind_param('iss', $postedBy, $companyName, $jobTitle);
        if ($dupStmt->execute()) {
            $dupStmt->store_result();
            if ($dupStmt->num_rows > 0) {
                $_SESSION['job_error'] = 'You already posted this job title for this company. Please choose a different title.';
                header('Location: post-job.php');
                exit();
            }
        }
        $dupStmt->close();
    }

    // Insert into jobs table (handle nullable salary)
    if ($salaryNumeric === null) {
        $sql = 'INSERT INTO jobs (title, description, company_name, location, salary, required_skills, preferred_skills, posted_by) VALUES (?,?,?,?,NULL,?,?,?)';
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            $_SESSION['job_error'] = 'Failed to prepare statement: ' . $conn->error;
            header("Location: post-job.php");
            exit();
        }
        $stmt->bind_param('ssssssi', $jobTitle, $jobDescription, $companyName, $location, $requiredSkills, $preferredSkills, $postedBy);
    } else {
        $sql = 'INSERT INTO jobs (title, description, company_name, location, salary, required_skills, preferred_skills, posted_by) VALUES (?,?,?,?,?,?,?,?)';
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            $_SESSION['job_error'] = 'Failed to prepare statement: ' . $conn->error;
            header("Location: post-job.php");
            exit();
        }
        $stmt->bind_param('ssssdssi', $jobTitle, $jobDescription, $companyName, $location, $salaryNumeric, $requiredSkills, $preferredSkills, $postedBy);
    }

    if (!$stmt->execute()) {
        // Handle duplicate key from DB unique constraint
        if (strpos($stmt->error, 'unique_postedby_company_title') !== false || $stmt->errno === 1062) {
            $_SESSION['job_error'] = 'A job with this title already exists for this company under your account.';
        } else {
            $_SESSION['job_error'] = 'Failed to post job: ' . $stmt->error;
        }
        header("Location: post-job.php");
        exit();
    }

    unset($_SESSION['job_error']);
    $_SESSION['job_success'] = 'Job posted successfully!';

    header("Location: dashboard.php");
    exit();
} else {
    // If not POST request, redirect to post job page
    header("Location: post-job.php");
    exit();
}
?>
