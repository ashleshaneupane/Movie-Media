const resetForm = document.getElementById("resetForm");

const newPassword = document.getElementById("newPassword");
const confirmPassword = document.getElementById("confirmPassword");

const newPasswordError = document.getElementById("newPasswordError");
const confirmPasswordError = document.getElementById("confirmPasswordError");


resetForm.addEventListener("submit", function(event) {

    event.preventDefault();


    newPasswordError.textContent = "";
    confirmPasswordError.textContent = "";


    let isValid = true;


    if (newPassword.value.trim() === "") {

        newPasswordError.textContent = "Please enter a new password.";
        isValid = false;

    } else if (newPassword.value.length < 8) {

        newPasswordError.textContent =
            "Password must be at least 8 characters.";
        isValid = false;

    }


    if (confirmPassword.value.trim() === "") {

        confirmPasswordError.textContent =
            "Please confirm your password.";
        isValid = false;

    } else if (newPassword.value !== confirmPassword.value) {

        confirmPasswordError.textContent =
            "Passwords do not match.";
        isValid = false;

    }


    if (isValid) {

        alert("Password reset successful! You can now log in.");

        resetForm.reset();

        window.location.href = "login.php";
    }

});