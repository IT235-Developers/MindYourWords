document.addEventListener("DOMContentLoaded", function() {
    const form = document.getElementById('signupForm');
    const password = document.getElementById('txt_password');
    const confirmPassword = document.getElementById('txt_cpassword');
    // Get the error message element by its ID
    const errorMessage = document.getElementById('error-message');

    form.addEventListener('submit', function(event) {
        // Check if the password and confirm password fields match
        if (password.value !== confirmPassword.value) {
            // Prevent the form from submitting if the passwords do not match
            event.preventDefault();
            errorMessage.textContent = 'Passwords do not match.';
        } else {
            errorMessage.textContent = '';
        }
    });
});
