document.getElementById("settings-form").addEventListener("submit", function(event) {
    event.preventDefault();

    // Remove error classes
    document.getElementById("email").classList.remove("is-invalid");
    document.getElementById("username").classList.remove("is-invalid");
    document.getElementById("password").classList.remove("is-invalid");
    document.getElementById("confirm-password").classList.remove("is-invalid");
    document.getElementById("notif").classList.remove("is-invalid");

    // Reset error messages
    document.getElementById("email-error").innerHTML = "";
    document.getElementById("username-error").innerHTML = "";
    document.getElementById("password-error").innerHTML = "";
    document.getElementById("confirm-password-error").innerHTML = "";
    document.getElementById("notif-error").innerHTML = "";
    document.getElementById("log-error").innerHTML = "";

    // Blur inputs
    document.getElementById("email").blur();
    document.getElementById("username").blur();
    document.getElementById("password").blur();
    document.getElementById("confirm-password").blur();
    document.getElementById("notif").blur();

    // Get values
    const email = document.getElementById("email").value;
    const username = document.getElementById("username").value;
    const password = document.getElementById("password").value;
    const passwordConfirm = document.getElementById("confirm-password").value;
    const notif = document.getElementById("notif").checked;
    const userID = parseInt(document.getElementById("user-id")?.value || 0);

    if (!userID || userID === 0) {
        document.getElementById("log-error").innerHTML = "Invalid user ID.";
        return;
    }

    if (password !== passwordConfirm) {
        document.getElementById("password").classList.add("is-invalid");
        document.getElementById("confirm-password").classList.add("is-invalid");
        document.getElementById("confirm-password-error").innerHTML = "Passwords do not match.";
        return;
    }

    // Check email
    if (!email_regex.test(email)) {
        document.getElementById("email").classList.add("is-invalid");
        document.getElementById("email-error").innerHTML = "Invalid email address.";
        return;
    }

    // Check username
    if (!username_regex.test(username)) {
        document.getElementById("username").classList.add("is-invalid");
        document.getElementById("username-error").innerHTML = "Invalid username.";
        return;
    }

    // Check password
    if (password !== "" && !password_regex.test(password)) {
        document.getElementById("password").classList.add("is-invalid");
        document.getElementById("password-error").innerHTML = "Invalid password.";
        return;
    }

    // Start loader
    document.getElementById("loader-wrapper").classList.remove("hidden");

    // Disable submit button
    document.getElementById("submit-btn").disabled = true;
    document.getElementById("submit-btn").classList.add("hidden");


    // Send request with AJAX
    try {
        const xhr = new XMLHttpRequest();
        xhr.open("POST", "/scripts/settings.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function() {
            try {
                if (xhr.readyState === XMLHttpRequest.UNSENT) {
                    // Error
                    document.getElementById("log-error").innerHTML = "An error occurred. Please try again.";
                    return;
                }

                if (xhr.readyState === XMLHttpRequest.DONE) {
                    // Stop loader
                    document.getElementById("loader-wrapper").classList.add("hidden");

                    if (xhr.status !== 200) {
                        // Error
                        document.getElementById("log-error").innerHTML = "An error occurred. Please try again.";
                        return;
                    }

                    const text = JSON.parse(xhr.responseText);

                    if (text?.status === "success") {
                        // Success
                        document.getElementById("success-log").classList.remove("hidden");
                    } else {
                        // Error
                        if (text?.message && !text?.field) {
                            document.getElementById("log-error").innerHTML = text.message;
                        }

                        if (text?.field) {
                            document.getElementById(text.field).classList.add("is-invalid");
                            document.getElementById(text.field + "-error").innerHTML = text.message;
                        }

                        throw new Error(xhr.responseText);
                    }
                }
            } catch (e) {
                // Enable submit button
                document.getElementById("submit-btn").disabled = false;
                document.getElementById("submit-btn").classList.remove("hidden");

                // Stop loader
                document.getElementById("loader-wrapper").classList.add("hidden");
            }
        };
        xhr.send("email=" + encodeURIComponent(email) + "&username=" + encodeURIComponent(username) + "&password=" + encodeURIComponent(password) + "&notif=" + notif + "&user_id=" + userID);
    } catch (e) {
        document.getElementById("log-error").innerHTML = "An error occurred. Please try again.";

        // Enable submit button
        document.getElementById("submit-btn").disabled = false;
        document.getElementById("submit-btn").classList.remove("hidden");

        // Stop loader
        document.getElementById("loader-wrapper").classList.add("hidden");
    }
});

// Clear error on input change
document.getElementById("email").addEventListener("input", function() {
    document.getElementById("email").classList.remove("is-invalid");
    document.getElementById("email-error").innerHTML = "";
});

document.getElementById("username").addEventListener("input", function() {
    document.getElementById("username").classList.remove("is-invalid");
    document.getElementById("username-error").innerHTML = "";
});

document.getElementById("password").addEventListener("input", function() {
    document.getElementById("password").classList.remove("is-invalid");
    document.getElementById("password-error").innerHTML = "";
});

document.getElementById("confirm-password").addEventListener("input", function() {
    document.getElementById("confirm-password").classList.remove("is-invalid");
    document.getElementById("confirm-password-error").innerHTML = "";
});
