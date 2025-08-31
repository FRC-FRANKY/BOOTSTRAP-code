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
    // Sample user credentials (in a real app, this would be an API call)
    const users = [
      { email: 'demo@example.com', username: 'demo', password: 'password123', name: 'Demo User', role: 'jobseeker' },
      { email: 'employer@example.com', username: 'employer', password: 'password123', name: 'Demo Employer', role: 'employer' }
    ];

    const user = users.find(u => 
      (u.email.toLowerCase() === email.toLowerCase() || u.username.toLowerCase() === email.toLowerCase()) && 
      u.password === password
    );

    if (user) {
      // Store user data (without password)
      const userData = {
        email: user.email,
        username: user.username,
        name: user.name,
        role: user.role,
        loginTime: new Date().toISOString()
      };

      localStorage.setItem('isLoggedIn', 'true');
      localStorage.setItem('loggedInUser', JSON.stringify(userData));
      
      this.isAuthenticated = true;
      this.currentUser = userData;
      
      return { success: true, user: userData };
    } else {
      return { success: false, message: 'Invalid email/username or password' };
    }
  }

  logout() {
    localStorage.removeItem('isLoggedIn');
    localStorage.removeItem('loggedInUser');
    this.isAuthenticated = false;
    this.currentUser = null;
    
    // Redirect to login page
    window.location.href = 'login.html';
  }

  requireAuth(redirectTo = 'login.html') {
    if (!this.checkAuthStatus()) {
      // Store the intended destination
      localStorage.setItem('redirectAfterLogin', window.location.href);
      window.location.replace(redirectTo);
      return false;
    }
    return true;
  }

  redirectAfterLogin() {
    const redirectUrl = localStorage.getItem('redirectAfterLogin');
    if (redirectUrl) {
      localStorage.removeItem('redirectAfterLogin');
      window.location.href = redirectUrl;
    } else {
      window.location.href = 'dashboard.html';
    }
  }

  getUserRole() {
    return this.currentUser ? this.currentUser.role : null;
  }

  isJobSeeker() {
    return this.getUserRole() === 'jobseeker';
  }

  isEmployer() {
    return this.getUserRole() === 'employer';
  }

  setupLogoutButton() {
    const logoutBtn = document.getElementById('logoutBtn');
    if (logoutBtn) {
      logoutBtn.addEventListener('click', () => {
        const confirmLogout = confirm('Do you want to log out?');
        if (confirmLogout) {
          this.logout();
        }
      });
    }
  }

  updateNavbar() {
    const logoutBtn = document.getElementById('logoutBtn');
    const loginNav = document.getElementById('loginNav');
    const signupNav = document.getElementById('signupNav');
    
    if (this.isAuthenticated) {
      // User is logged in
      if (logoutBtn) logoutBtn.style.display = '';
      if (loginNav) loginNav.classList.add('d-none');
      if (signupNav) signupNav.classList.add('d-none');
    } else {
      // User is not logged in
      if (logoutBtn) logoutBtn.style.display = 'none';
      if (loginNav) loginNav.classList.remove('d-none');
      if (signupNav) signupNav.classList.remove('d-none');
    }
  }
}

// Initialize authentication service
const auth = new AuthService();

// Export for use in other files
if (typeof module !== 'undefined' && module.exports) {
  module.exports = AuthService;
}
