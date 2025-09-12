const links = document.querySelectorAll('.sidebar a');
const sections = ['dashboard', 'users', 'employers', 'jobs', 'applications', 'settings'];

links.forEach(link => {
  link.addEventListener('click', (e) => {
    e.preventDefault();
    const target = link.getAttribute('data-section');

    // Remove active class from all links, add to clicked
    links.forEach(l => l.classList.remove('active'));
    link.classList.add('active');

    // Hide all sections with fade-out, then show selected with fade-in
    sections.forEach(section => {
      const sectionEl = document.getElementById(section + '-section');
      if (section !== target) {
        sectionEl.style.opacity = '0';
        sectionEl.style.transform = 'translateY(10px)';
        // Use a timeout to add d-none after animation
        setTimeout(() => {
          sectionEl.classList.add('d-none');
          sectionEl.style.position = 'absolute';
        }, 400); // Match CSS transition duration
      }
    });

    // Show target section after hiding others
    const targetEl = document.getElementById(target + '-section');
    if (targetEl.classList.contains('d-none')) {
      targetEl.classList.remove('d-none');
      // Allow a small delay for display block to take effect
      setTimeout(() => {
        targetEl.style.opacity = '1';
        targetEl.style.transform = 'translateY(0)';
        targetEl.style.position = 'relative';
      }, 50);
    }

    // Change header title (capitalize)
    document.getElementById('section-title').textContent = target.charAt(0).toUpperCase() + target.slice(1);
  });
});

// --- Chart.js example data and config ---

const userGrowthCtx = document.getElementById('userGrowthChart').getContext('2d');
const userGrowthChart = new Chart(userGrowthCtx, {
  type: 'line',
  data: {
    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep'],
    datasets: [{
      label: 'Users',
      data: [100, 150, 300, 500, 650, 800, 900, 1100, 1200],
      borderColor: '#007bff',
      backgroundColor: 'rgba(0, 123, 255, 0.3)',
      fill: true,
      tension: 0.4,
      pointRadius: 3,
    }]
  },
  options: {
    responsive: true,
    plugins: {
      legend: { display: false }
    },
    scales: {
      y: { beginAtZero: true }
    }
  }
});

const applicationsCtx = document.getElementById('applicationsChart').getContext('2d');
const applicationsChart = new Chart(applicationsCtx, {
  type: 'doughnut',
  data: {
    labels: ['Tech', 'Finance', 'Healthcare', 'Education', 'Others'],
    datasets: [{
      label: 'Applications',
      data: [400, 300, 200, 150, 100],
      backgroundColor: [
        '#007bff',
        '#6610f2',
        '#6f42c1',
        '#e83e8c',
        '#fd7e14'
      ]
    }]
  },
  options: {
    responsive: true,
    plugins: {
      legend: { position: 'bottom' }
    }
  }
});


//admin settings
// Load saved settings from localStorage on page load
function loadSettings() {
  const savedSettings = JSON.parse(localStorage.getItem('adminSettings'));
  if (!savedSettings) return;

  document.getElementById('siteName').value = savedSettings.siteName || '';
  document.getElementById('language').value = savedSettings.language || 'en';
  document.getElementById('enableRegistration').checked = savedSettings.enableRegistration ?? true;
  document.getElementById('enableEmailNotifications').checked = savedSettings.enableEmailNotifications ?? true;
}

// Save settings to localStorage when form is submitted
document.getElementById('settings-form').addEventListener('submit', function(e) {
  e.preventDefault();

  const settings = {
    siteName: document.getElementById('siteName').value.trim(),
    language: document.getElementById('language').value,
    enableRegistration: document.getElementById('enableRegistration').checked,
    enableEmailNotifications: document.getElementById('enableEmailNotifications').checked,
  };

  localStorage.setItem('adminSettings', JSON.stringify(settings));

  const message = document.getElementById('settings-message');
  message.textContent = 'Settings saved successfully!';
  message.className = 'text-success';

  setTimeout(() => {
    message.textContent = '';
  }, 3000);
});

// Call loadSettings once when the page loads
window.addEventListener('DOMContentLoaded', loadSettings);
