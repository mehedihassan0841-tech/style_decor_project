// ==============================
// Show / Hide Password
// ==============================

const loginPassword = document.getElementById("loginPassword");

const togglePassword = document.querySelector(".login-password-group .toggle-password");

togglePassword.addEventListener("click", function () {

    const icon = this.querySelector("i");

    if (loginPassword.type === "password") {

        loginPassword.type = "text";

        icon.classList.remove("fa-eye");

        icon.classList.add("fa-eye-slash");

    } else {

        loginPassword.type = "password";

        icon.classList.remove("fa-eye-slash");

        icon.classList.add("fa-eye");

    }

});


// ==============================
// Loading Button
// ==============================

const loginForm = document.querySelector(".login-form-card form");

const loginBtn = document.getElementById("loginBtn");

loginForm.addEventListener("submit", function () {

    loginBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Logging in...';

    loginBtn.disabled = true;

});