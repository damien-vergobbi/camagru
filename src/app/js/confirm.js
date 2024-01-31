document.getElementById("confirm-form").addEventListener("submit", function(event) {
    event.preventDefault();

    // Remove error classes
    document.getElementById("email").classList.remove("is-invalid");
    document.getElementById("token").classList.remove("is-invalid");

    // Reset error messages
    document.getElementById("log-error").innerHTML = "";

    // Blur inputs
    document.getElementById("email").blur();
    document.getElementById("token").blur();

    // Get values
    const email = document.getElementById("email").value;
    const token = document.getElementById("token").value;

    // Check email
    if (!email_regex.test(email)) {
        document.getElementById("email").classList.add("is-invalid");
        document.getElementById("log-error").innerHTML = "Invalid email address.";
        return;
    }

    // Check token
    if (!token || token === "") {
        document.getElementById("token").classList.add("is-invalid");
        document.getElementById("log-error").innerHTML = "Invalid or empty token";
        return;
    }

    // Start loader
    document.getElementById("loader-wrapper").classList.remove("hidden");

    // Disable submit button
    document.getElementById("submit-btn").disabled = true;
    document.getElementById("submit-btn").classList.add("hidden");

    // Send request with AJAX
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "/scripts/confirm.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function() {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            try {
                console.log(xhr.responseText);
                const text = JSON.parse(xhr.responseText);

                if (xhr.status === 200 && text?.status === "success") {
                    // Success
                    window.location.href = "signin.php";
                } else {
                    // Error
                    throw new Error(JSON.parse(xhr.responseText).message || xhr.responseText);
                }
            } catch (error) {
                console.error(error);

                // Enable submit button
                document.getElementById("submit-btn").disabled = false;
                document.getElementById("submit-btn").classList.remove("hidden");

                if (error?.message) {
                    document.getElementById("log-error").innerHTML = error?.message;
                }
            } finally {
                // Stop loader
                document.getElementById("loader-wrapper").classList.add("hidden");
            }
        }
    };
    xhr.send("email=" + encodeURIComponent(email) + "&token=" + encodeURIComponent(token));
});

// Clear error on input change
document.getElementById("email").addEventListener("input", function() {
    document.getElementById("email").classList.remove("is-invalid");
});

document.getElementById("token").addEventListener("input", function() {
    document.getElementById("token").classList.remove("is-invalid");
});
