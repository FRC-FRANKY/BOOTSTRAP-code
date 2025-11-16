<?php /* Employer View Application Modal */ ?>
<div class="modal fade" id="employerAppModal" tabindex="-1" aria-labelledby="employerAppModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="employerAppModalLabel">Application Form</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-2"><strong>Job:</strong> <span id="empAppJobTitle"></span></div>
        <div class="row g-3 mb-2">
          <div class="col-md-4">
            <div class="text-muted small">Applicant</div>
            <div id="empApplicantName"></div>
          </div>
          <div class="col-md-4">
            <div class="text-muted small">Email</div>
            <div id="empApplicantEmail"></div>
          </div>
          <div class="col-md-4">
            <div class="text-muted small">Phone</div>
            <div id="empApplicantPhone"></div>
          </div>
        </div>
        <div class="row g-3 mb-3">
          <div class="col-md-6">
            <div class="text-muted small">Applied</div>
            <div id="empAppDate"></div>
          </div>
          <div class="col-md-6">
            <div class="text-muted small">Status</div>
            <div id="empAppStatus"></div>
          </div>
        </div>
        <div class="mb-3">
          <div class="text-muted small mb-1">Cover Letter</div>
          <div id="empAppCover" class="border rounded p-2" style="white-space: pre-wrap;"></div>
        </div>
        <div class="mb-2">
          <a id="empAppResumeLink" href="#" class="btn btn-sm btn-outline-primary" target="_blank" rel="noopener">
            <i class="bi bi-download me-1"></i>Download Resume
          </a>
        </div>
        <div class="mt-2 d-none" id="empAppResumePreview">
          <div class="border rounded" style="height: 360px; overflow: hidden;">
            <iframe id="empAppResumeFrame" src="" title="Resume Preview" style="width: 100%; height: 100%; border: 0;" loading="lazy"></iframe>
          </div>
          <small class="text-muted">Preview is shown for PDF files only.</small>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
