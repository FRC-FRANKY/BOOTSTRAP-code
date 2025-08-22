// Post Job Page JavaScript

document.addEventListener('DOMContentLoaded', function() {
    initializePostJobPage();
});

function initializePostJobPage() {
    
    // Form validation
    const form = document.getElementById('jobPostForm');
    if (form) {
        form.addEventListener('submit', handleJobPost);
    }

    // Save draft button
    const saveDraftBtn = document.getElementById('saveDraft');
    if (saveDraftBtn) {
        saveDraftBtn.addEventListener('click', saveJobDraft);
    }

    // Real-time preview
    setupRealTimePreview();

    // Form validation on input
    setupFormValidation();

    // Auto-save functionality
    setupAutoSave();

    // Skills input enhancement
    setupSkillsInput();
}

function handleJobPost(event) {
    event.preventDefault();
    
    // Check if user is logged in
    const isLoggedIn = localStorage.getItem('isLoggedIn') === 'true';
    if (!isLoggedIn) {
        showNotification('Please log in to post a job.', 'warning');
        window.location.href = 'login.html';
        return;
    }

    // Validate form
    if (!validateForm()) {
        showNotification('Please fill in all required fields.', 'error');
        return;
    }

    // Show loading state
    const submitBtn = event.target.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    submitBtn.textContent = 'Posting Job...';
    submitBtn.disabled = true;

    // Collect form data
    const jobData = collectFormData();

    // Simulate API call
    setTimeout(() => {
        
        // Success
        submitBtn.textContent = 'Job Posted Successfully!';
        submitBtn.classList.remove('btn-primary');
        submitBtn.classList.add('btn-success');
        
        showNotification('Job posted successfully! Our AI will start matching candidates.', 'success');
        
        // Save to localStorage for demo
        saveJobToStorage(jobData);
        
        // Reset form after delay
        setTimeout(() => {
            form.reset();
            submitBtn.textContent = originalText;
            submitBtn.classList.remove('btn-success');
            submitBtn.classList.add('btn-primary');
            submitBtn.disabled = false;
            
            // Redirect to dashboard
            window.location.href = 'dashboard.html';
        }, 2000);
        
    }, 2000);
}

function validateForm() {
    const requiredFields = [
        'jobTitle', 'companyName', 'location', 'jobCategory',
        'jobType', 'experienceLevel', 'salaryMin', 'salaryMax',
        'requiredSkills', 'jobDescription', 'contactEmail'
    ];

    let isValid = true;

    requiredFields.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (!field || !field.value.trim()) {
            isValid = false;
            field.classList.add('is-invalid');
        } else {
            field.classList.remove('is-invalid');
            field.classList.add('is-valid');
        }
    });

    // Validate salary range
    const salaryMin = document.getElementById('salaryMin');
    const salaryMax = document.getElementById('salaryMax');
    
    if (salaryMin && salaryMax) {
        const min = parseInt(salaryMin.value);
        const max = parseInt(salaryMax.value);
        
        if (min >= max) {
            isValid = false;
            salaryMax.classList.add('is-invalid');
            showFieldError(salaryMax, 'Maximum salary must be greater than minimum salary');
        } else {
            salaryMax.classList.remove('is-invalid');
            salaryMax.classList.add('is-valid');
        }
    }

    // Validate email
    const email = document.getElementById('contactEmail');
    if (email && !isValidEmail(email.value)) {
        isValid = false;
        email.classList.add('is-invalid');
        showFieldError(email, 'Please enter a valid email address');
    }

    return isValid;
}

function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

function showFieldError(field, message) {
    const feedback = field.parentNode.querySelector('.invalid-feedback');
    if (feedback) {
        feedback.textContent = message;
    }
}

function collectFormData() {
    const formData = {
        jobTitle: document.getElementById('jobTitle').value,
        companyName: document.getElementById('companyName').value,
        location: document.getElementById('location').value,
        jobCategory: document.getElementById('jobCategory').value,
        jobType: document.getElementById('jobType').value,
        experienceLevel: document.getElementById('experienceLevel').value,
        salaryMin: document.getElementById('salaryMin').value,
        salaryMax: document.getElementById('salaryMax').value,
        requiredSkills: document.getElementById('requiredSkills').value,
        preferredSkills: document.getElementById('preferredSkills').value,
        jobDescription: document.getElementById('jobDescription').value,
        companyDescription: document.getElementById('companyDescription').value,
        companyWebsite: document.getElementById('companyWebsite').value,
        contactEmail: document.getElementById('contactEmail').value,
        benefits: getSelectedBenefits(),
        postedDate: new Date().toISOString(),
        status: 'active'
    };

    return formData;
}

function getSelectedBenefits() {
    const benefits = [];
    const benefitCheckboxes = [
        'healthInsurance', 'dentalInsurance', 'visionInsurance', 'retirementPlan',
        'paidTimeOff', 'flexibleSchedule', 'remoteWork', 'professionalDevelopment'
    ];

    benefitCheckboxes.forEach(benefit => {
        const checkbox = document.getElementById(benefit);
        if (checkbox && checkbox.checked) {
            benefits.push(benefit);
        }
    });

    return benefits;
}

function saveJobDraft() {
    const formData = collectFormData();
    formData.status = 'draft';
    
    // Save to localStorage
    const drafts = JSON.parse(localStorage.getItem('jobDrafts') || '[]');
    drafts.push(formData);
    localStorage.setItem('jobDrafts', JSON.stringify(drafts));
    
    showNotification('Job draft saved successfully!', 'success');
}

function setupRealTimePreview() {
    const previewFields = [
        'jobTitle', 'companyName', 'location', 'jobCategory',
        'jobType', 'experienceLevel', 'salaryMin', 'salaryMax',
        'requiredSkills', 'jobDescription', 'companyDescription'
    ];

    previewFields.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field) {
            field.addEventListener('input', updatePreview);
        }
    });
}

function updatePreview() {
    const previewSection = document.getElementById('jobPreview');
    const previewContent = document.getElementById('previewContent');
    
    if (!previewSection || !previewContent) return;

    const jobTitle = document.getElementById('jobTitle').value;
    const companyName = document.getElementById('companyName').value;
    const location = document.getElementById('location').value;
    const salaryMin = document.getElementById('salaryMin').value;
    const salaryMax = document.getElementById('salaryMax').value;
    const requiredSkills = document.getElementById('requiredSkills').value;
    const jobDescription = document.getElementById('jobDescription').value;

    if (jobTitle || companyName || location) {
        previewSection.style.display = 'block';
        
        const previewHTML = `
            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 col-md-8">
                            <h5 class="card-title">${jobTitle || 'Job Title'}</h5>
                            <p class="text-muted mb-2">${companyName || 'Company Name'} • ${location || 'Location'}</p>
                            ${requiredSkills ? `<div class="mb-2">${requiredSkills.split(',').map(skill => `<span class="badge bg-primary me-1">${skill.trim()}</span>`).join('')}</div>` : ''}
                            <p class="card-text">${jobDescription || 'Job description will appear here...'}</p>
                        </div>
                        <div class="col-12 col-md-4 text-md-end">
                            ${salaryMin && salaryMax ? `<p class="text-success fw-bold mb-2">$${parseInt(salaryMin).toLocaleString()} - $${parseInt(salaryMax).toLocaleString()}</p>` : ''}
                            <p class="text-muted small mb-2">Posted just now</p>
                            <button class="btn btn-primary btn-sm">Apply Now</button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        previewContent.innerHTML = previewHTML;
    } else {
        previewSection.style.display = 'none';
    }
}

function setupFormValidation() {
    const form = document.getElementById('jobPostForm');
    if (!form) return;

    // Real-time validation
    const inputs = form.querySelectorAll('input, select, textarea');
    inputs.forEach(input => {
        input.addEventListener('blur', validateField);
        input.addEventListener('input', clearFieldError);
    });
}

function validateField(event) {
    const field = event.target;
    const value = field.value.trim();
    
    // Remove previous validation classes
    field.classList.remove('is-valid', 'is-invalid');
    
    // Check if required
    if (field.hasAttribute('required') && !value) {
        field.classList.add('is-invalid');
        return;
    }
    
    // Email validation
    if (field.type === 'email' && value && !isValidEmail(value)) {
        field.classList.add('is-invalid');
        return;
    }
    
    // URL validation
    if (field.type === 'url' && value && !isValidUrl(value)) {
        field.classList.add('is-invalid');
        return;
    }
    
    // If we get here, field is valid
    if (value) {
        field.classList.add('is-valid');
    }
}

function isValidUrl(string) {
    try {
        new URL(string);
        return true;
    } catch (_) {
        return false;
    }
}

function clearFieldError(event) {
    const field = event.target;
    field.classList.remove('is-invalid');
}

function setupAutoSave() {
    let autoSaveTimer;
    const form = document.getElementById('jobPostForm');
    
    if (!form) return;

    const inputs = form.querySelectorAll('input, select, textarea');
    inputs.forEach(input => {
        input.addEventListener('input', () => {
            clearTimeout(autoSaveTimer);
            autoSaveTimer = setTimeout(() => {
                saveJobDraft();
            }, 30000); // Auto-save after 30 seconds of inactivity
        });
    });
}

function setupSkillsInput() {
    const skillsInputs = ['requiredSkills', 'preferredSkills'];
    
    skillsInputs.forEach(inputId => {
        const input = document.getElementById(inputId);
        if (!input) return;

        input.addEventListener('input', function() {
            // Auto-format skills (remove extra spaces, commas)
            let value = this.value;
            value = value.replace(/\s*,\s*/g, ', '); // Normalize comma spacing
            value = value.replace(/,\s*,/g, ','); // Remove double commas
            value = value.replace(/^,\s*/, ''); // Remove leading comma
            value = value.replace(/,\s*$/, ''); // Remove trailing comma
            
            this.value = value;
        });
    });
}

function saveJobToStorage(jobData) {
    const jobs = JSON.parse(localStorage.getItem('postedJobs') || '[]');
    jobData.id = Date.now(); // Simple ID generation
    jobs.push(jobData);
    localStorage.setItem('postedJobs', JSON.stringify(jobs));
}

function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show position-fixed`;
    notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    // Add to page
    document.body.appendChild(notification);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 5000);
}

// Export functions for use in other scripts
window.JobPosting = {
    validateForm,
    collectFormData,
    saveJobDraft,
    showNotification
};
