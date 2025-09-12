// Authentication Service for JobFilter
class AuthService {
  constructor() {
    this.isAuthenticated = false;
    this.currentUser = null;
    this.rolePermissions = {
      job_seeker: ['dashboard.html', 'jobs.html', 'about.html', 'contact.html', 'user-profile.html'],
      employer: ['dashboard.html', 'post-job.html', 'about.html', 'contact.html', 'user-profile.html'],
      admin: ['*']
    };
    this.init();
  }

  init() {
    // Check if user is already logged in
    this.checkAuthStatus();
    
    // Set up logout button event listener
    this.setupLogoutButton();

    // Update navbar on load
    this.updateNavbar();
  }

  checkAuthStatus() {
    const isLoggedIn = localStorage.getItem('isLoggedIn') === 'true';
    const userData = localStorage.getItem('loggedInUser');
    
    if (isLoggedIn && userData) {
      this.isAuthenticated = true;
      this.currentUser = JSON.parse(userData);
    } else {
      this.isAuthenticated = false;
      this.currentUser = null;
    }
    
    return this.isAuthenticated;
  }

  login(email, password) {
    // Sample authentication for demo roles
    const users = [
      { email: 'jobseeker@demo.com', password: 'password123', role: 'job_seeker', name: 'Job Seeker' },
      { email: 'employer@demo.com', password: 'password123', role: 'employer', name: 'Employer' },
      { email: 'admin@demo.com', password: 'password123', role: 'admin', name: 'Administrator' }
    ];

    const user = users.find(u => u.email === email && u.password === password);
    
    if (user) {
      this.isAuthenticated = true;
      this.currentUser = { email: user.email, name: user.name, role: user.role };
      
      // Store in localStorage
      localStorage.setItem('isLoggedIn', 'true');
      localStorage.setItem('loggedInUser', JSON.stringify(this.currentUser));
      
      return { success: true, message: 'Login successful' };
    } else {
      return { success: false, message: 'Invalid email or password' };
    }
  }

  logout() {
    this.isAuthenticated = false;
    this.currentUser = null;
    
    // Clear localStorage
    localStorage.removeItem('isLoggedIn');
    localStorage.removeItem('loggedInUser');
    
    // Redirect to home page
    window.location.href = 'login.html';
  }

  requireAuth() {
    if (!this.isAuthenticated) {
      // Store current page for redirect after login
      localStorage.setItem('redirectUrl', window.location.href);
      window.location.href = 'login.html';
    }
  }

  redirectAfterLogin() {
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

  updateNavbar() {
    const isLoggedIn = this.isAuthenticated;
    const loginNav = document.getElementById('loginNav');
    const signupNav = document.getElementById('signupNav');

    if (isLoggedIn) {
      // User is logged in - hide login/register buttons
      if (loginNav) loginNav.classList.add('d-none');
      if (signupNav) signupNav.classList.add('d-none');
      
      // Add logout button if it doesn't exist
      if (!document.getElementById('logoutBtn')) {
        const navList = document.querySelector('#navMenu .navbar-nav');
        if (navList) {
          const logoutLi = document.createElement('li');
          logoutLi.className = 'nav-item ms-lg-2';
          // Use global logout() to ensure confirmation everywhere
          logoutLi.innerHTML = '<button id="logoutBtn" class="btn btn-outline-danger btn-sm" onclick="logout()">Log out</button>';
          navList.appendChild(logoutLi);
        }
      }

      // Role-based visibility for nav links
      try {
        const role = (this.currentUser && this.currentUser.role) || 'job_seeker';
        const allowed = this.rolePermissions[role] || [];
        const navLinks = document.querySelectorAll('#navMenu .nav-link');
        navLinks.forEach(link => {
          const href = (link.getAttribute('href') || '').trim();
          // Ignore anchors and external links
          if (!href || href.startsWith('#') || href.startsWith('http')) return;
          // Always allow login/register pages handling elsewhere
          const file = href.split('?')[0].split('#')[0];
          const li = link.closest('li');
          const isAllowed = allowed.includes('*') || allowed.includes(file);
          if (li) {
            if (isAllowed) {
              li.classList.remove('d-none');
            } else {
              li.classList.add('d-none');
            }
          }
        });
      } catch (e) {
        // no-op
      }
    } else {
      // User is not logged in - show login/register buttons
      if (loginNav) loginNav.classList.remove('d-none');
      if (signupNav) signupNav.classList.remove('d-none');
      
      // Remove logout button if it exists
      const logoutBtn = document.getElementById('logoutBtn');
      if (logoutBtn && logoutBtn.parentElement) {
        // Remove the wrapping <li> if present, else just remove the button
        const wrapper = logoutBtn.closest('li') || logoutBtn;
        wrapper.remove();
      }

      // For logged-out users, keep all public links visible
      const navLinks = document.querySelectorAll('#navMenu .nav-link');
      navLinks.forEach(link => {
        const li = link.closest('li');
        if (li) li.classList.remove('d-none');
      });
    }
  }

  setupLogoutButton() {
    // Global logout function
    window.logout = () => {
      if (window.confirm('Are you sure you want to log out?')) {
        this.logout();
      }
    };

    // Bind click for any existing #logoutBtn
    const bindDirectButton = () => {
      const btn = document.getElementById('logoutBtn');
      if (btn) {
        btn.onclick = (e) => {
          e.preventDefault();
          window.logout();
        };
      }
    };
    bindDirectButton();

    // Delegate clicks for any element declaring data-action="logout"
    document.addEventListener('click', (e) => {
      const target = e.target;
      if (target && target.closest('[data-action="logout"]')) {
        e.preventDefault();
        window.logout();
      }
    });
  }
}

// Initialize authentication service
const auth = new AuthService();

// Export for use in other files
if (typeof module !== 'undefined' && module.exports) {
  module.exports = AuthService;
}
