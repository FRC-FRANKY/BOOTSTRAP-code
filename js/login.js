// Simple client-side validation toggle
(function () {
  'use strict';
  // Sample account
  const SAMPLE_USERNAME = 'demo';
  const SAMPLE_EMAIL = 'demo@example.com';
  const SAMPLE_PASSWORD = 'password123';
  const form = document.querySelector('form.needs-validation');
  form.addEventListener('submit', function (event) {
    event.preventDefault();
    if (!form.checkValidity()) {
      event.stopPropagation();
      form.classList.add('was-validated');
      return;
    }
    form.classList.add('was-validated');
    const inputVal = document.getElementById('email').value.trim();
    const pwdVal = document.getElementById('password').value;
    const matchesUser = inputVal.toLowerCase() === SAMPLE_EMAIL || inputVal === SAMPLE_USERNAME;
    const matchesPass = pwdVal === SAMPLE_PASSWORD;
    const errorBox = document.getElementById('loginError');
    if (!(matchesUser && matchesPass)) {
      errorBox.classList.remove('d-none');
      return;
    }
    errorBox.classList.add('d-none');
    // Mark as logged in and go to homepage
    localStorage.setItem('isLoggedIn', 'true');
    localStorage.setItem('loggedInUser', inputVal);
    window.location.href = 'Homepage.html';
  }, false);

  // Password visibility toggle
  const pwd = document.getElementById('password');
  const toggle = document.getElementById('togglePassword');
  toggle.addEventListener('click', function () {
    const isHidden = pwd.getAttribute('type') === 'password';
    pwd.setAttribute('type', isHidden ? 'text' : 'password');
    toggle.textContent = isHidden ? '🙈' : '👁️';
  });
})();
