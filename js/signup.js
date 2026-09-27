const signupForm = document.getElementById("signupForm");

const nameInput = document.getElementById("name");
const usernameInput = document.getElementById("username");
const emailInput = document.getElementById("email");
const passwordInput = document.getElementById("password");
const confirmPasswordInput = document.getElementById("confirmPassword");

const nameError = document.getElementById("nameError");
const usernameError = document.getElementById("usernameError");
const emailError = document.getElementById("emailError");
const passwordError = document.getElementById("passwordError");
const confirmPasswordError = document.getElementById("confirmPasswordError");


signupForm.addEventListener("submit", function (event) {

    event.preventDefault();

    let valid = true;


    // FULL NAME
    if (nameInput.value.trim() === "") {
        nameError.textContent = "Please enter your full name.";
        valid = false;
    } else {
        nameError.textContent = "";
    }


    // USERNAME
    const username = usernameInput.value.trim();

    if (username === "") {

        usernameError.textContent = "Please enter a username.";
        valid = false;

    } else if (username.length < 3) {

        usernameError.textContent = "Username must be at least 3 characters.";
        valid = false;

    } else if (username.length > 30) {

        usernameError.textContent = "Username cannot exceed 30 characters.";
        valid = false;

    } else if (!/^[a-zA-Z0-9_]+$/.test(username)) {

        usernameError.textContent =
            "Username can only contain letters, numbers and underscores.";
        valid = false;

    } else {

        usernameError.textContent = "";
    }


    // EMAIL
    const email = emailInput.value.trim();

    if (email === "") {

        emailError.textContent = "Please enter your email address.";
        valid = false;

    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {

        emailError.textContent = "Please enter a valid email address.";
        valid = false;

    } else {

        emailError.textContent = "";
    }


    // PASSWORD
    const password = passwordInput.value;

    if (password === "") {

        passwordError.textContent = "Please enter a password.";
        valid = false;

    } else if (password.length < 8) {

        passwordError.textContent =
            "Password must be at least 8 characters.";
        valid = false;

    } else if (!/[A-Za-z]/.test(password)) {

        passwordError.textContent =
            "Password must contain at least one letter.";
        valid = false;

    } else if (!/[0-9]/.test(password)) {

        passwordError.textContent =
            "Password must contain at least one number.";
        valid = false;

    } else if (!/[!@#$%^&*(),.?":{}|<>_\-+=\/\\[\];'`~]/.test(password)) {

        passwordError.textContent =
            "Password must contain at least one special character.";
        valid = false;

    } else {

        passwordError.textContent = "";
    }


    // CONFIRM PASSWORD
    const confirmPassword = confirmPasswordInput.value;

    if (confirmPassword === "") {

        confirmPasswordError.textContent =
            "Please confirm your password.";
        valid = false;

    } else if (password !== confirmPassword) {

        confirmPasswordError.textContent =
            "Passwords do not match.";
        valid = false;

    } else {

        confirmPasswordError.textContent = "";
    }


    // EVERYTHING VALID
    if (valid) {

        signupForm.submit();

    }

});