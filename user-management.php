<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/db_connect.php';

// Fetch users from DB
$users = [];
$result = $conn->query("SELECT id, name, email, role, status, DATE_FORMAT(created_at, '%Y-%m-%d') AS joined FROM users ORDER BY id DESC");
if ($result) {
  while ($row = $result->fetch_assoc()) { $users[] = $row; }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>User Management - JobFilter</title>
  <link rel="icon" type="image/svg+xml" href="Images/log.png" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="css/dashboard.css" rel="stylesheet">
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
          <button type="button" class="btn btn-outline-secondary btn-sm" data-action="logout">Logout</button>
          <hr class="mt-3">
        </div>
        <ul class="navbar-nav ms-auto mb-2 mb-lg-0" id="navMenuList">
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

  <!-- Header -->
  <section class="py-4 bg-primary text-white">
    <div class="container">
      <div class="d-flex justify-content-between align-items-center flex-wrap">
        <div>
          <h1 class="fw-bold mb-1">User Management</h1>
          <p class="mb-0">View, search, and manage user accounts.</p>
        </div>
        <div class="d-flex gap-2">
          <a class="btn btn-light btn-sm" href="user-profile.php">My Profile</a>
        </div>
      </div>
    </div>
  </section>

  <!-- Content -->
  <section class="py-5">
    <div class="container">
      <div class="card mb-4">
        <div class="card-body">
          <div class="row g-3 align-items-end">
            <div class="col-12 col-md-4">
              <label for="searchQuery" class="form-label">Search</label>
              <input id="searchQuery" type="text" class="form-control" placeholder="Name, email, or role">
            </div>
            <div class="col-12 col-md-3">
              <label for="filterRole" class="form-label">Role</label>
              <select id="filterRole" class="form-select">
                <option value="">All</option>
                <option>Job Seeker</option>
                <option>Employer</option>
                <option>Admin</option>
              </select>
            </div>
            <div class="col-12 col-md-3">
              <label for="filterStatus" class="form-label">Status</label>
              <select id="filterStatus" class="form-select">
                <option value="">All</option>
                <option>Active</option>
                <option>Suspended</option>
                <option>Pending</option>
              </select>
            </div>
            <div class="col-12 col-md-2 d-grid">
              <button id="applyFilters" class="btn btn-primary">Apply</button>
            </div>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0">Users</h5>
          <div class="d-flex gap-2">
            <button id="exportBtn" class="btn btn-outline-secondary btn-sm">Export</button>
            <a href="users_create.php" class="btn btn-primary btn-sm">New User</a>
          </div>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover align-middle">
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Email</th>
                  <th>Role</th>
                  <th>Status</th>
                  <th>Joined</th>
                  <th class="text-end">Actions</th>
                </tr>
              </thead>
              <tbody id="usersTable">
                <?php if (count($users) > 0): ?>
                  <?php foreach ($users as $u): ?>
                  <tr>
                    <td><?php echo htmlspecialchars($u['name']); ?></td>
                    <td><?php echo htmlspecialchars($u['email']); ?></td>
                    <td>
                      <?php
                        $role = $u['role'];
                        if ($role === 'job_seeker') {
                          echo '<span class="badge bg-primary">Job Seeker</span>';
                        } elseif ($role === 'employer') {
                          echo '<span class="badge bg-warning text-dark">Employer</span>';
                        } elseif ($role === 'admin') {
                          echo '<span class="badge bg-dark">Admin</span>';
                        } else {
                          echo '<span class="badge bg-secondary">' . htmlspecialchars($role) . '</span>';
                        }
                      ?>
                    </td>
                    <td>
                      <?php
                        $status = $u['status'] ?: 'active';
                        $statusClass = ($status === 'active') ? 'success' : (($status === 'suspended') ? 'danger' : 'secondary');
                        echo '<span class="badge bg-' . $statusClass . '">' . htmlspecialchars(ucfirst($status)) . '</span>';
                      ?>
                    </td>
                    <td><?php echo htmlspecialchars($u['joined']); ?></td>
                    <td class="text-end">
                      <a href="user-record.php?id=<?php echo (int)$u['id']; ?>" class="btn btn-sm btn-outline-primary">View</a>
                      <a href="users_edit.php?id=<?php echo (int)$u['id']; ?>" class="btn btn-sm btn-outline-secondary">Edit</a>
                      <a href="users_delete.php?id=<?php echo (int)$u['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this user?');">Delete</a>
                    </td>
                  </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr><td colspan="6" class="text-center text-muted">No users found.</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
          <nav class="mt-3">
            <ul class="pagination mb-0">
              <li class="page-item disabled"><span class="page-link">Previous</span></li>
              <li class="page-item active"><span class="page-link">1</span></li>
              <li class="page-item"><a class="page-link" href="#">2</a></li>
              <li class="page-item"><a class="page-link" href="#">3</a></li>
              <li class="page-item"><a class="page-link" href="#">Next</a></li>
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

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="js/common.js"></script>
  <script src="js/role-control.js"></script>
  <script>
    document.getElementById('applyFilters').addEventListener('click', function () {
      alert('Filters applied (demo)');
    });
    document.getElementById('exportBtn').addEventListener('click', function () {
      alert('Exporting users (demo)');
    });
    document.getElementById('newUserBtn').addEventListener('click', function () {
      alert('Open create user modal (demo)');
    });
  </script>
</body>
</html>
