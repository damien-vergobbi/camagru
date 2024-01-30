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
                
                <form id="signin-form">
                    <input
                        placeholder="Email / Username"
                        type="text"
                        id="username"
                        name="username"
                        required
                    >
                    <input
                        placeholder="Password"
                        type="password"
                        id="password"
                        name="password"
                        minlength="6"
                        maxlength="40"
                        required
                    >

                    <button type="submit">Sign in</button>
                </form>
            </div>
            
            <div class="signup-div">
                <p>
                    Don't have an account ? <a href="signup.php">Sign up</a>
                </p>
            </div>
        </div>
    </main>
    


    <!-- Load script from ../app/js/singin.js -->
    <script src="../app/js/signin.js" defer></script>
</body>
</html>
