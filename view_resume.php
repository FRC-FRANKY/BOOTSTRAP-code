<?php
/**
 * Resume View/Download for Employers
 */

session_start();
require_once __DIR__ . '/check_auth.php';
require_once __DIR__ . '/db_connect.php';

// Check authentication
if (!isset($_SESSION['isLoggedIn']) || $_SESSION['isLoggedIn'] !== true) {
    http_response_code(401);
    die('Unauthorized');
}

// Only employers and admins can view resumes
$userRole = $_SESSION['role'] ?? '';
if (!in_array($userRole, ['employer', 'admin'])) {
    die('Access denied. Only employers can view resumes.');
}

$action = $_GET['action'] ?? 'list';
$userId = $_GET['user_id'] ?? null;

try {
    if ($action === 'download' && $userId) {
        // Download specific user's resume
        $query = "SELECT original_name, file_content, mime_type, file_size FROM user_resumes WHERE user_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $resume = $result->fetch_assoc();
        
        if (!$resume) {
            die('Resume not found');
        }
        
        // Set headers for download
        header('Content-Type: ' . $resume['mime_type']);
        header('Content-Disposition: attachment; filename="' . $resume['original_name'] . '"');
        header('Content-Length: ' . $resume['file_size']);
        
        // Output file content
        echo $resume['file_content'];
        exit;
        
    } elseif ($action === 'view' && $userId) {
        // View specific user's resume content
        $query = "SELECT u.name, u.email, ur.original_name, ur.text_content, ur.created_at 
                  FROM users u 
                  JOIN user_resumes ur ON u.id = ur.user_id 
                  WHERE u.id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $resume = $result->fetch_assoc();
        
        if (!$resume) {
            die('Resume not found');
        }
        
        echo "<h2>Resume: " . htmlspecialchars($resume['name']) . "</h2>";
        echo "<p><strong>Email:</strong> " . htmlspecialchars($resume['email']) . "</p>";
        echo "<p><strong>File:</strong> " . htmlspecialchars($resume['original_name']) . "</p>";
        echo "<p><strong>Uploaded:</strong> " . date('Y-m-d H:i', strtotime($resume['created_at'])) . "</p>";
        echo "<p><a href='?action=download&user_id=$userId'>Download Resume</a></p>";
        echo "<h3>Resume Content:</h3>";
        echo "<pre style='background: #f5f5f5; padding: 15px; border: 1px solid #ddd; max-height: 400px; overflow-y: auto;'>";
        echo htmlspecialchars($resume['text_content']);
        echo "</pre>";
        exit;
        
    } else {
        // List all candidates with resumes
        $query = "SELECT u.id, u.name, u.email, ur.original_name, ur.created_at 
                  FROM users u 
                  JOIN user_resumes ur ON u.id = ur.user_id 
                  WHERE u.role = 'job_seeker' 
                  ORDER BY ur.created_at DESC";
        $result = $conn->query($query);
        $candidates = $result->fetch_all(MYSQLI_ASSOC);
        
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <title>Candidate Resumes - JobFilter</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        </head>
        <body>
            <div class="container mt-4">
                <h1>Candidate Resumes</h1>
                
                <?php if (empty($candidates)): ?>
                    <div class="alert alert-info">No resumes available yet.</div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($candidates as $candidate): ?>
                            <div class="col-md-6 mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title"><?php echo htmlspecialchars($candidate['name']); ?></h5>
                                        <p class="card-text"><?php echo htmlspecialchars($candidate['email']); ?></p>
                                        <p class="text-muted small">
                                            Resume: <?php echo htmlspecialchars($candidate['original_name']); ?><br>
                                            Uploaded: <?php echo date('Y-m-d H:i', strtotime($candidate['created_at'])); ?>
                                        </p>
                                        <div class="btn-group">
                                            <a href="?action=view&user_id=<?php echo $candidate['id']; ?>" 
                                               class="btn btn-sm btn-outline-primary">View</a>
                                            <a href="?action=download&user_id=<?php echo $candidate['id']; ?>" 
                                               class="btn btn-sm btn-primary">Download</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                
                <div class="mt-4">
                    <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
                </div>
            </div>
        </body>
        </html>
        <?php
    }
    
} catch (Exception $e) {
    die('Error: ' . $e->getMessage());
}
?>