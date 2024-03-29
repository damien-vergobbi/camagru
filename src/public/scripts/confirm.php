<?php

if ($_SERVER['REQUEST_METHOD'] !== "POST") {
    header('Location: /');
    exit();
}

// Handle POST request
$email = $_POST['email'] ?? '';
$token = $_POST['token'] ?? '';

// Neutralize HTML tags
$email = htmlspecialchars($email);
$token = htmlspecialchars($token);

// Check if email is valid
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid email format'
    ]);
    exit();
}

// Check if username is valid
if (empty($token)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'token is not valid'
    ]);
    exit();
}

try {
    // Check if user already exists
    $pdo = require '../../config/db.php';

    // Get user_token where user_email = $email
    $stmt = $pdo->prepare('SELECT user_token FROM users WHERE user_email = ? AND user_verified = 0');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user) {
        throw new Exception('User not found');
    }

    $token_hashed = $user['user_token'];

    // Check if token is valid
    if (!password_verify($token, $token_hashed)) {
        throw new Exception('Invalid token');
    }

    // Update user_token to NULL where user_email = $email
    $stmt = $pdo->prepare('UPDATE users SET user_token = ?, user_verified = 1 WHERE user_email = ?');
    $stmt->execute([$token, $email]);

    echo json_encode([
        'status' => 'success',
        'message' => 'Account confirmed'
    ]);
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
} catch (Throwable $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'An error occurred'
    ]);
} catch (Error $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'An error occurred'
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'An error occurred'
    ]);
}
?>