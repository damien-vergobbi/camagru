<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== "POST") {
  header('Location: /');
  exit();
}

try {
  // Get variables
  $post_id = intval($_POST['id']) ?? -1;
  $user_id = $_SESSION['user_id'] ?? -1;

  if (!isset($post_id) || $post_id < 0 || !is_numeric($post_id)) {
    echo json_encode([
      'error' => 'Post id is required'
    ]);
    return;
  }

  if (!isset($user_id) || $user_id < 0 || !is_numeric($user_id)) {
    echo json_encode([
      'error' => 'User id is required'
    ]);
    return;
  }

  $IsLogged = isset($_SESSION['user_id']);

  $pdo = require_once '../../config/db.php';

  // Check if the post exists
  $stmt = $pdo->prepare('SELECT * FROM posts WHERE post_id = :post_id AND post_user_id = :user_id');
  $stmt->bindValue(':post_id', $post_id, PDO::PARAM_INT);
  $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
  $stmt->execute();
  $post = $stmt->fetch();

  if (!$post) {
    throw new Exception('Post not found');
  }

  // Delete file
  $uploadDirectory = '../posts/';
  $fileName = $uploadDirectory . $post['post_image'];

  if (file_exists($fileName)) {
    unlink($fileName);
  }

  // Delete post
  $stmt = $pdo->prepare('DELETE FROM posts WHERE post_id = :post_id');
  $stmt->bindValue(':post_id', $post_id, PDO::PARAM_INT);
  $stmt->execute();

  echo json_encode([
    'success' => 'Post deleted'
  ]);

} catch (Exception $e) {
  echo json_encode([
    'error' => $e->getMessage()
  ]);
}
?>