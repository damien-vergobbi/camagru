<?php

if ($_SERVER['REQUEST_METHOD'] !== "POST") {
    header('Location: /');
    exit();
}

// Handle POST request
$email = $_POST['email'] ?? '';
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

// Neutralize HTML tags
$email = htmlspecialchars($email);
$username = htmlspecialchars($username);
$password = htmlspecialchars($password);

// Check if email is valid
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid email format'
    ]);
    exit();
}

// Check if username is valid
if (!preg_match('/^(?=.*[a-z])[a-z0-9_-]{3,30}$/', $username)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Username is not valid'
    ]);
    exit();
}

// Check if password is valid
if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{6,30}$/', $password)) {
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
    
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $statement = $pdo->prepare('SELECT * FROM users WHERE user_email = ? OR user_name = ?');
    $statement->execute([$email, $username]);

    $user = $statement->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        echo json_encode([
            'status' => 'error',
            'message' => $user['user_email'] === $email ? 'Email already exists' : 'Username already exists',
            'field' => $user['user_email'] === $email ? 'email' : 'username'
        ]);
        exit();
    }

    // Generate token and send email
    $token = bin2hex(random_bytes(50));
    $hashedToken = password_hash($token, PASSWORD_DEFAULT);

    $statement = $pdo->prepare('INSERT INTO users (user_email, user_name, user_pass, user_token) VALUES (?, ?, ?, ?)');
    $exec = $statement->execute([$email, $username, $hashedPassword, $hashedToken]);

    if (!$exec) {
        throw new Exception('User not created');
    }

    $ret = sendTokenMail($email, $username, $token);

    echo json_encode([
        'status' => $ret ? 'success' : 'error',
        'message' => $ret ? 'email success' : 'email failed'
    ]);
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>