<?php
if ($_SERVER['REQUEST_METHOD'] !== "POST") {
  header('Location: /');
  exit();
}

// Handle POST request
$email = $_POST['email'] ?? '';
$token = $_POST['token'] ?? '';
$password = $_POST['password'] ?? '';

// Neutralize HTML tags
$email = htmlspecialchars($email);
$token = htmlspecialchars($token);
$password = htmlspecialchars($password);

if (!filter_var($email, FILTER_VALIDATE_EMAIL) && !preg_match('/^(?=.*[a-z])[a-z0-9_-]{3,30}$/', $email)) {
  echo json_encode([
    'status' => 'error',
    'message' => 'Invalid email format'
  ]);
  exit();
}

// Send email to user (with token)
if (empty($token) && empty($password)) {
  try {
    $pdo = require '../../config/db.php';

    $stmt = $pdo->prepare("SELECT * FROM users WHERE user_name = :name OR user_email = :email");
    $stmt->execute(['name' => $email, 'email' => $email]);

    $stmt = $stmt->fetch();

    if (!$stmt) {
      throw new Exception('User not found');
    }

    $token = bin2hex(random_bytes(50));
    $token_hash = password_hash($token, PASSWORD_DEFAULT);

    // Update token in database
    $up = $pdo->prepare("UPDATE users SET user_token = :user_token WHERE user_id = :id");
    $up->execute(['user_token' => $token_hash, 'id' => $stmt['user_id']]);

    require_once '../PHPMailer/mails.php';

    $ret = sendRecoverMail($stmt['user_email'], $stmt['user_name'], $token);

    if (!$ret) {
      throw new Exception('Email not sent');
    }

    echo json_encode([
      'status' => 'success',
      'message' => 'Email sent'
    ]);
    
  } catch (Exception $e) {
    echo json_encode([
      'status' => 'error',
      'message' => $e->getMessage()
    ]);
  }
  exit();
}

/* ======== RECOVERY PART ======== */

// Check if password is valid
if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{6,30}$/', $password)) {
  echo json_encode([
    'status' => 'error',
    'message' => 'Password is not valid'
  ]);
  exit();
}

if (empty($token)) {
  echo json_encode([
    'status' => 'error',
    'message' => 'Token is not valid'
  ]);
  exit();
}

try {
  // Check if user exists
  $pdo = require '../../config/db.php';

  $stmt = $pdo->prepare("SELECT * FROM users WHERE user_name = :name OR user_email = :email");
  $stmt->execute(['name' => $email, 'email' => $email]);

  $stmt = $stmt->fetch();

  if (!$stmt) {
    throw new Exception('User not found');
    exit();
  }

  // Check if token is valid
  if (!password_verify($token, $stmt['user_token'])) {
    throw new Exception('Invalid token');
    exit();
  }

  // Update password
  $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

  $up = $pdo->prepare("UPDATE users SET user_pass = :user_pass, user_token = :token, user_verified = 1 WHERE user_id = :id");
  $exec = $up->execute(['user_pass' => $hashedPassword, 'token' => $token, 'id' => $stmt['user_id']]);

  if (!$exec) {
    throw new Exception('Password not updated');
  }

  echo json_encode([
    'status' => 'success',
    'message' => 'Password updated'
  ]);
} catch (Exception $e) {
  echo json_encode([
    'status' => 'error',
    'message' => $e->getMessage()
  ]);
}
?>