<?php
session_start();

try {
  // Get variables
  $post_id = intval($_POST['id']) ?? -1;
  $user_id = $_SESSION['user_id'] ?? -1;
  $comment = $_POST['comment'] ?? '';

  $comment_regex = '/^[a-zA-Z0-9\s.,;:!?\'-éèàêâîôûùç]{3,150}$/';

  if (!isset($post_id) || $post_id < 0) {
    echo json_encode([
      'error' => 'Post id is required'
    ]);
    return;
  }

  if (!isset($user_id) || $user_id < 0) {
    echo json_encode([
      'error' => 'User id is required'
    ]);
    return;
  }

  if (!isset($comment) || empty($comment) || !preg_match($comment_regex, $comment)) {
    echo json_encode([
      'error' => 'Comment is required and must be between 3 and 150 characters'
    ]);
    return;
  }

  $IsLogged = isset($_SESSION['user_id']);

  $pdo = require_once '../../config/db.php';

  // Check if the post exists
  $stmt = $pdo->prepare('SELECT posts.*, users.user_name AS post_user_name
                        FROM posts
                        LEFT JOIN users ON posts.post_user_id = users.user_id
                        WHERE posts.post_id = :post_id');
  $stmt->bindValue(':post_id', $post_id, PDO::PARAM_INT);
  $stmt->execute();
  $post = $stmt->fetch();

  if (!$post) {
    throw new Exception('Post not found');
  }

  // Add comment
  $stmt = $pdo->prepare('INSERT INTO comments (comment_post_id, comment_user_id, comment_text)
                        VALUES (:post_id, :user_id, :comment)');
  $stmt->bindValue(':post_id', $post_id, PDO::PARAM_INT);
  $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
  $stmt->bindValue(':comment', $comment, PDO::PARAM_STR);
  $stmt->execute();

  require_once '../PHPMailer/mails.php';

  // Get post user
  $stmt = $pdo->prepare('SELECT user_email, user_name, user_mails
                        FROM users
                        WHERE user_id = :user_id');
  $stmt->bindValue(':user_id', $post['post_user_id'], PDO::PARAM_INT);
  $stmt->execute();
  $post_user = $stmt->fetch();

  if (!$post_user) {
    throw new Exception('Post user not found');
  }

  if (intval($post_user['user_mails']) === 1) {
    // Send mail
    sendCommentMail($post_user['user_email'], $post_user['user_name'], $post_id, $_SESSION['user_name'], $comment);
  }

  echo json_encode([
    'success' => 'Comment added'
  ]);
} catch (Exception $e) {
  echo json_encode([
    'error' => 'Error: ' . $e->getMessage()
  ]);
}
?>