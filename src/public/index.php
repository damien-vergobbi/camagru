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
    <title>Camagru</title>
</head>
<body>

    <h1>Welcome to my camagru</h1>

    <?php if (IS_LOGGED): ?>
        <a href="signin.php">Logout</a>
    <?php else: ?>
        <a href="signin.php">Signin</a>
    <?php endif; ?>

    <?php if (IS_LOGGED): ?>
        <p>You are logged in</p>
    <?php else: ?>
        <p>You are not logged in</p>
    <?php endif; ?>

    
</body>
</html>
