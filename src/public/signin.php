<? // Logout user
session_start();
session_destroy();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Camagru - Sign In</title>

    <link rel="stylesheet" href="../app/css/global.css">
    
    <!-- Load style from ../app/css/signin.css -->
    <link rel="stylesheet" href="../app/css/signin.css">
</head>
<body>
    <main>
        <div class="side-div">
            <img
                src="https://via.placeholder.com/350"
                alt="Logo Camagru"    
            >
        </div>

        <div class="main-div">
            <div class="form-div">
                <h1>Camagru</h1>

                <h2>
                    Sign in to see photos and videos from your friends.
                </h2>
                
                <form id="signin-form">
                    <input
                        placeholder="Email / Username"
                        type="text"
                        id="username"
                        name="username"
                        minlength="3"
                        maxlength="90"
                        required
                    >
                    <input
                        placeholder="Password"
                        type="password"
                        id="password"
                        name="password"
                        minlength="6"
                        maxlength="30"
                        required
                    >

                    <p id="confirm-password-error"></p>
                    <p id="log-error"></p>

                    <button id="submit-btn" type="submit">Sign in</button>
                    <div id="loader-wrapper" class="loader-wrapper hidden">
                        <div class="loader"></div>
                    </div>

                    <p id="success-log" class="hidden">
                        An email has been sent to you. Click on the link to recover your password.
                    </p>

                    <a href="#" id="recover-link" class="littleLink">
                        Forgot password ?
                    </a>
                </form>
            </div>
            
            <div class="signup-div">
                <p>
                    Don't have an account ? <a href="/signup.php">Sign up</a>
                    <br>
                    Continue as <a href="/index.php">guest</a>
                </p>
            </div>
        </div>
    </main>
    


    <!-- Load script from ../app/js/signin.js -->
    <script src="../app/js/utils.js" async></script>
    <script src="../app/js/signin.js" async></script>
</body>
</html>
