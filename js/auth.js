// Authentication Service for JobFilter
class AuthService {
  constructor() {
    this.isAuthenticated = false;
    this.currentUser = null;
    this.init();
  }

  init() {
    // Check if user is already logged in
    this.checkAuthStatus();
    
    // Set up logout button event listener
    this.setupLogoutButton();
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
    // Sample authentication - in a real app, this would check against a database
    const validCredentials = [
      { email: 'demo@example.com', password: 'password123' },
      { email: 'demo', password: 'password123' }
    ];
    
    const isValid = validCredentials.some(cred => 
      cred.email === email && cred.password === password
    );
    
    if (isValid) {
      this.isAuthenticated = true;
      this.currentUser = { email, name: 'Demo User' };
      
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
