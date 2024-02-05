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
                src="../app/media/preview.png"
                alt="Logo Camagru"    
            >
        </div>

        <div class="main-div">
            <div class="form-div">
                <h1>Camagru</h1>

                <h2>
                    Sign up to see photos and videos from your friends.
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

                    <p id="log-error"></p>

                    <button type="submit" id="submit-btn">
                      Confirm account
                    </button>

                    <div id="loader-wrapper" class="loader-wrapper hidden">
                        <div class="loader"></div>
                    </div>
                </form>
            </div>
            
            <div class="signup-div">
                <p>
                  If you are not redirected <a href="/signin.php">Sign in</a>
                </p>
            </div>
        </div>
    </main>
    


    <!-- Load script from ../app/js/singin.js -->
    <script src="../app/js/utils.js" defer></script>
    <script src="../app/js/confirm.js" defer></script>
</body>
</html>
