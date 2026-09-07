const forgotForm = document.getElementById("forgotForm");
const email = document.getElementById("email");
const emailError = document.getElementById("emailError");

forgotForm.addEventListener("submit", function(event) {

    event.preventDefault();

    const emailValue = email.value.trim();

    emailError.textContent = "";

    if (emailValue === "") {
        emailError.textContent = "Email address is required.";
        return;
    }

    if (!email.validity.valid) {
        emailError.textContent = "Please enter a valid email address.";
        return;
    }

    window.location.href = "resetPassword.php";

});