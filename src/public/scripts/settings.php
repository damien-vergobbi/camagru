<?php

if ($_SERVER['REQUEST_METHOD'] !== "POST") {
    header('Location: /');
    exit();
}

// Handle POST request
$email = $_POST['email'] ?? '';
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';
$notif = $_POST['notif'] === "true" ? 1 : 0;
$userID = $_POST['user_id'] ?? 0;

// Check user id
if (!is_numeric($userID) || $userID <= 0) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid user id'
    ]);
    exit();
}

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
if ($password !== "" && !preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{6,30}$/', $password)) {
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

    $statement = $pdo->prepare('SELECT * FROM users WHERE (user_email = ? OR user_name = ?) AND user_id <> ?');
    $statement->execute([$email, $username, $userID]);

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

    // Get current pass hash
    $statement = $pdo->prepare('SELECT user_pass FROM users WHERE user_id = ?');
    $statement->execute([$userID]);

    $user = $statement->fetch(PDO::FETCH_ASSOC);

    $statement = $pdo->prepare('UPDATE users SET user_email = ?, user_name = ?, user_pass = ?, user_mails = ? WHERE user_id = ?');
    $exec = $statement->execute([$email, $username, $password === "" ? $user['user_pass'] : $hashedPassword, $notif, $userID]);

    if (!$exec) {
        throw new Exception('User not updated');
    }

    // Clear session
    session_start();
    unset($_SESSION['user_email']);
    unset($_SESSION['user_name']);
    unset($_SESSION['user_email']);
    unset($_SESSION['user_mails']);

    // Update session
    $_SESSION['user_email'] = $email;
    $_SESSION['user_name'] = $username;
    $_SESSION['user_email'] = $email;
    $_SESSION['user_mails'] = $notif === 1;


    echo json_encode([
        'status' => 'success',
        'message' => 'User updated'
    ]);
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>