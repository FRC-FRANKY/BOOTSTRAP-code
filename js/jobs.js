// Jobs Page JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Initialize jobs page functionality
    initializeJobsPage();
});

function initializeJobsPage() {
    // Search form handling
    const searchForm = document.getElementById('jobSearchForm');
    if (searchForm) {
        searchForm.addEventListener('submit', handleJobSearch);
    }

    // Advanced filters toggle
    const toggleFiltersBtn = document.getElementById('toggleFilters');
    const advancedFilters = document.getElementById('advancedFilters');
    
    if (toggleFiltersBtn && advancedFilters) {
        toggleFiltersBtn.addEventListener('click', function() {
            const isVisible = advancedFilters.style.display !== 'none';
            advancedFilters.style.display = isVisible ? 'none' : 'block';
            toggleFiltersBtn.textContent = isVisible ? 'Advanced Filters' : 'Hide Filters';
        });
    }

    // Clear filters button
    const clearFiltersBtn = document.getElementById('clearFilters');
    if (clearFiltersBtn) {
        clearFiltersBtn.addEventListener('click', clearAllFilters);
    }

    // Sidebar filter checkboxes
    const filterCheckboxes = document.querySelectorAll('.form-check-input');
    filterCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', filterJobs);
    });

    // Sort dropdown
    const sortDropdown = document.getElementById('sortBy');
    if (sortDropdown) {
        sortDropdown.addEventListener('change', sortJobs);
    }

    // Apply buttons
    const applyButtons = document.querySelectorAll('.btn-primary.btn-sm');
    applyButtons.forEach(button => {
        button.addEventListener('click', handleJobApplication);
    });

    // Initialize job count
    updateJobCount();
}

function handleJobSearch(event) {
    event.preventDefault();
    
    const jobTitle = document.getElementById('jobTitle').value;
    const location = document.getElementById('location').value;
    const category = document.getElementById('category').value;
    const experience = document.getElementById('experience').value;
    const salary = document.getElementById('salary').value;
    const jobType = document.getElementById('jobType').value;

    // Show loading state
    showLoadingState();

    // Simulate API call delay
    setTimeout(() => {
        filterJobsBySearch({
            jobTitle,
            location,
            category,
            experience,
            salary,
            jobType
        });
        hideLoadingState();
    }, 1000);
}

function filterJobsBySearch(searchParams) {
    const jobCards = document.querySelectorAll('.job-card');
    
    jobCards.forEach(card => {
        let shouldShow = true;
        
        // Filter by job title/keywords
        if (searchParams.jobTitle) {
            const title = card.querySelector('.card-title').textContent.toLowerCase();
            const keywords = searchParams.jobTitle.toLowerCase();
            if (!title.includes(keywords)) {
                shouldShow = false;
            }
        }
        
        // Filter by experience level
        if (searchParams.experience) {
            const cardExperience = card.dataset.experience;
            if (cardExperience !== searchParams.experience) {
                shouldShow = false;
            }
        }
        
        // Filter by salary range
        if (searchParams.salary) {
            const cardSalary = card.dataset.salary;
            if (cardSalary !== searchParams.salary) {
                shouldShow = false;
            }
        }
        
        // Filter by job type
        if (searchParams.jobType) {
            const cardType = card.dataset.type;
            if (cardType !== searchParams.jobType) {
                shouldShow = false;
            }
        }
        
        // Show/hide card
        card.style.display = shouldShow ? 'block' : 'none';
    });
    
    updateJobCount();
}

function filterJobs() {
    const jobCards = document.querySelectorAll('.job-card');
    const skillMatch = document.getElementById('skillMatch').checked;
    const entryLevel = document.getElementById('entryLevel').checked;
    const midLevel = document.getElementById('midLevel').checked;
    const seniorLevel = document.getElementById('seniorLevel').checked;
    const fullTime = document.getElementById('fullTime').checked;
    const partTime = document.getElementById('partTime').checked;
    const remote = document.getElementById('remote').checked;
    
    jobCards.forEach(card => {
        let shouldShow = true;
        
        // Filter by skill match
        if (skillMatch) {
            const matchBadge = card.querySelector('.badge.bg-success, .badge.bg-warning, .badge.bg-info');
            if (matchBadge) {
                const matchText = matchBadge.textContent;
                const matchPercentage = parseInt(matchText.replace('% Match', ''));
                if (matchPercentage < 80) {
                    shouldShow = false;
                }
            }
        }
        
        // Filter by experience level
        if (entryLevel || midLevel || seniorLevel) {
            const cardExperience = card.dataset.experience;
            const experienceMatch = 
                (entryLevel && cardExperience === 'entry') ||
                (midLevel && cardExperience === 'mid') ||
                (seniorLevel && cardExperience === 'senior');
            
            if (!experienceMatch) {
                shouldShow = false;
            }
        }
        
        // Filter by job type
        if (fullTime || partTime || remote) {
            const cardType = card.dataset.type;
            const typeMatch = 
                (fullTime && cardType === 'full-time') ||
                (partTime && cardType === 'part-time') ||
                (remote && cardType === 'remote');
            
            if (!typeMatch) {
                shouldShow = false;
            }
        }
        
        // Show/hide card
        card.style.display = shouldShow ? 'block' : 'none';
    });
    
    updateJobCount();
}

function sortJobs() {
    const sortBy = document.getElementById('sortBy').value;
    const jobListings = document.getElementById('jobListings');
    const jobCards = Array.from(jobListings.querySelectorAll('.job-card'));
    
    jobCards.sort((a, b) => {
        switch (sortBy) {
            case 'date':
                // Sort by posted date (simulated)
                const dateA = a.querySelector('.text-muted.small').textContent;
                const dateB = b.querySelector('.text-muted.small').textContent;
                return getDateValue(dateA) - getDateValue(dateB);
                
            case 'salary':
                // Sort by salary range
                const salaryA = getSalaryValue(a.querySelector('.text-success.fw-bold').textContent);
                const salaryB = getSalaryValue(b.querySelector('.text-success.fw-bold').textContent);
                return salaryB - salaryA; // Highest first
                
            case 'relevance':
            default:
                // Sort by match percentage
                const matchA = getMatchPercentage(a);
                const matchB = getMatchPercentage(b);
                return matchB - matchA; // Highest first
        }
    });
    
    // Reorder cards in DOM
    jobCards.forEach(card => jobListings.appendChild(card));
}

function getDateValue(dateText) {
    if (dateText.includes('today')) return 0;
    if (dateText.includes('yesterday')) return 1;
    if (dateText.includes('days ago')) {
        const days = parseInt(dateText.match(/(\d+)/)[1]);
        return days;
    }
    if (dateText.includes('week')) return 7;
    if (dateText.includes('weeks')) {
        const weeks = parseInt(dateText.match(/(\d+)/)[1]);
        return weeks * 7;
    }
    return 999; // Default for older posts
}

function getSalaryValue(salaryText) {
    const match = salaryText.match(/\$([0-9,]+)/);
    if (match) {
        return parseInt(match[1].replace(/,/g, ''));
    }
    return 0;
}

function getMatchPercentage(card) {
    const matchBadge = card.querySelector('.badge.bg-success, .badge.bg-warning, .badge.bg-info');
    if (matchBadge) {
        const matchText = matchBadge.textContent;
        const matchPercentage = parseInt(matchText.replace('% Match', ''));
        return matchPercentage || 0;
    }
    return 0;
}

function clearAllFilters() {
    // Clear search form
    document.getElementById('jobSearchForm').reset();
    
    // Clear sidebar filters
    const filterCheckboxes = document.querySelectorAll('.form-check-input');
    filterCheckboxes.forEach(checkbox => {
        checkbox.checked = false;
    });
    
    // Reset sort dropdown
    document.getElementById('sortBy').value = 'relevance';
    
    // Show all job cards
    const jobCards = document.querySelectorAll('.job-card');
    jobCards.forEach(card => {
        card.style.display = 'block';
    });
    
    updateJobCount();
}

function updateJobCount() {
    const visibleJobs = document.querySelectorAll('.job-card[style*="block"], .job-card:not([style*="none"])');
    const jobCount = document.getElementById('jobCount');
    if (jobCount) {
        jobCount.textContent = visibleJobs.length;
    }
}

function handleJobApplication(event) {
    event.preventDefault();
    
    const button = event.target;
    const jobCard = button.closest('.job-card');
    const jobTitle = jobCard.querySelector('.card-title').textContent;
    const company = jobCard.querySelector('.text-muted').textContent.split('•')[0].trim();
    
    // Check if user is logged in
    const isLoggedIn = localStorage.getItem('isLoggedIn') === 'true';
    
    if (!isLoggedIn) {
        alert('Please log in to apply for jobs.');
        window.location.href = 'login.html';
        return;
    }
    
    // Show loading state
    button.textContent = 'Applying...';
    button.disabled = true;
    
    // Simulate application process
    setTimeout(() => {
        button.textContent = 'Applied ✓';
        button.classList.remove('btn-primary');
        button.classList.add('btn-success');
        button.disabled = true;
        
        // Show success message
        showNotification(`Successfully applied for ${jobTitle} at ${company}!`, 'success');
        
        // Reset button after 3 seconds
        setTimeout(() => {
            button.textContent = 'Apply Now';
            button.classList.remove('btn-success');
            button.classList.add('btn-primary');
            button.disabled = false;
        }, 3000);
    }, 2000);
}

function showLoadingState() {
    const jobCards = document.querySelectorAll('.job-card');
    jobCards.forEach(card => {
        card.classList.add('loading');
    });
}

function hideLoadingState() {
    const jobCards = document.querySelectorAll('.job-card');
    jobCards.forEach(card => {
        card.classList.remove('loading');
    });
}

function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
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

// Skill matching algorithm (simplified)
function calculateSkillMatch(userSkills, jobSkills) {
    if (!userSkills || !jobSkills) return 0;
    
    const userSkillArray = userSkills.toLowerCase().split(',').map(s => s.trim());
    const jobSkillArray = jobSkills.toLowerCase().split(',').map(s => s.trim());
    
    let matches = 0;
    userSkillArray.forEach(skill => {
        if (jobSkillArray.includes(skill)) {
            matches++;
        }
    });
    
    return Math.round((matches / jobSkillArray.length) * 100);
}

// Export functions for use in other scripts
window.JobFilter = {
    calculateSkillMatch,
    showNotification,
    filterJobs,
    sortJobs
};
