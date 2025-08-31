// Login functionality
(function () {
  'use strict';
  
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
    
    // Simple authentication logic
    if (authenticateUser(inputVal, pwdVal)) {
      // Hide any error messages
      const errorBox = document.getElementById('loginError');
      if (errorBox) errorBox.classList.add('d-none');
      
      // Store login state
      localStorage.setItem('isLoggedIn', 'true');
      localStorage.setItem('loggedInUser', JSON.stringify({
        email: inputVal,
        name: 'Demo User'
      }));
      
      // Redirect to dashboard or intended page
      redirectAfterLogin();
    } else {
      // Show error message
      const errorBox = document.getElementById('loginError');
      if (errorBox) {
        errorBox.textContent = 'Invalid email or password. Please try again.';
        errorBox.classList.remove('d-none');
      }
    }
  }, false);

  // Authentication function
  function authenticateUser(email, password) {
    // Demo credentials - in a real app, this would check against a database
    const validCredentials = [
      { email: 'demo@example.com', password: 'password123' },
      { email: 'demo', password: 'password123' }
    ];
    
    return validCredentials.some(cred => 
      cred.email === email && cred.password === password
    );
  }

  // Redirect function
  function redirectAfterLogin() {
    // Check if there's a redirect URL in localStorage
    const redirectUrl = localStorage.getItem('redirectUrl');
    if (redirectUrl) {
      localStorage.removeItem('redirectUrl');
      window.location.href = redirectUrl;
    } else {
      // Default redirect to dashboard
      window.location.href = 'dashboard.html';
    }
  }

  // Password visibility toggle
  const pwd = document.getElementById('password');
  const toggle = document.getElementById('togglePassword');
  if (toggle && pwd) {
    toggle.addEventListener('click', function () {
      const isHidden = pwd.getAttribute('type') === 'password';
      pwd.setAttribute('type', isHidden ? 'text' : 'password');
      toggle.textContent = isHidden ? '🙈' : '👁️';
    });
  }
})();
