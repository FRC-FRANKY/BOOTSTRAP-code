<!-- View Application Modal -->
<div class="modal fade" id="viewApplicationModal" tabindex="-1" aria-labelledby="viewApplicationModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="viewApplicationModalLabel">Application Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-4 text-center">
            <img src="https://ui-avatars.com/api/?name=Applicant+Name" alt="Profile" class="img-fluid rounded-circle mb-3" style="width: 120px; height: 120px; object-fit: cover;">
            <h5 class="mb-1" id="applicantName">John Doe</h5>
            <p class="text-muted mb-3">Senior Web Developer</p>
            <div class="d-grid gap-2">
              <button class="btn btn-primary"><i class="bi bi-envelope me-1"></i> Message</button>
              <button class="btn btn-outline-primary"><i class="bi bi-telephone me-1"></i> Call</button>
            </div>
          </div>
          <div class="col-md-8">
            <h6 class="mb-3">Contact Information</h6>
            <div class="row mb-4">
              <div class="col-6 mb-2">
                <p class="mb-1 text-muted small">Email</p>
                <p class="mb-0">john.doe@example.com</p>
              </div>
              <div class="col-6 mb-2">
                <p class="mb-1 text-muted small">Phone</p>
                <p class="mb-0">(123) 456-7890</p>
              </div>
              <div class="col-6 mb-2">
                <p class="mb-1 text-muted small">Location</p>
                <p class="mb-0">New York, NY</p>
              </div>
              <div class="col-6 mb-2">
                <p class="mb-1 text-muted small">Application Date</p>
                <p class="mb-0">May 20, 2023</p>
              </div>
            </div>

            <h6 class="mb-3">Skills</h6>
            <div class="mb-4">
              <span class="badge bg-light text-dark me-1 mb-1">HTML5</span>
              <span class="badge bg-light text-dark me-1 mb-1">CSS3</span>
              <span class="badge bg-light text-dark me-1 mb-1">JavaScript</span>
              <span class="badge bg-light text-dark me-1 mb-1">React</span>
              <span class="badge bg-light text-dark me-1 mb-1">Node.js</span>
              <span class="badge bg-light text-dark me-1 mb-1">MongoDB</span>
            </div>

            <h6 class="mb-3">Experience</h6>
            <div class="mb-3">
              <h6 class="mb-0">Senior Web Developer</h6>
              <p class="mb-1">Tech Solutions Inc. • 2019 - Present</p>
              <p class="small text-muted">Lead a team of developers in building responsive web applications using modern JavaScript frameworks.</p>
            </div>

            <h6 class="mb-3">Cover Letter</h6>
            <div class="bg-light p-3 rounded mb-3">
              <p class="mb-0">Dear Hiring Manager,</p>
              <p class="mb-0">I am excited to apply for the Web Developer position at your company. With 5+ years of experience in web development, I am confident in my ability to contribute to your team.</p>
              <p class="mb-0">Looking forward to discussing how I can contribute to your team.</p>
              <p class="mb-0">Best regards,<br>John Doe</p>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <div class="d-flex justify-content-between w-100">
          <div>
            <select class="form-select form-select-sm me-2" style="width: auto; display: inline-block;">
              <option>Update status...</option>
              <option>New</option>
              <option>In Review</option>
              <option>Interview</option>
              <option>Hired</option>
              <option>Rejected</option>
            </select>
            <button class="btn btn-sm btn-outline-secondary">Save</button>
          </div>
          <div>
            <button type="button" class="btn btn-outline-secondary me-2" data-bs-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary">Schedule Interview</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- New Job Modal -->
<div class="modal fade" id="newJobModal" tabindex="-1" aria-labelledby="newJobModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="newJobModalLabel">Post a New Job</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="newJobForm">
          <div class="mb-3">
            <label for="jobTitle" class="form-label">Job Title <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="jobTitle" required>
          </div>
          <div class="mb-3">
            <label for="jobType" class="form-label">Job Type <span class="text-danger">*</span></label>
            <select class="form-select" id="jobType" required>
              <option value="">Select job type</option>
              <option value="full-time">Full-time</option>
              <option value="part-time">Part-time</option>
              <option value="contract">Contract</option>
              <option value="internship">Internship</option>
              <option value="temporary">Temporary</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="location" class="form-label">Location <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="location" required>
          </div>
          <div class="mb-3">
            <label for="salary" class="form-label">Salary Range</label>
            <input type="text" class="form-control" id="salary" placeholder="e.g., $50,000 - $70,000 per year">
          </div>
          <div class="mb-3">
            <label for="jobDescription" class="form-label">Job Description <span class="text-danger">*</span></label>
            <textarea class="form-control" id="jobDescription" rows="5" required></textarea>
          </div>
          <div class="mb-3">
            <label for="requirements" class="form-label">Requirements</label>
            <textarea class="form-control" id="requirements" rows="3"></textarea>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" form="newJobForm" class="btn btn-primary">Post Job</button>
      </div>
    </div>
  </div>
</div>
