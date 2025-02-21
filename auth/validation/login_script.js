document.addEventListener("DOMContentLoaded", function() {
    const password = document.getElementById("txt_password");
    const togglePassword = document.getElementById("togglePassword");

    togglePassword.addEventListener("click", function() {
        const isPassword = password.getAttribute("type") === "password";
        const icon = this.querySelector("i");

        password.setAttribute("type", isPassword ? "text" : "password");
            
        icon.classList.toggle("bi-eye", !isPassword);
        icon.classList.toggle("bi-eye-slash", isPassword);
    });
});