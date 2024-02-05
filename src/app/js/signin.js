document.getElementById("signin-form").addEventListener("submit", function(event) {
    event.preventDefault();

    // Remove error classes
    document.getElementById("username").classList.remove("is-invalid");
    document.getElementById("password").classList.remove("is-invalid");

    // Reset error messages
    document.getElementById("log-error").innerHTML = "";

    // Blur inputs
    document.getElementById("username").blur();
    document.getElementById("password").blur();

    // Get values
    const username = document.getElementById("username").value;
    const password = document.getElementById("password").value;

    // Check username
    if (!email_regex.test(username) && !username_regex.test(username)) {
        document.getElementById("username").classList.add("is-invalid");
        document.getElementById("log-error").innerHTML = "Invalid username / email address.";
        return;
    }

    // Check password
    if (!password || password === "") {
        document.getElementById("password").classList.add("is-invalid");
        document.getElementById("log-error").innerHTML = "Invalid or empty password";
        return;
    }

    // Start loader
    document.getElementById("loader-wrapper").classList.remove("hidden");

    // Disable submit button
    document.getElementById("submit-btn").disabled = true;
    document.getElementById("submit-btn").classList.add("hidden");

    try {
        // Send request with AJAX
        const xhr = new XMLHttpRequest();
        xhr.open("POST", "/scripts/signin.php", true);
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
                        window.location.href = "index.php";
                    } else {
                        // Error
                        throw new Error(JSON.parse(xhr.responseText).message || xhr.responseText);
                    }
                }
            } catch (error) {
                if (error?.message) {
                    document.getElementById("log-error").innerHTML = error?.message;
                }

                // Enable submit button
                document.getElementById("submit-btn").disabled = false;
                document.getElementById("submit-btn").classList.remove("hidden");

                // Stop loader
                document.getElementById("loader-wrapper").classList.add("hidden");
            }
        };
        xhr.send("username=" + encodeURIComponent(username) + "&password=" + encodeURIComponent(password));
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
document.getElementById("username").addEventListener("input", function() {
    document.getElementById("username").classList.remove("is-invalid");
});

document.getElementById("password").addEventListener("input", function() {
    document.getElementById("password").classList.remove("is-invalid");
});

/* ======== Recover password ======== */
document.getElementById("recover-link").addEventListener("click", function(event) {
    event.preventDefault();

    const username = document.getElementById("username").value;

    // Check username
    if (!email_regex.test(username) && !username_regex.test(username)) {
        document.getElementById("username").classList.add("is-invalid");
        document.getElementById("log-error").innerHTML = "Invalid username / email address.";
        return;
    }


    // Start loader
    document.getElementById("loader-wrapper").classList.remove("hidden");

    // Disable submit button
    document.getElementById("recover-link").disabled = true;
    document.getElementById("recover-link").classList.add("hidden");

    try {
        // Send request with AJAX
        const xhr = new XMLHttpRequest();
        xhr.open("POST", "/scripts/recover.php", true);
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

                    if (xhr.status === 200 && text?.status === "success") {
                        // Success
                        document.getElementById("success-log").classList.remove("hidden");
                    } else {
                        // Error
                        throw new Error(JSON.parse(xhr.responseText).message || xhr.responseText);
                    }
                } 
            } catch (error) {
                // Enable submit button
                document.getElementById("recover-link").disabled = false;
                document.getElementById("recover-link").classList.remove("hidden");

                // Stop loader
                document.getElementById("loader-wrapper").classList.add("hidden");

                document.getElementById("success-log").classList.add("hidden");

                if (error?.message) {
                    document.getElementById("log-error").innerHTML = error?.message;
                }
            }
        };
        xhr.send("email=" + encodeURIComponent(username));
    } catch (e) {
        document.getElementById("log-error").innerHTML = "An error occurred. Please try again.";

        // Enable submit button
        document.getElementById("recover-link").disabled = false;
        document.getElementById("recover-link").classList.remove("hidden");

        // Stop loader
        document.getElementById("loader-wrapper").classList.add("hidden");
    }
});
