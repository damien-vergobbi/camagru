<?php
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
    <title>Camagru</title>

    <link href='../app/media/logo.png' rel='icon' type='image/png'>

    <link rel="stylesheet" href="../app/css/global.css">
    
    <!-- Load style from ../app/css/signin.css -->
    <link rel="stylesheet" href="../app/css/navbar.css">
    <link rel="stylesheet" href="../app/css/main.css">
</head>
<body>
    <? require_once 'components/navbar.php'; ?>
    <? require_once 'components/scroll-mode.php'; ?>
    <? require_once 'components/go-top.php'; ?>

    
    <main>
        
        <? require_once 'components/public-list.php'; ?>
        
    </main>
    
</body>
</html>
