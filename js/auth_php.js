// Authentication Service for JobFilter with PHP Backend
class AuthServicePHP {
  constructor() {
    this.isAuthenticated = false;
    this.currentUser = null;
    this.rolePermissions = {
      job_seeker: ['dashboard.php', 'jobs.php', 'about.php', 'contact.php', 'user-profile.php'],
      employer: ['dashboard.php', 'post-job.php', 'about.php', 'contact.php', 'user-profile.php'],
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

    // Enforce role-based access for current page
    this.enforceRoleAccess();
  }

  async checkAuthStatus() {
    try {
      const response = await fetch('api_auth.php?action=check_auth');
      const data = await response.json();
      
      if (data.success && data.authenticated) {
        this.isAuthenticated = true;
        this.currentUser = data.user;
      } else {
        this.isAuthenticated = false;
        this.currentUser = null;
      }
    } catch (error) {
      console.error('Error checking auth status:', error);
      this.isAuthenticated = false;
      this.currentUser = null;
    }
    
    return this.isAuthenticated;
  }

  async login(email, password) {
    try {
      const formData = new FormData();
      formData.append('action', 'login');
      formData.append('email', email);
      formData.append('password', password);

      const response = await fetch('api_auth.php', {
        method: 'POST',
        body: formData
      });
      
      const data = await response.json();
      
      if (data.success) {
        this.isAuthenticated = true;
        this.currentUser = data.user;
        return { success: true, message: data.message };
      } else {
        return { success: false, message: data.message };
      }
    } catch (error) {
      console.error('Login error:', error);
      return { success: false, message: 'Login failed. Please try again.' };
    }
  }

  async logout() {
    try {
      const response = await fetch('api_auth.php?action=logout');
      const data = await response.json();
      
      this.isAuthenticated = false;
      this.currentUser = null;
      
      // Redirect to login page
      window.location.href = 'login.php';
    } catch (error) {
      console.error('Logout error:', error);
      // Force logout even if API call fails
      this.isAuthenticated = false;
      this.currentUser = null;
      window.location.href = 'login.php';
    }
  }

  async register(formData) {
    try {
      formData.append('action', 'register');

      const response = await fetch('api_auth.php', {
        method: 'POST',
        body: formData
      });
      
      const data = await response.json();
      
      if (data.success) {
        this.isAuthenticated = true;
        this.currentUser = data.user;
        return { success: true, message: data.message };
      } else {
        return { success: false, message: data.message };
      }
    } catch (error) {
      console.error('Registration error:', error);
      return { success: false, message: 'Registration failed. Please try again.' };
    }
  }

  requireAuth() {
    if (!this.isAuthenticated) {
      // Store current page for redirect after login
      localStorage.setItem('redirectUrl', window.location.href);
      window.location.href = 'login.php';
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
      window.location.href = 'dashboard.php';
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
      
      // Add logout button only for classic collapse navbars (not offcanvas)
      const isOffcanvasMenu = !!document.querySelector('#navMenu.offcanvas, .offcanvas#navMenu');
      const hasOffcanvasLogout = !!document.querySelector('#navMenu .offcanvas-body [data-action="logout"], #navMenu .offcanvas-body [onclick="logout()"], #navMenu .offcanvas-body #logoutBtn');
      if (!isOffcanvasMenu && !document.getElementById('logoutBtn')) {
        const navList = document.querySelector('#navMenu .navbar-nav');
        if (navList) {
          const logoutLi = document.createElement('li');
          logoutLi.className = 'nav-item ms-lg-2';
          logoutLi.innerHTML = '<button id="logoutBtn" class="btn btn-outline-danger btn-sm" data-action="logout">Log out</button>';
          navList.appendChild(logoutLi);
        }
      } else if (hasOffcanvasLogout) {
        // Ensure the offcanvas logout buttons use the delegated logout handler
        document.querySelectorAll('#navMenu .offcanvas-body [onclick="logout()"]').forEach(btn => {
          btn.removeAttribute('onclick');
          btn.setAttribute('data-action', 'logout');
        });
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

  // Determine whether current user may access the current page; if not, force re-login
  enforceRoleAccess() {
    try {
      const currentFile = (window.location.pathname.split('/').pop() || '').split('?')[0].split('#')[0];
      if (!currentFile) return;

      // Public pages that should not enforce role checks
      const publicFiles = ['login.php', 'Registration.php', 'landing.php', 'about.php', 'contact.php'];
      if (publicFiles.includes(currentFile)) return;

      // Require auth for app pages
      if (!this.isAuthenticated) {
        localStorage.setItem('redirectUrl', window.location.href);
        window.location.href = 'login.php';
        return;
      }
        //Changing Account Role
      const role = (this.currentUser && this.currentUser.role) || 'job_seeker';
      const allowed = this.rolePermissions[role] || [];
      const isAllowed = allowed.includes('*') || allowed.includes(currentFile);
      if (!isAllowed) {
        // If page is allowed for employer but not for job seeker, encourage employer login
        const employerAllowed = (this.rolePermissions['employer'] || []).includes('*') || (this.rolePermissions['employer'] || []).includes(currentFile);
        const message = employerAllowed
          ? 'Access requires Employer account. Please sign in as employer.'
          : 'Access denied for your role. Please sign in with the correct account.';
        try { alert(message); } catch (_) {}
        localStorage.setItem('redirectUrl', window.location.href);
        // Force re-authentication
        this.logout();
      }
    } catch (_) {
      // no-op
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
const auth = new AuthServicePHP();

// Export for use in other files
if (typeof module !== 'undefined' && module.exports) {
  module.exports = AuthServicePHP;
}
