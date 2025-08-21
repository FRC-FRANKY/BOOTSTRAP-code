// Forgot password form handling
(function () {
  'use strict';
  
  const form = document.querySelector('form.needs-validation');
  const resetMessage = document.getElementById('resetMessage');
  
  form.addEventListener('submit', function (event) {
    event.preventDefault();
    
    if (!form.checkValidity()) {
      event.stopPropagation();
      form.classList.add('was-validated');
      return;
    }
    
    form.classList.add('was-validated');
    
    // Get email value
    const email = document.getElementById('email').value.trim();
    
    // Show success message
    resetMessage.classList.remove('d-none');
    resetMessage.classList.remove('alert-info');
    resetMessage.classList.add('alert-success');
    resetMessage.textContent = `Password reset link sent to ${email}. Please check your email.`;
    
    // Disable form submission
    const submitBtn = form.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.textContent = 'Link Sent!';
    
    // Reset form after 3 seconds
    setTimeout(() => {
      form.reset();
      form.classList.remove('was-validated');
      resetMessage.classList.add('d-none');
      submitBtn.disabled = false;
      submitBtn.textContent = 'Send Reset Link';
    }, 3000);
  });
})();
