<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test PHP</title>
</head>
<body>
    <h1>Bienvenue sur la page de test PHP</h1>

    <p>Ceci est une page de test PHP. Voici quelques informations sur votre environnement :</p>

    <ul>
        <li>Version PHP : <?php echo phpversion(); ?></li>
        <li>Informations sur le serveur :</li>
        <ul>
            <li>Nom du serveur : <?php echo $_SERVER['SERVER_NAME']; ?></li>
            <li>Adresse IP du serveur : <?php echo $_SERVER['SERVER_ADDR']; ?></li>
            <li>Navigateur client : <?php echo $_SERVER['HTTP_USER_AGENT']; ?></li>
        </ul>
    </ul>
</body>
</html>
