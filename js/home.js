(function () {
  'use strict';
  
  // Determine if current page is the public landing page
  const path = (window.location.pathname || '').toLowerCase();
  const isLanding = path.endsWith('index.html') || path === '/' || path.endsWith('/');

  // Use authentication service to check auth status
  if (!isLanding) {
    // Require authentication for all non-landing pages
    requireAuth();
  }

  // Update navbar based on authentication status
  updateNavbar();

  // Landing page specific logic
  if (isLanding) {
    const navList = document.querySelector('#navMenu .navbar-nav');
    if (navList) {
      // Remove existing nav links and logout button
      Array.from(navList.children).forEach(function (li) {
        const link = li.querySelector('a.nav-link');
        const logoutBtn = li.querySelector('#logoutBtn');
        if (link && !link.href.includes('login') && !link.href.includes('Registration')) {
          li.remove();
        }
        if (logoutBtn) {
          logoutBtn.remove();
        }
      });

      // Add login/register buttons
      const loginNav = document.getElementById('loginNav');
      const signupNav = document.getElementById('signupNav');
      if (loginNav) loginNav.classList.remove('d-none');
      if (signupNav) signupNav.classList.remove('d-none');

      // Hide hero section CTAs when not logged in
      hideHeroCTAs();
    }

    // Add authentication guards to ALL protected buttons
    addAuthGuards();
    
    // Disable direct navigation to protected pages
    disableDirectNavigation();
  }

  // Function to require authentication
  function requireAuth() {
    const isLoggedIn = localStorage.getItem('isLoggedIn') === 'true';
    if (!isLoggedIn) {
      // Store current page for redirect after login
      localStorage.setItem('redirectUrl', window.location.href);
      window.location.href = 'login.html';
    }
  }

  // Function to update navbar
  function updateNavbar() {
    const isLoggedIn = localStorage.getItem('isLoggedIn') === 'true';
    const loginNav = document.getElementById('loginNav');
    const signupNav = document.getElementById('signupNav');

    if (isLoggedIn) {
      // User is logged in - hide login/register buttons
      if (loginNav) loginNav.classList.add('d-none');
      if (signupNav) signupNav.classList.add('d-none');
      
      // No logout button will be added
    } else {
      // User is not logged in - show login/register buttons
      if (loginNav) loginNav.classList.remove('d-none');
      if (signupNav) signupNav.classList.remove('d-none');
    }
  }

  // Function to hide hero section CTAs when not logged in
  function hideHeroCTAs() {
    const isLoggedIn = localStorage.getItem('isLoggedIn') === 'true';
    const ctaButtons = document.querySelectorAll('.cta-btn');
    
    if (!isLoggedIn) {
      ctaButtons.forEach(btn => {
        btn.style.display = 'none';
      });
    }
  }

  // Function to add authentication guards to ALL protected buttons
  function addAuthGuards() {
    // Find Jobs button
    const findJobsBtn = document.querySelector('a[href="jobs.html"]');
    if (findJobsBtn) {
      findJobsBtn.addEventListener('click', function(e) {
        e.preventDefault();
        checkAuthAndRedirect('jobs.html');
      });
    }

    // Post a Job button
    const postJobBtn = document.querySelector('a[href="post-job.html"]');
    if (postJobBtn) {
      postJobBtn.addEventListener('click', function(e) {
        e.preventDefault();
        checkAuthAndRedirect('post-job.html');
      });
    }

    // Get Started button
    const getStartedBtn = document.querySelector('a[href="dashboard.html"]');
    if (getStartedBtn) {
      getStartedBtn.addEventListener('click', function(e) {
        e.preventDefault();
        checkAuthAndRedirect('dashboard.html');
      });
    }

    // Start Matching button
    const startMatchingBtn = document.querySelector('a[href="dashboard.html"]');
    if (startMatchingBtn && startMatchingBtn.textContent.includes('Start Matching')) {
      startMatchingBtn.addEventListener('click', function(e) {
        e.preventDefault();
        checkAuthAndRedirect('dashboard.html');
      });
    }

    // Contact Us button
    const contactUsBtn = document.querySelector('a[href="contact.html"]');
    if (contactUsBtn) {
      contactUsBtn.addEventListener('click', function(e) {
        e.preventDefault();
        checkAuthAndRedirect('contact.html');
      });
    }

    // Read All Testimonials button
    const testimonialsBtn = document.querySelector('a[href="testimonials.html"]');
    if (testimonialsBtn) {
      testimonialsBtn.addEventListener('click', function(e) {
        e.preventDefault();
        checkAuthAndRedirect('testimonials.html');
      });
    }

    // About button (if it exists)
    const aboutBtn = document.querySelector('a[href="about.html"]');
    if (aboutBtn) {
      aboutBtn.addEventListener('click', function(e) {
        e.preventDefault();
        checkAuthAndRedirect('about.html');
      });
    }

    // Dashboard button (if it exists)
    const dashboardBtn = document.querySelector('a[href="dashboard.html"]');
    if (dashboardBtn && !dashboardBtn.textContent.includes('Get Started') && !dashboardBtn.textContent.includes('Start Matching')) {
      dashboardBtn.addEventListener('click', function(e) {
        e.preventDefault();
        checkAuthAndRedirect('dashboard.html');
      });
    }

    // Add authentication to any other protected links
    const allProtectedLinks = document.querySelectorAll('a[href*=".html"]');
    allProtectedLinks.forEach(link => {
      const href = link.getAttribute('href');
      // Skip login, registration, and index pages
      if (href && !href.includes('login') && !href.includes('Registration') && !href.includes('index') && !href.includes('#')) {
        link.addEventListener('click', function(e) {
          e.preventDefault();
          checkAuthAndRedirect(href);
        });
      }
    });
  }

  // Function to disable direct navigation to protected pages
  function disableDirectNavigation() {
    // Prevent users from typing protected URLs directly in the address bar
    const protectedPages = ['jobs.html', 'post-job.html', 'dashboard.html', 'contact.html', 'testimonials.html', 'about.html'];
    const currentPage = window.location.pathname.split('/').pop();
    
    if (protectedPages.includes(currentPage)) {
      const isLoggedIn = localStorage.getItem('isLoggedIn') === 'true';
      if (!isLoggedIn) {
        localStorage.setItem('redirectUrl', window.location.href);
        window.location.href = 'login.html';
      }
    }
  }

  // Function to check authentication and redirect
  function checkAuthAndRedirect(targetPage) {
    const isLoggedIn = localStorage.getItem('isLoggedIn') === 'true';
    
    if (isLoggedIn) {
      // User is logged in, allow access
      window.location.href = targetPage;
    } else {
      // User is not logged in, redirect to login
      localStorage.setItem('redirectUrl', targetPage);
      window.location.href = 'login.html';
    }
  }

  // Enhanced logout function with confirmation
  window.logout = function() {
    if (confirm('Are you sure you want to log out? You will lose access to all protected features.')) {
      localStorage.removeItem('isLoggedIn');
      localStorage.removeItem('loggedInUser');
      localStorage.removeItem('redirectUrl');
      window.location.href = 'index.html';
    }
  };

  // Add global authentication check
  window.checkAuth = function() {
    return localStorage.getItem('isLoggedIn') === 'true';
  };

  // Add global redirect function
  window.redirectToLogin = function(targetPage) {
    localStorage.setItem('redirectUrl', targetPage || window.location.href);
    window.location.href = 'login.html';
  };

  // Update navbar when authentication state changes
  window.addEventListener('storage', function(e) {
    if (e.key === 'isLoggedIn') {
      updateNavbar();
      if (isLanding) {
        hideHeroCTAs();
      }
    }
  });

  // Additional security: Check authentication on page load
  document.addEventListener('DOMContentLoaded', function() {
    if (isLanding) {
      // Re-check authentication status
      const isLoggedIn = localStorage.getItem('isLoggedIn') === 'true';
      if (!isLoggedIn) {
        // Ensure all protected buttons are properly secured
        addAuthGuards();
        hideHeroCTAs();
      }
    }
  });

})();
