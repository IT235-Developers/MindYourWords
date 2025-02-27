document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById('signupForm');
    const passwordFields = [
        { input: document.getElementById('txt_password'), toggle: document.getElementById('togglePassword') },
        { input: document.getElementById('txt_cpassword'), toggle: document.getElementById('toggleCPassword') }
    ];
    // Get the error message element by its ID
    const errorMessage = document.getElementById('error-message');


    passwordFields.forEach(({ input, toggle }) => {
        toggle.addEventListener("click", function() {
            const isPassword = input.getAttribute("type") === "password";
            const icon = this.querySelector("i");

            input.setAttribute("type", isPassword ? "text" : "password");

            icon.classList.toggle("bi-eye", !isPassword);
            icon.classList.toggle("bi-eye-slash", isPassword);
        });
    });


    form.addEventListener('submit', function(event) {
        // Check if the password and confirm password fields match
        if (passwordFields[0].input.value !== passwordFields[1].inputconfirmPassword.value) {
            // Prevent the form from submitting if the passwords do not match
            event.preventDefault();
            errorMessage.textContent = 'Passwords do not match.';
        } else {
            errorMessage.textContent = '';
        }
    });

    document.getElementById('btn_login').addEventListener('click', function(event) {
        event.preventDefault();

        // Send fetch request to clear session variables
        fetch('auth/clear_session.php', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // Redirect to user_category.php after clearing session variables
                    window.location.href = 'login.php';
                } else {
                    console.error('Failed to clear session variables');
                }
            })
            .catch(error => console.error('Error:', error));
    });
});
