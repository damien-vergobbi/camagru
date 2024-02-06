<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== "POST") {
  header('Location: /');
  exit();
}

try {
  // Get id
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

  // Check if the user liked the post
  $stmt = $pdo->prepare('SELECT COUNT(*) AS count
                        FROM likes
                        WHERE like_post_id = :post_id AND like_user_id = :user_id');
  $stmt->bindValue(':post_id', $post_id, PDO::PARAM_INT);
  $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
  $stmt->execute();

  $like = $stmt->fetch();

  if ($like['count'] > 0) {
    // Unlike the post
    $stmt = $pdo->prepare('DELETE FROM likes
                          WHERE like_post_id = :post_id AND like_user_id = :user_id');
    $stmt->bindValue(':post_id', $post_id, PDO::PARAM_INT);
    $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();

    echo json_encode([
      'likes' => $like['count'] - 1,
      'liked' => false
    ]);
  } else {
    // Like the post
    $stmt = $pdo->prepare('INSERT INTO likes (like_post_id, like_user_id)
                          VALUES (:post_id, :user_id)');
    $stmt->bindValue(':post_id', $post_id, PDO::PARAM_INT);
    $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();

    echo json_encode([
      'likes' => $like['count'] + 1,
      'liked' => true
    ]);
  }
} catch (Exception $e) {
  echo json_encode([
    'error' => 'Error: ' . $e->getMessage()
  ]);
}
?>