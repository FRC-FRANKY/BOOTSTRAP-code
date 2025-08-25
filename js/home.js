(function () {
  'use strict';
  // Determine if current page is the public landing page
  const path = (window.location.pathname || '').toLowerCase();
  const isLanding = path.endsWith('index.html') || path === '/' || path.endsWith('/');

  // Redirect to login for protected pages only
  const isLoggedIn = localStorage.getItem('isLoggedIn') === 'true';
  if (!isLoggedIn && !isLanding) {
    window.location.replace('login.html');
    return;
  }


  // Toggle navbar auth controls on landing
  const logoutBtn = document.getElementById('logoutBtn');
  const loginNav = document.getElementById('loginNav');
  const signupNav = document.getElementById('signupNav');
  if (!isLoggedIn) {
    if (logoutBtn) logoutBtn.style.display = 'none';
    if (loginNav) loginNav.classList.remove('d-none');
    if (signupNav) signupNav.classList.remove('d-none');
  } else {
    if (logoutBtn) logoutBtn.style.display = '';
    if (loginNav) loginNav.classList.add('d-none');
    if (signupNav) signupNav.classList.add('d-none');
  }

  // Landing navbar: show only Login and Register, move other tabs to dashboard
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

      // Do not show greeting item on landing

      // Inject Login button if user not logged in
      if (!isLoggedIn) {
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
    }

    // Hide hero CTAs (Find Jobs, Post a Job) for logged-out users on landing
    if (!isLoggedIn) {
      const hero = document.getElementById('home');
      if (hero) {
        const findJobsBtn = hero.querySelector('a[href="jobs.html"]');
        const postJobBtn = hero.querySelector('a[href="post-job.html"]');
        if (findJobsBtn) findJobsBtn.style.display = 'none';
        if (postJobBtn) postJobBtn.style.display = 'none';
      }
    }
  }
  // Logout confirmation and redirect to login page
  if (logoutBtn) {
    logoutBtn.addEventListener('click', function () {
      const confirmLogout = confirm('Do you want to log out?');
      if (confirmLogout) {
        localStorage.removeItem('isLoggedIn');
        window.location.href = 'login.html';
      }
    });
  }

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
