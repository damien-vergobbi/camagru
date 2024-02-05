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
    <title>Camagru - Editor</title>

    <link rel="stylesheet" href="../app/css/global.css">
    
    <!-- Load style from ../app/css/signin.css -->
    <link rel="stylesheet" href="../app/css/navbar.css">
    <link rel="stylesheet" href="../app/css/editor.css">
</head>
<body>
    <? require_once 'components/navbar.php'; ?>

    <main>
        <h1>Welcome to the editor page</h1>

        <? require_once 'components/stickers-list.php'; ?>

        <p id="log_error"></p>
        <input type="file" id="stickerFile" accept="image/*" hidden>
        <input type="file" id="imageFile" accept="image/*" hidden>

        <div class="container">

            <div id="video_container">
                <video id="videoElement" autoplay></video>
                <img id="imageElement" src="" alt="Background" class="hidden" />
                
                <!-- StickerElement -->
                <img id="stickerElement" src="" alt="Sticker" style="display: none;">

                <div id="buttons">
                    <button id="upStickerButton">
                        <img src="../app/media/icon-add-sticker.png" alt="Sticker">
                        Add sticker
                    </button>

                    <button id="upImageButton">
                        <img src="../app/media/icon-add-image.png" alt="Image">
                        Add image
                    </button>

                    <button id="delImageButton" class="hidden">
                        <img src="../app/media/icon-delete.png" alt="Image">
                        Clear image
                    </button>

                    <button id="captureButton">
                        <img src="../app/media/icon-take-photo.png" alt="Camera">
                        Take photo
                    </button>
                </div>
            </div>
            
            <div class="left_bar">
                <h2>Previous images</h2>
                <div id="previous_images">
                    <p>
                        No previous images
                    </p>
                </div>
            </div>
        </div>
    </main>

    <!-- Load script from ../app/js/editor.js -->
    <script src="../app/js/editor.js" defer></script>
</body>
</html>
