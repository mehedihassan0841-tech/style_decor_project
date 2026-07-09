// ================================
// Show / Hide Password
// ================================

const toggleButtons = document.querySelectorAll(".toggle-password");

toggleButtons.forEach(button => {

    button.addEventListener("click", function () {

        const input = this.previousElementSibling;
        const icon = this.querySelector("i");

        if (input.type === "password") {

            input.type = "text";

            icon.classList.remove("fa-eye");
            icon.classList.add("fa-eye-slash");

        } else {

            input.type = "password";

            icon.classList.remove("fa-eye-slash");
            icon.classList.add("fa-eye");

        }

    });

});
// ================================
// Password Match Check
// ================================

const password = document.getElementById("password");
const confirmPassword = document.getElementById("confirm_password");
const message = document.getElementById("password-message");
const strength = document.getElementById("password-strength");

confirmPassword.addEventListener("keyup", function () {

    if (confirmPassword.value === "") {

        message.innerHTML = "";
        return;

    }

    if (password.value === confirmPassword.value) {

        message.innerHTML = "✅ Password Matched";
        message.style.color = "green";

    } else {

        message.innerHTML = "❌ Password Doesn't Match";
        message.style.color = "red";

    }

});
// ================================
// Password Strength
// ================================

password.addEventListener("keyup", function () {

    let value = password.value;

    if (value.length === 0) {

        strength.innerHTML = "";

        return;

    }

    if (value.length < 8) {

        strength.innerHTML = "🔴 Weak Password";
        strength.style.color = "red";

    }

    else if (value.match(/[A-Z]/) && value.match(/[0-9]/)) {

        strength.innerHTML = "🟢 Strong Password";
        strength.style.color = "green";

    }

    else {

        strength.innerHTML = "🟡 Medium Password";
        strength.style.color = "orange";

    }

});
// ================================
// Loading Button
// ================================

const form = document.querySelector("form");
const registerBtn = document.getElementById("registerBtn");

form.addEventListener("submit", function () {

    registerBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating Account...';

    registerBtn.disabled = true;

});