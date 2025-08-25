(function(){
  'use strict';
  // Auth controls (no hard guard, just toggle buttons)
  var isLoggedIn = localStorage.getItem('isLoggedIn') === 'true';
  var loginNav = document.getElementById('loginNav');
  var signupNav = document.getElementById('signupNav');
  var logoutNav = document.getElementById('logoutNav');
  var logoutBtn = document.getElementById('logoutBtn');

  if (isLoggedIn) {
    if (loginNav) loginNav.classList.add('d-none');
    if (signupNav) signupNav.classList.add('d-none');
    if (logoutNav) logoutNav.classList.remove('d-none');
  } else {
    if (loginNav) loginNav.classList.remove('d-none');
    if (signupNav) signupNav.classList.remove('d-none');
    if (logoutNav) logoutNav.classList.add('d-none');
  }

  if (logoutBtn) {
    logoutBtn.addEventListener('click', function(){
      if (confirm('Do you want to log out?')) {
        localStorage.removeItem('isLoggedIn');
        localStorage.removeItem('loggedInUser');
        window.location.href = 'index.html';
      }
    });
  }

  // Add smooth scrolling for any anchor links
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
})();
