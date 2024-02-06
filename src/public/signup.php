<? // Logout user
session_start();
session_destroy();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Camagru - Sign Up</title>

    <link rel="stylesheet" href="../app/css/global.css">
    
    <!-- Load style from ../app/css/signin.css -->
    <link rel="stylesheet" href="../app/css/signin.css">
</head>
<body>
    <main>
        <div class="side-div">
            <img
                src="../app/media/preview2.png"
                alt="Logo Camagru"    
            >
        </div>

        <div class="main-div">
            <div class="form-div">
                <h1>Camagru</h1>

                <h2>
                    Sign up to see photos and videos from your friends.
                </h2>
                
                <form id="signup-form">
                    <input
                        placeholder="Email"
                        type="email"
                        id="email"
                        name="email"
                        required
                    >
                    <p id="email-error"></p>

                    <input
                        placeholder="Username"
                        type="text"
                        id="username"
                        name="username"
                        minlength="3"
                        maxlength="30"
                        required
                    >
                    <p id="username-error"></p>

                    <input
                        placeholder="Password"
                        type="password"
                        id="password"
                        name="password"
                        minlength="6"
                        maxlength="30"
                        required
                    >
                    <p id="password-error"></p>

                    <input
                        placeholder="Confirm password"
                        type="password"
                        id="confirm-password"
                        name="confirm-password"
                        minlength="6"
                        maxlength="30"
                        required
                    >
                    <p id="confirm-password-error"></p>
                    <p id="log-error"></p>

                    <button id="submit-btn" type="submit">Sign up</button>
                    <div id="loader-wrapper" class="loader-wrapper hidden">
                        <div class="loader"></div>
                    </div>

                    <p id="success-log" class="hidden">
                        An email has been sent to you. Please confirm your account.
                    </p>
                </form>
            </div>
            
            <div class="signup-div">
                <p>
                    Already have an account ? <a href="/signin.php">Sign in</a>
                </p>
            </div>
        </div>
    </main>
    


    <!-- Load script from ../app/js/singup.js -->
    <script src="../app/js/utils.js" defer></script>
    <script src="../app/js/signup.js" defer></script>
</body>
</html>
