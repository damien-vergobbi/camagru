<?php

if ($_SERVER['REQUEST_METHOD'] !== "POST") {
    header('Location: /');
    exit();
}

// Handle POST request
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

// Check if username / email is valid
if (!filter_var($username, FILTER_VALIDATE_EMAIL) && !preg_match('/^(?=.*[a-z])[a-z0-9_-]{3,30}$/', $username)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Username / email is not valid'
    ]);
    exit();
}

// Check if password is valid
if (empty($password)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Password is not valid'
    ]);
    exit();
}


// Check if user already exists
$pdo = require '../../config/db.php';

require_once '../PHPMailer/mails.php';

$statement = $pdo->prepare('SELECT * FROM users WHERE user_email = ? OR user_name = ?');
$statement->execute([$username, $username]);
$user = $statement->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Password or username is not correct'
    ]);
    exit();
}

// Check if password is correct
if (!password_verify($password, $user['user_pass'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Password or username is not correct'
    ]);
    exit();
}

// Check if user is verified
if (!$user['user_verified']) {
    require_once '../PHPMailer/mails.php';

    $token = bin2hex(random_bytes(50));
    $token_hashed = password_hash($token, PASSWORD_DEFAULT);

    $statement = $pdo->prepare('UPDATE users SET user_token = ? WHERE user_id = ?');
    $statement->execute([$token_hashed, $user['user_id']]);

    sendTokenMail($user['user_email'], $user['user_name'], $token);

    echo json_encode([
        'status' => 'error',
        'message' => 'Account is not verified, please check your emails'
    ]);
    exit();
}

// Set session variables
session_start();
$_SESSION['user_id'] = $user['user_id'];
$_SESSION['user_name'] = $user['user_name'];
$_SESSION['user_email'] = $user['user_email'];

echo json_encode([
    'status' => 'success',
    'message' => 'Logged in'
]);

?>