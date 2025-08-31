// About Page JavaScript with Authentication Guard
(function () {
  'use strict';

  // Check authentication status immediately
  function checkAuth() {
    const isLoggedIn = localStorage.getItem('isLoggedIn') === 'true';
    if (!isLoggedIn) {
      // Store current page for redirect after login
      localStorage.setItem('redirectUrl', window.location.href);
      window.location.href = 'login.html';
      return;
    }
  }

  // Update navbar based on authentication status
  function updateNavbar() {
    const isLoggedIn = localStorage.getItem('isLoggedIn') === 'true';
    const loginNav = document.getElementById('loginNav');
    const signupNav = document.getElementById('signupNav');
    const logoutNav = document.getElementById('logoutNav');

    if (isLoggedIn) {
      // User is logged in - hide login/register buttons, show logout
      if (loginNav) loginNav.style.display = 'none';
      if (signupNav) signupNav.style.display = 'none';
      if (logoutNav) logoutNav.classList.remove('d-none');
    } else {
      // User is not logged in - show login/register buttons, hide logout
      if (loginNav) loginNav.style.display = '';
      if (signupNav) signupNav.style.display = '';
      if (logoutNav) logoutNav.classList.add('d-none');
    }
  }

  // Setup logout functionality
  function setupLogout() {
    const logoutBtn = document.getElementById('logoutBtn');
    if (logoutBtn) {
      logoutBtn.addEventListener('click', function() {
        if (confirm('Are you sure you want to log out?')) {
          localStorage.removeItem('isLoggedIn');
          localStorage.removeItem('loggedInUser');
          window.location.href = 'index.html';
        }
      });
    }
  }

  // Initialize page
  function init() {
    // Check authentication first
    checkAuth();
    
    // Update navbar
    updateNavbar();
    
    // Setup logout
    setupLogout();
  }

  // Run initialization when DOM is loaded
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

  // Update navbar when authentication state changes
  window.addEventListener('storage', function(e) {
    if (e.key === 'isLoggedIn') {
      updateNavbar();
    }
  });

})();
