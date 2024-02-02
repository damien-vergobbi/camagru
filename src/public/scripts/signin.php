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

try {
    // Check if user already exists
    $pdo = require_once '../../config/db.php';

    require_once '../PHPMailer/mails.php';

    $statement = $pdo->prepare('SELECT * FROM users WHERE user_email = ? OR user_name = ?');
    $statement->execute([$username, $username]);
    $user = $statement->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        throw new Exception('Username or password is not correct');
    }

    // Check if password is correct
    if (!password_verify($password, $user['user_pass'])) {
        throw new Exception('Username or password is not correct');
    }

    // Check if user is verified
    if (!$user['user_verified']) {
        require_once '../PHPMailer/mails.php';

        $token = bin2hex(random_bytes(50));
        $token_hashed = password_hash($token, PASSWORD_DEFAULT);

        $statement = $pdo->prepare('UPDATE users SET user_token = ? WHERE user_id = ?');
        $exec = $statement->execute([$token_hashed, $user['user_id']]);

        if (!$exec) {
            throw new Exception('Token not updated');
        }

        sendTokenMail($user['user_email'], $user['user_name'], $token);

        throw new Exception('Account is not verified, please check your emails');
    }

    // Set session variables
    session_start();
    $_SESSION['user_id'] = $user['user_id'];
    $_SESSION['user_name'] = $user['user_name'];
    $_SESSION['user_email'] = $user['user_email'];
    $_SESSION['user_verified'] = $user['user_verified'];
    $_SESSION['user_mails'] = $user['user_mails'];

    echo json_encode([
        'status' => 'success',
        'message' => 'Logged in'
    ]);
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>