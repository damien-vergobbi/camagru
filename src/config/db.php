<?php

// Chemin vers le fichier .env
$envFile = __DIR__ . '../.env';

// Charge les variables d'environnement depuis le fichier .env
if (file_exists($envFile)) {
    $env = file_get_contents($envFile);
    $env = array_filter(explode("\n", $env));
    foreach ($env as $line) {
        putenv($line);
    }
}

// Récupère les variables d'environnement
$dbHost = getenv('DB_HOST');
$dbName = getenv('DB_NAME');
$dbUser = 'root';
$dbPassword = getenv('DB_PASS');

// Retourne une connexion à la base de données
try {
  return new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPassword);
} catch (PDOException $e) {
  echo 'Erreur de connexion : ' . $e->getMessage();
  return null;
}
