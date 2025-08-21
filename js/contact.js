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

  // Contact form handling
  var form = document.getElementById('contactForm');
  var success = document.getElementById('contactSuccess');
  if (form) {
    form.addEventListener('submit', function(e){
      e.preventDefault();
      if (!form.checkValidity()) {
        e.stopPropagation();
        form.classList.add('was-validated');
        return;
      }
      // Simulate send
      form.classList.add('was-validated');
      success.classList.remove('d-none');
      form.reset();
      setTimeout(function(){ success.classList.add('d-none'); }, 4000);
    });
  }
})();
