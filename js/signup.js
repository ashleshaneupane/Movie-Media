document.getElementById("signupForm").addEventListener("submit", function (event) {

    let name = document.getElementById("name");
    let username = document.getElementById("username");
    let email = document.getElementById("email");
    let password = document.getElementById("password");
    let confirmPassword = document.getElementById("confirmPassword");

    let namePattern = /^[A-Za-z ]{2,50}$/;
    let usernamePattern = /^[a-z0-9]{8,12}$/;

    // Clear old custom errors
    name.setCustomValidity("");
    username.setCustomValidity("");
    email.setCustomValidity("");
    password.setCustomValidity("");
    confirmPassword.setCustomValidity("");

    let firstInvalidField = null;

    // FULL NAME
    if (name.value.trim() === "") {
        name.setCustomValidity("Please enter your full name");
        firstInvalidField = name;
    }
    else if (!namePattern.test(name.value.trim())) {
        name.setCustomValidity("Name must contain at least 2 letters");
        firstInvalidField = name;
    }

    // USERNAME
    if (username.value.trim() === "") {
        username.setCustomValidity("Please enter a username");

        if (firstInvalidField === null) {
            firstInvalidField = username;
        }
    }
    else if (!usernamePattern.test(username.value.trim())) {
        username.setCustomValidity(
            "Username must be 8-12 lowercase letters or numbers"
        );

        if (firstInvalidField === null) {
            firstInvalidField = username;
        }
    }

    // EMAIL
    if (email.value.trim() === "") {
        email.setCustomValidity("Please enter your email address");

        if (firstInvalidField === null) {
            firstInvalidField = email;
        }
    }

    // PASSWORD
    if (password.value === "") {
        password.setCustomValidity("Please enter a password");

        if (firstInvalidField === null) {
            firstInvalidField = password;
        }
    }
    else if (password.value.length < 8) {
        password.setCustomValidity("Password must be at least 8 characters");

        if (firstInvalidField === null) {
            firstInvalidField = password;
        }
    }

    // CONFIRM PASSWORD
    if (confirmPassword.value === "") {
        confirmPassword.setCustomValidity("Please confirm your password");

        if (firstInvalidField === null) {
            firstInvalidField = confirmPassword;
        }
    }
    else if (confirmPassword.value !== password.value) {
        confirmPassword.setCustomValidity("Passwords do not match");

        if (firstInvalidField === null) {
            firstInvalidField = confirmPassword;
        }
    }

    // STOP FORM IF THERE IS AN ERROR
    if (firstInvalidField !== null) {
        event.preventDefault();
        firstInvalidField.reportValidity();
    }
});
