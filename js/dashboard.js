// Dashboard JavaScript

document.addEventListener('DOMContentLoaded', function() {
    initializeDashboard();
});

function initializeDashboard() {
    // Check login status
    checkLoginStatus();
    
    // Setup role switching
    setupRoleSwitching();

    // Auto-select tab based on logged-in user's role
    try {
        const user = JSON.parse(localStorage.getItem('loggedInUser') || 'null');
        const role = user && user.role ? user.role : 'job_seeker';
        const jobSeekerRadio = document.getElementById('jobSeeker');
        const employerRadio = document.getElementById('employer');
        if (role === 'employer' || role === 'admin') {
            if (employerRadio) employerRadio.checked = true;
            showEmployerDashboard();
        } else {
            if (jobSeekerRadio) jobSeekerRadio.checked = true;
            showJobSeekerDashboard();
        }
    } catch (e) { /* no-op */ }
    
    // Setup refresh button
    setupRefreshButton();
    
    // Load initial data
    loadDashboardData();
    
    // Setup interactive elements
    setupInteractiveElements();
}

function checkLoginStatus() {
    const isLoggedIn = localStorage.getItem('isLoggedIn') === 'true';
    if (!isLoggedIn) {
        window.location.href = 'login.html';
        return;
    }
    

}

function setupRoleSwitching() {
    const jobSeekerRadio = document.getElementById('jobSeeker');
    const employerRadio = document.getElementById('employer');
    const jobSeekerDashboard = document.getElementById('jobSeekerDashboard');
    const employerDashboard = document.getElementById('employerDashboard');
    
    const requireEmployerRoleOrRelogin = () => {
        try {
            const user = JSON.parse(localStorage.getItem('loggedInUser') || 'null');
            const role = user && user.role ? user.role : 'job_seeker';
            if (role !== 'employer' && role !== 'admin') {
                alert('Access requires Employer account. Please sign in as employer.');
                localStorage.setItem('redirectUrl', window.location.href);
                // Force relogin
                localStorage.removeItem('isLoggedIn');
                localStorage.removeItem('loggedInUser');
                window.location.href = 'login.html';
                return false;
            }
        } catch (e) { /* no-op */ }
        return true;
    };

    const requireJobSeekerRoleOrRelogin = () => {
        try {
            const user = JSON.parse(localStorage.getItem('loggedInUser') || 'null');
            const role = user && user.role ? user.role : 'job_seeker';
            if (role !== 'job_seeker' && role !== 'admin') {
                alert('Access requires Job Seeker account. Please sign in as job seeker.');
                localStorage.setItem('redirectUrl', window.location.href);
                // Force relogin
                localStorage.removeItem('isLoggedIn');
                localStorage.removeItem('loggedInUser');
                window.location.href = 'login.html';
                return false;
            }
        } catch (e) { /* no-op */ }
        return true;
    };
    
    if (jobSeekerRadio && employerRadio) {
        jobSeekerRadio.addEventListener('change', function() {
            if (this.checked) {
                if (!requireJobSeekerRoleOrRelogin()) return;
                showJobSeekerDashboard();
            }
        });
        
        employerRadio.addEventListener('change', function() {
            if (this.checked) {
                if (!requireEmployerRoleOrRelogin()) return;
                showEmployerDashboard();
            }
        });
    }
    
    // Switch role button
    const switchRoleBtn = document.getElementById('switchRoleBtn');
    if (switchRoleBtn) {
        switchRoleBtn.addEventListener('click', function() {
            if (jobSeekerDashboard.style.display !== 'none') {
                employerRadio.checked = true;
                if (!requireEmployerRoleOrRelogin()) return;
                showEmployerDashboard();
            } else {
                jobSeekerRadio.checked = true;
                if (!requireJobSeekerRoleOrRelogin()) return;
                showJobSeekerDashboard();
            }
        });
    }
}

function showJobSeekerDashboard() {
    const jobSeekerDashboard = document.getElementById('jobSeekerDashboard');
    const employerDashboard = document.getElementById('employerDashboard');
    
    if (jobSeekerDashboard && employerDashboard) {
        employerDashboard.style.display = 'none';
        jobSeekerDashboard.style.display = 'block';
        
        // Update button text
        const switchRoleBtn = document.getElementById('switchRoleBtn');
        if (switchRoleBtn) {
            switchRoleBtn.textContent = 'Switch to Employer';
        }
        
        // Load job seeker data
        loadJobSeekerData();
    }
}

function showEmployerDashboard() {
    const jobSeekerDashboard = document.getElementById('jobSeekerDashboard');
    const employerDashboard = document.getElementById('employerDashboard');
    
    if (jobSeekerDashboard && employerDashboard) {
        jobSeekerDashboard.style.display = 'none';
        employerDashboard.style.display = 'block';
        
        // Update button text
        const switchRoleBtn = document.getElementById('switchRoleBtn');
        if (switchRoleBtn) {
            switchRoleBtn.textContent = 'Switch to Job Seeker';
        }
        
        // Load employer data
        loadEmployerData();
    }
}

function setupRefreshButton() {
    const refreshBtn = document.getElementById('refreshBtn');
    if (refreshBtn) {
        refreshBtn.addEventListener('click', function() {
            this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Refreshing...';
            this.disabled = true;
            
            setTimeout(() => {
                loadDashboardData();
                this.innerHTML = 'Refresh';
                this.disabled = false;
                showNotification('Dashboard refreshed successfully!', 'success');
            }, 1500);
        });
    }
}

function loadDashboardData() {
    // Load data based on current role
    const jobSeekerRadio = document.getElementById('jobSeeker');
    if (jobSeekerRadio && jobSeekerRadio.checked) {
        loadJobSeekerData();
    } else {
        loadEmployerData();
    }
}

function loadJobSeekerData() {
    // Load applications from localStorage
    const applications = JSON.parse(localStorage.getItem('jobApplications') || '[]');
    
    // Update stats
    updateJobSeekerStats(applications);
    
    // Update applications table
    updateApplicationsTable(applications);
    
    // Load recommended jobs
    loadRecommendedJobs();
}

function loadEmployerData() {
    // Load posted jobs from localStorage
    const postedJobs = JSON.parse(localStorage.getItem('postedJobs') || '[]');
    
    // Update stats
    updateEmployerStats(postedJobs);
    
    // Update jobs table
    updateJobsTable(postedJobs);
}

function updateJobSeekerStats(applications) {
    const applicationsCount = document.getElementById('applicationsCount');
    const matchesCount = document.getElementById('matchesCount');
    const avgMatchScore = document.getElementById('avgMatchScore');
    const responsesCount = document.getElementById('responsesCount');
    
    if (applicationsCount) applicationsCount.textContent = applications.length;
    if (matchesCount) matchesCount.textContent = applications.filter(app => app.matchScore > 80).length;
    
    if (avgMatchScore && applications.length > 0) {
        const avgScore = Math.round(applications.reduce((sum, app) => sum + (app.matchScore || 0), 0) / applications.length);
        avgMatchScore.textContent = avgScore + '%';
    }
    
    if (responsesCount) {
        const responses = applications.filter(app => app.status === 'Interview Scheduled' || app.status === 'Under Review').length;
        responsesCount.textContent = responses;
    }
}

function updateEmployerStats(postedJobs) {
    const activeJobsCount = document.getElementById('activeJobsCount');
    const totalApplicants = document.getElementById('totalApplicants');
    const avgApplicantScore = document.getElementById('avgApplicantScore');
    const viewsCount = document.getElementById('viewsCount');
    
    if (activeJobsCount) activeJobsCount.textContent = postedJobs.filter(job => job.status === 'active').length;
    
    if (totalApplicants) {
        const totalApps = postedJobs.reduce((sum, job) => sum + (job.applicants || 0), 0);
        totalApplicants.textContent = totalApps;
    }
    
    if (avgApplicantScore) {
        const avgScore = Math.round(postedJobs.reduce((sum, job) => sum + (job.avgScore || 75), 0) / Math.max(postedJobs.length, 1));
        avgApplicantScore.textContent = avgScore + '%';
    }
    
    if (viewsCount) {
        const totalViews = postedJobs.reduce((sum, job) => sum + (job.views || 0), 0);
        viewsCount.textContent = totalViews;
    }
}

function updateApplicationsTable(applications) {
    const tableBody = document.getElementById('applicationsTable');
    if (!tableBody) return;
    
    if (applications.length === 0) {
        tableBody.innerHTML = `
            <tr>
                <td colspan="6" class="text-center text-muted py-4">
                    No applications yet. <a href="jobs.html">Start applying for jobs!</a>
                </td>
            </tr>
        `;
        return;
    }
    
    const tableRows = applications.map(app => `
        <tr>
            <td>${app.jobTitle}</td>
            <td>${app.company}</td>
            <td>${formatDate(app.appliedDate)}</td>
            <td><span class="badge bg-${getStatusColor(app.status)}">${app.status}</span></td>
            <td><span class="badge bg-${getScoreColor(app.matchScore)}">${app.matchScore}%</span></td>
            <td><button class="btn btn-sm btn-outline-primary" onclick="viewApplication('${app.id}')">View</button></td>
        </tr>
    `).join('');
    
    tableBody.innerHTML = tableRows;
}

function updateJobsTable(postedJobs) {
    const tableBody = document.getElementById('jobsTable');
    if (!tableBody) return;
    
    if (postedJobs.length === 0) {
        tableBody.innerHTML = `
            <tr>
                <td colspan="5" class="text-center text-muted py-4">
                    No jobs posted yet. <a href="post-job.html">Post your first job!</a>
                </td>
            </tr>
        `;
        return;
    }
    
    const tableRows = postedJobs.map(job => `
        <tr>
            <td>${job.jobTitle}</td>
            <td><span class="badge bg-${job.status === 'active' ? 'success' : 'secondary'}">${job.status}</span></td>
            <td>${job.applicants || 0}</td>
            <td>${formatDate(job.postedDate)}</td>
            <td>
                <button class="btn btn-sm btn-outline-primary me-1" onclick="viewJob('${job.id}')">View</button>
                <button class="btn btn-sm btn-outline-secondary" onclick="editJob('${job.id}')">Edit</button>
            </td>
        </tr>
    `).join('');
    
    tableBody.innerHTML = tableRows;
}

function loadRecommendedJobs() {
    // This would typically load from an API
    // For demo purposes, we'll use sample data
    const recommendedJobs = [
        {
            title: 'Senior Frontend Developer',
            company: 'TechCorp Inc.',
            location: 'New York, NY',
            salary: '$85,000 - $120,000',
            matchScore: 95,
            skills: ['JavaScript', 'React', 'Node.js']
        },
        {
            title: 'Python Developer',
            company: 'StartupXYZ',
            location: 'San Francisco, CA',
            salary: '$70,000 - $95,000',
            matchScore: 88,
            skills: ['Python', 'Django', 'SQL']
        }
    ];
    
    // Update recommended jobs section if it exists
    const recommendedJobsContainer = document.getElementById('recommendedJobs');
    if (recommendedJobsContainer) {
        const jobsHTML = recommendedJobs.map(job => `
            <div class="col-12 col-md-6 mb-3">
                <div class="card h-100">
                    <div class="card-body">
                        <h6 class="card-title">${job.title}</h6>
                        <p class="text-muted mb-2">${job.company} • ${job.location}</p>
                        <div class="mb-2">
                            ${job.skills.map(skill => `<span class="badge bg-primary me-1">${skill}</span>`).join('')}
                            <span class="badge bg-success ms-2">${job.matchScore}% Match</span>
                        </div>
                        <p class="text-success fw-bold mb-2">${job.salary}</p>
                        <button class="btn btn-primary btn-sm">Apply Now</button>
                    </div>
                </div>
            </div>
        `).join('');
        
        recommendedJobsContainer.innerHTML = jobsHTML;
    }
}

function setupInteractiveElements() {
    // Add skill button
    const addSkillBtn = document.querySelector('.badge.bg-secondary');
    if (addSkillBtn) {
        addSkillBtn.addEventListener('click', function() {
            const newSkill = prompt('Enter a new skill:');
            if (newSkill && newSkill.trim()) {
                addSkill(newSkill.trim());
            }
        });
    }
    
    // Update skills button
    const updateSkillsBtn = document.querySelector('button[onclick*="Update Skills"]');
    if (updateSkillsBtn) {
        updateSkillsBtn.addEventListener('click', function() {
            showNotification('Skills update feature coming soon!', 'info');
        });
    }
}

function addSkill(skill) {
    const skillsContainer = document.querySelector('.skills-container');
    if (skillsContainer) {
        const skillBadge = document.createElement('span');
        skillBadge.className = 'badge bg-primary me-2 mb-2';
        skillBadge.textContent = skill;
        skillBadge.style.transition = 'all 0.3s ease';
        
        // Insert before the "Add Skill" badge
        const addSkillBadge = skillsContainer.querySelector('.badge.bg-secondary');
        skillsContainer.insertBefore(skillBadge, addSkillBadge);
        
        // Animate the new skill
        skillBadge.style.transform = 'scale(0)';
        setTimeout(() => {
            skillBadge.style.transform = 'scale(1)';
        }, 100);
        
        showNotification(`Skill "${skill}" added successfully!`, 'success');
    }
}

function getStatusColor(status) {
    switch (status) {
        case 'Under Review': return 'warning';
        case 'Interview Scheduled': return 'info';
        case 'Hired': return 'success';
        case 'Rejected': return 'danger';
        default: return 'secondary';
    }
}

function getScoreColor(score) {
    if (score >= 90) return 'success';
    if (score >= 80) return 'warning';
    if (score >= 70) return 'info';
    return 'secondary';
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString();
}

function viewApplication(applicationId) {
    showNotification(`Viewing application ${applicationId}`, 'info');
    // This would typically open a modal or navigate to application details
}

function viewJob(jobId) {
    showNotification(`Viewing job ${jobId}`, 'info');
    // This would typically open a modal or navigate to job details
}

function editJob(jobId) {
    showNotification(`Editing job ${jobId}`, 'info');
    // This would typically navigate to job edit page
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
window.Dashboard = {
    showJobSeekerDashboard,
    showEmployerDashboard,
    loadDashboardData,
    showNotification
};
