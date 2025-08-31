// Testimonials Page JavaScript
(function () {
  'use strict';

  // Check authentication status and update navbar
  function updateNavbar() {
    const isLoggedIn = localStorage.getItem('isLoggedIn') === 'true';
    const loginBtn = document.getElementById('loginBtn');
    const registerBtn = document.getElementById('registerBtn');
    const logoutBtn = document.getElementById('logoutBtn');

    if (isLoggedIn) {
      // User is logged in
      if (loginBtn) loginBtn.parentElement.classList.add('d-none');
      if (registerBtn) registerBtn.parentElement.classList.add('d-none');
      if (logoutBtn) logoutBtn.parentElement.classList.remove('d-none');
    } else {
      // User is not logged in
      if (loginBtn) loginBtn.parentElement.classList.remove('d-none');
      if (registerBtn) registerBtn.parentElement.classList.remove('d-none');
      if (logoutBtn) logoutBtn.parentElement.classList.add('d-none');
    }
  }

  // Logout function
  window.logout = function() {
    if (confirm('Are you sure you want to log out?')) {
      localStorage.removeItem('isLoggedIn');
      localStorage.removeItem('loggedInUser');
      window.location.href = 'index.html';
    }
  };

  // Add fade-in animation to testimonial cards
  function addFadeInAnimation() {
    const cards = document.querySelectorAll('.testimonial-card');
    const observer = new IntersectionObserver((entries) => {
      entries.forEach((entry, index) => {
        if (entry.isIntersecting) {
          setTimeout(() => {
            entry.target.classList.add('fade-in');
          }, index * 100); // Stagger the animation
        }
      });
    }, {
      threshold: 0.1
    });

    cards.forEach(card => observer.observe(card));
  }

  // Add hover effects to stats
  function addStatsHoverEffects() {
    const statsItems = document.querySelectorAll('.stats-section .col-md-3');
    statsItems.forEach(item => {
      item.addEventListener('mouseenter', function() {
        this.style.transform = 'translateY(-5px)';
      });
      
      item.addEventListener('mouseleave', function() {
        this.style.transform = 'translateY(0)';
      });
    });
  }

  // Initialize page
  function init() {
    updateNavbar();
    addFadeInAnimation();
    addStatsHoverEffects();
    
    // Add smooth scrolling for navigation links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
      anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
          target.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
          });
        }
      });
    });
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
