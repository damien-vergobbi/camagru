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

    // Send request with AJAX
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "/scripts/signin.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function() {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            try {
                console.log(xhr.responseText);
                const text = JSON.parse(xhr.responseText);

                if (xhr.status === 200 && text?.status === "success") {
                    // Success
                    window.location.href = "index.php";
                    console.log(xhr.responseText);
                } else {
                    // Error
                    throw new Error(JSON.parse(xhr.responseText).message || xhr.responseText);
                }
            } catch (error) {
                console.error(error);

                if (error?.message) {
                    document.getElementById("log-error").innerHTML = error?.message;
                }
            } finally {
                // Enable submit button
                document.getElementById("submit-btn").disabled = false;
                document.getElementById("submit-btn").classList.remove("hidden");

                // Stop loader
                document.getElementById("loader-wrapper").classList.add("hidden");
            }
        }
    };
    xhr.send("username=" + encodeURIComponent(username) + "&password=" + encodeURIComponent(password));
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

     // Send request with AJAX
     const xhr = new XMLHttpRequest();
     xhr.open("POST", "/scripts/recover.php", true);
     xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
     xhr.onreadystatechange = function() {
         if (xhr.readyState === XMLHttpRequest.DONE) {
             try {
                 console.log(xhr.responseText);
                 const text = JSON.parse(xhr.responseText);
 
                 if (xhr.status === 200 && text?.status === "success") {
                    // Success
                    document.getElementById("success-log").classList.remove("hidden");
                 } else {
                    // Error
                    throw new Error(JSON.parse(xhr.responseText).message || xhr.responseText);
                 }
             } catch (error) {
                console.error(error);

                document.getElementById("success-log").classList.add("hidden");

                if (error?.message) {
                    document.getElementById("log-error").innerHTML = error?.message;
                }
             } finally {
                // Enable submit button
                document.getElementById("recover-link").disabled = false;
                document.getElementById("recover-link").classList.remove("hidden");

                // Stop loader
                document.getElementById("loader-wrapper").classList.add("hidden");
             }
         }
     };
     xhr.send("email=" + encodeURIComponent(username));
});
