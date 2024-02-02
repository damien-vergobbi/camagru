<?php // Redirect to signin.php if the user is not logged in
session_start();

define('IS_LOGGED', isset($_SESSION['user_id'])
    && isset($_SESSION['user_name'])
    && isset($_SESSION['user_email']));

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Camagru - Settings</title>

    <link rel="stylesheet" href="../app/css/global.css">
    
    <!-- Load style from ../app/css/signin.css -->
    <link rel="stylesheet" href="../app/css/navbar.css">
    <link rel="stylesheet" href="../app/css/signin.css">
</head>
<body>
    <? require_once 'components/navbar.php'; ?>

    <main>
        <div class="main-div">
            <div class="form-div">
                <h1>Camagru</h1>

                <h2>
                    You can change your personnal information here.
                </h2>
                
                <form id="settings-form">
                    <input
                        placeholder="User ID"
                        type="number"
                        id="user-id"
                        value="<?= $_SESSION['user_id'] ?>"
                        readonly
                        require
                        hidden
                    >

                    <input
                        placeholder="Email"
                        type="email"
                        id="email"
                        name="email"
                        value="<?= $_SESSION['user_email'] ?>"
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
                        value="<?= $_SESSION['user_name'] ?>"
                        required
                    >
                    <p id="username-error"></p>

                    <input
                        placeholder="New password"
                        type="password"
                        id="password"
                        name="password"
                        minlength="6"
                        maxlength="30"
                    >
                    <p id="password-error"></p>

                    <input
                        placeholder="New password confirmation"
                        type="password"
                        id="confirm-password"
                        name="confirm-password"
                        minlength="6"
                        maxlength="30"
                    >
                    <p id="confirm-password-error"></p>
                    <p id="log-error"></p>

                    <!-- Notif -->
                    <div class="mails">
                        <label for="notif">Receive mails 'New comments'</label>
                        <input
                            type="checkbox"
                            id="notif"
                            name="notif"
                            <?= $_SESSION['user_mails'] ? 'checked' : '' ?>
                        >
                    </div>
                    <p id="notif-error"></p>

                    <button id="submit-btn" type="submit">
                        Save changes
                    </button>
                    <div id="loader-wrapper" class="loader-wrapper hidden">
                        <div class="loader"></div>
                    </div>

                    <p id="success-log" class="hidden">
                        Your changes have been saved.
                        <a href="index.php">Go home</a>
                    </p>
                </form>
            </div>
            
            <div class="signup-div">
                <p>
                    Leave the password fields empty if you don't want to change it.
                </p>
            </div>
        </div>
    </main>

    <!-- Load script from ../app/js/singup.js -->
    <script src="../app/js/utils.js" defer></script>
    <script src="../app/js/settings.js" defer></script>
</body>
</html>
