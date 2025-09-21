<?php
session_start();

// Include authentication check
require_once 'check_auth.php';

// Require authentication to view this page
requireAuth();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Data Display - JobFilter</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="mb-0">User Session Data</h3>
                    </div>
                    <div class="card-body">
                        <h5>Session Information:</h5>
                        <table class="table table-striped">
                            <tr>
                                <td><strong>User ID:</strong></td>
                                <td><?php echo htmlspecialchars($_SESSION['user_id'] ?? 'Not set'); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Email:</strong></td>
                                <td><?php echo htmlspecialchars($_SESSION['email'] ?? 'Not set'); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Name:</strong></td>
                                <td><?php echo htmlspecialchars($_SESSION['name'] ?? 'Not set'); ?></td>
                            </tr>
                            <tr>
                                <td><strong>First Name:</strong></td>
                                <td><?php echo htmlspecialchars($_SESSION['firstname'] ?? 'Not set'); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Last Name:</strong></td>
                                <td><?php echo htmlspecialchars($_SESSION['lastname'] ?? 'Not set'); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Role:</strong></td>
                                <td><?php echo htmlspecialchars($_SESSION['role'] ?? 'Not set'); ?></td>
                            </tr>
                        </table>

                        <h5 class="mt-4">Cookie Information:</h5>
                        <table class="table table-striped">
                            <tr>
                                <td><strong>User Email:</strong></td>
                                <td><?php echo htmlspecialchars($_COOKIE['user_email'] ?? 'Not set'); ?></td>
                            </tr>
                            <tr>
                                <td><strong>User Role:</strong></td>
                                <td><?php echo htmlspecialchars($_COOKIE['user_role'] ?? 'Not set'); ?></td>
                            </tr>
                            <tr>
                                <td><strong>User Name:</strong></td>
                                <td><?php echo htmlspecialchars($_COOKIE['user_name'] ?? 'Not set'); ?></td>
                            </tr>
                            <tr>
                                <td><strong>First Name:</strong></td>
                                <td><?php echo htmlspecialchars($_COOKIE['firstname'] ?? 'Not set'); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Last Name:</strong></td>
                                <td><?php echo htmlspecialchars($_COOKIE['lastname'] ?? 'Not set'); ?></td>
                            </tr>
                        </table>

                        <h5 class="mt-4">All Session Data:</h5>
                        <pre class="bg-light p-3"><?php print_r($_SESSION); ?></pre>

                        <h5 class="mt-4">All Cookie Data:</h5>
                        <pre class="bg-light p-3"><?php print_r($_COOKIE); ?></pre>

                        <div class="mt-4">
                            <a href="dashboard.php" class="btn btn-primary">Go to Dashboard</a>
                            <a href="process_logout.php" class="btn btn-danger">Logout</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
