<?php // Redirect to signin.php if the user is not logged in
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: signin.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Camagru</title>
</head>
<body>
</body>
</html>
