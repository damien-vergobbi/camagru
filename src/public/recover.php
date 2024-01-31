<? // Logout user
session_start();
session_destroy();

$email = $_GET['email'] ?? '';
$token = $_GET['token'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Camagru - Confirm account</title>

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
                    Recover your password to access your account.
                </h2>
                
                <form id="confirm-form">
                    <input
                        placeholder="Email"
                        type="text"
                        id="email"
                        name="email"
                        value="<?= $email ?>"
                        required
                        readonly
                    >

                    <input
                        placeholder="Token"
                        type="text"
                        id="token"
                        name="token"
                        value="<?= $token ?>"
                        required
                        readonly
                        hidden
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
                    <p id="success-log" class="hidden">
                        Password successfully updated.
                    </p>

                    <button type="submit" id="submit-btn">
                        Update password
                    </button>

                    <div id="loader-wrapper" class="loader-wrapper hidden">
                        <div class="loader"></div>
                    </div>
                </form>
            </div>
            
            <div class="signup-div">
                <p>
                  If you are not redirected <a href="signin.php">Sign in</a>
                </p>
            </div>
        </div>
    </main>
    


    <!-- Load script from ../app/js/singin.js -->
    <script src="../app/js/utils.js" defer></script>
    <script src="../app/js/recover.js" defer></script>
</body>
</html>
