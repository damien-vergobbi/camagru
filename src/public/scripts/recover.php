<?php
if ($_SERVER['REQUEST_METHOD'] !== "POST") {
  header('Location: /');
  exit();
}

// Handle POST request
$email = $_POST['email'] ?? '';
$token = $_POST['token'] ?? '';
$password = $_POST['password'] ?? '';

if (!filter_var($email, FILTER_VALIDATE_EMAIL) && !preg_match('/^(?=.*[a-z])[a-z0-9_-]{3,30}$/', $email)) {
  echo json_encode([
    'status' => 'error',
    'message' => 'Invalid email format'
  ]);
  exit();
}

// Send email to user (with token)
if (empty($token) && empty($password)) {
  $pdo = require '../../config/db.php';

  $stmt = $pdo->prepare("SELECT * FROM users WHERE user_name = :name OR user_email = :email");
  $stmt->execute(['name' => $email, 'email' => $email]);

  $stmt = $stmt->fetch();

  if (!$stmt) {
    echo json_encode([
      'status' => 'error',
      'message' => 'Account does not exist'
    ]);
    exit();
  }

  $token = bin2hex(random_bytes(50));
  $token_hash = password_hash($token, PASSWORD_DEFAULT);

  // Update token in database
  $up = $pdo->prepare("UPDATE users SET user_token = :user_token WHERE user_id = :id");
  $up->execute(['user_token' => $token_hash, 'id' => $stmt['user_id']]);

  require_once '../PHPMailer/mails.php';

  $ret = sendRecoverMail($stmt['user_email'], $stmt['user_name'], $token);

  if (!$ret) {
    echo json_encode([
      'status' => 'error',
      'message' => 'Account does not exist'
    ]);
  } else {
    echo json_encode([
      'status' => 'success',
      'message' => 'Email sent'
    ]);
  }
  exit();
}

/* ======== RECOVERY PART ======== */

// Check if user exists
$pdo = require '../../config/db.php';

$stmt = $pdo->prepare("SELECT * FROM users WHERE user_name = :name OR user_email = :email");
$stmt->execute(['name' => $email, 'email' => $email]);

$stmt = $stmt->fetch();

if (!$stmt) {
  echo json_encode([
    'status' => 'error',
    'message' => 'Account does not exist'
  ]);
  exit();
}

// Check if token is valid
if (!password_verify($token, $stmt['user_token'])) {
  echo json_encode([
    'status' => 'error',
    'message' => 'Invalid token'
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

// Update password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

$up = $pdo->prepare("UPDATE users SET user_pass = :user_pass, user_token = :token, user_verified = 1 WHERE user_id = :id");
$up->execute(['user_pass' => $hashedPassword, 'token' => $token, 'id' => $stmt['user_id']]);

echo json_encode([
  'status' => 'success',
  'message' => 'Password updated'
]);

?>