(function () {
  'use strict';
  // Redirect to login if not logged in
  const isLoggedIn = localStorage.getItem('isLoggedIn') === 'true';
  if (!isLoggedIn) {
    window.location.replace('login.html');
    return;
  }
  // Navbar greeting
  const storedUser = localStorage.getItem('loggedInUser');
  const greet = document.getElementById('userGreeting');
  if (greet && storedUser) {
    greet.textContent = `Hello, ${storedUser}!`;
  }
  // Logout confirmation and redirect to login page
  const logoutBtn = document.getElementById('logoutBtn');
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
