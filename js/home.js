(function () {
  'use strict';
  
  // Determine if current page is the public landing page
  const path = (window.location.pathname || '').toLowerCase();
  const isLanding = path.endsWith('index.html') || path === '/' || path.endsWith('/');

  // Use authentication service to check auth status
  if (!isLanding) {
    // Require authentication for all non-landing pages
    auth.requireAuth();
  }

  // Update navbar based on authentication status
  auth.updateNavbar();

  // Landing page specific logic
  if (isLanding) {
    const navList = document.querySelector('#navMenu .navbar-nav');
    if (navList) {
      // Remove existing nav links and logout button
      Array.from(navList.children).forEach(function (li) {
        const link = li.querySelector('a.nav-link');
        const logout = li.querySelector('#logoutBtn');
        if (logout) {
          li.remove();
          return;
        }
        if (link) {
          // Remove any standard nav link (Home/Jobs/Post/Dashboard/About)
          li.remove();
        }
      });

      // Inject Login/Register buttons if user not logged in
      if (!auth.isAuthenticated) {
        if (!navList.querySelector('a[href="login.html"]')) {
          const loginLi = document.createElement('li');
          loginLi.className = 'nav-item ms-lg-2';
          const loginA = document.createElement('a');
          loginA.href = 'login.html';
          loginA.className = 'btn btn-primary btn-sm';
          loginA.textContent = 'Login';
          loginLi.appendChild(loginA);
          navList.appendChild(loginLi);
        }
        if (!navList.querySelector('a[href="Registration.html"]')) {
          const regLi = document.createElement('li');
          regLi.className = 'nav-item ms-lg-2';
          const regA = document.createElement('a');
          regA.href = 'Registration.html';
          regA.className = 'btn btn-outline-primary btn-sm';
          regA.textContent = 'Register';
          regLi.appendChild(regA);
          navList.appendChild(regLi);
        }
      }

      // Hide hero CTAs (Find Jobs, Post a Job) for logged-out users on landing
      if (!auth.isAuthenticated) {
        const hero = document.getElementById('home');
        if (hero) {
          const findJobsBtn = hero.querySelector('a[href="jobs.html"]');
          const postJobBtn = hero.querySelector('a[href="post-job.html"]');
          if (findJobsBtn) findJobsBtn.style.display = 'none';
          if (postJobBtn) postJobBtn.style.display = 'none';
        }
      }
    }
  }

  // Form validation
  var forms = document.querySelectorAll('form.needs-validation');
  Array.from(forms).forEach(function (form) {
    form.addEventListener('submit', function (event) {
      if (!form.checkValidity()) {
        event.preventDefault();
        event.stopPropagation();
      }
      form.classList.add('was-validated');
    }, false);
  });
})();
