<?php
// Prefill user details
require_once __DIR__ . '/../db_connect.php';
$uid = $_SESSION['user_id'] ?? null;
$prefill = ['name' => '', 'email' => '', 'phone' => '', 'location' => '', 'resume_path' => ''];
if ($uid) {
  // Pull basic session fallbacks
  $prefill['name'] = $_SESSION['name'] ?? '';
  $prefill['email'] = $_SESSION['email'] ?? '';
  // Query users table for phone and saved resume
  if ($stmt = $conn->prepare('SELECT phone, resume_path FROM users WHERE id = ? LIMIT 1')) {
    $stmt->bind_param('i', $uid);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    if ($row) {
      $prefill['phone'] = $row['phone'] ?? '';
      $prefill['resume_path'] = $row['resume_path'] ?? '';
    }
    $stmt->close();
  }
}
?>
<div class="modal fade" id="applicationModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Apply for <span id="appJobTitle"></span></h5>
      </div>
      <form id="jobApplicationForm" enctype="multipart/form-data">
        <div class="modal-body">
          <input type="hidden" name="job_id" id="appJobId">
          <input type="hidden" name="use_saved_resume" id="useSavedResumeInput" value="0">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Full Name</label>
              <input type="text" class="form-control" name="full_name" value="<?php echo htmlspecialchars($prefill['name']); ?>">
            </div>
            <div class="col-md-6">
              <label class="form-label">Email</label>
              <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($prefill['email']); ?>">
            </div>
            <div class="col-md-6">
              <label class="form-label">Phone</label>
              <input type="text" class="form-control" name="phone" value="<?php echo htmlspecialchars($prefill['phone']); ?>">
            </div>
            <div class="col-md-6">
              <label class="form-label">Location</label>
              <input type="text" class="form-control" name="location" value="<?php echo htmlspecialchars($prefill['location']); ?>">
            </div>
            <div class="col-12">
              <div class="form-check mb-1">
                <input class="form-check-input" type="checkbox" id="useSavedResumeChk" <?php echo $prefill['resume_path'] ? 'checked' : ''; ?> <?php echo $prefill['resume_path'] ? '' : 'disabled'; ?>>
                <label class="form-check-label" for="useSavedResumeChk">
                  Use my saved resume<?php if (!$prefill['resume_path']) echo ' (no saved resume found)'; ?>
                </label>
              </div>
              <label class="form-label">Resume (PDF, DOC, DOCX, max 5MB)</label>
              <input type="file" class="form-control" name="resume" id="resume" accept=".pdf,.doc,.docx,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" <?php echo $prefill['resume_path'] ? '' : 'required'; ?> <?php echo $prefill['resume_path'] ? 'disabled' : ''; ?>>
              <?php if ($prefill['resume_path']): ?>
                <div class="form-text">Saved: <?php echo htmlspecialchars(basename($prefill['resume_path'])); ?></div>
              <?php endif; ?>
              <div class="mt-2 d-none" id="appExistingResume">
                <small class="text-muted">Current resume for this application: <a href="#" id="appExistingResumeLink" target="_blank" rel="noopener">download</a></small>
              </div>
              <div class="mt-2 d-none" id="appExistingResumePreview">
                <div class="border rounded" style="height: 360px; overflow: hidden;">
                  <iframe id="appExistingResumeFrame" src="" title="Resume Preview" style="width:100%; height:100%; border:0;" loading="lazy"></iframe>
                </div>
                <small class="text-muted">Preview is shown for PDF files only.</small>
              </div>
            </div>
            <div class="col-12">
              <label class="form-label">Cover Letter</label>
              <textarea class="form-control" name="cover_letter" rows="5" required></textarea>
            </div>
          </div>
          <div class="alert mt-3 d-none" id="appAlert" role="alert"></div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary" id="submitAppBtn">
            <span class="spinner-border spinner-border-sm me-2 d-none" id="submitSpinner"></span>
            Submit Application
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
