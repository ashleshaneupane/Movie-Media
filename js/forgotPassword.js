const forgotForm = document.getElementById("forgotForm");

const email = document.getElementById("email");
const emailError = document.getElementById("emailError");

forgotForm.addEventListener("submit", function (event) {

    emailError.textContent = "";

    const emailValue = email.value.trim();

    if (emailValue === "") {
        event.preventDefault();
        emailError.textContent = "Email address is required.";
        return;
    }

    if (!email.validity.valid) {
        event.preventDefault();
        emailError.textContent = "Please enter a valid email address.";
        return;
    }

    // If valid, allow the form to submit to PHP.
});