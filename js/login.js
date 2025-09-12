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
        const authedUser = authenticateUser(inputVal, pwdVal);
        if (authedUser) {
            // Hide any error messages
            const errorBox = document.getElementById('loginError');
            if (errorBox) errorBox.classList.add('d-none');

            // Store login state
            localStorage.setItem('isLoggedIn', 'true');
            localStorage.setItem('loggedInUser', JSON.stringify(authedUser));

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
        // Demo credentials with roles
        const users = [
            { email: 'jobseeker@demo.com', password: 'password123', role: 'job_seeker', name: 'Job Seeker' },
            { email: 'employer@demo.com', password: 'password123', role: 'employer', name: 'Employer' },
            { email: 'admin@demo.com', password: 'password123', role: 'admin', name: 'Administrator' }
        ];

        const user = users.find(u => u.email === email && u.password === password);
        if (!user) return null;
        return { email: user.email, name: user.name, role: user.role };
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