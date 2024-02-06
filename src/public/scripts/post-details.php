<?php
session_start();

// Get id
$post_id = $_GET['id'];

if (!isset($post_id) || $post_id < 0 || !is_numeric($post_id)) {
  echo json_encode([
    'error' => 'Post id is required'
  ]);
  return;
}

$IsLogged = isset($_SESSION['user_id']);

$envFile = __DIR__ . '/../../.env';

if (file_exists($envFile)) {
  $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
  foreach ($lines as $line) {
    if (strpos($line, '=') !== false) {
      list($key, $value) = explode('=', $line, 2);
      $key = trim($key);
      $value = trim($value);

      // Remove " from value
      $value = str_replace('"', '', $value);
      putenv("$key=$value");
    }
  }
}

try {
  $pdo = require_once '../../config/db.php';

  // Get the post
  $stmt = $pdo->prepare('SELECT posts.*, 
                                users.user_name AS post_user_name,
                                COUNT(comments.comment_id) AS comment_count,
                                COUNT(likes.like_id) AS like_count
                        FROM posts
                        LEFT JOIN users ON posts.post_user_id = users.user_id
                        LEFT JOIN comments ON posts.post_id = comments.comment_post_id
                        LEFT JOIN likes ON posts.post_id = likes.like_post_id
                        WHERE posts.post_id = :post_id
                        GROUP BY posts.post_id');
  $stmt->bindValue(':post_id', $post_id, PDO::PARAM_INT);
  $stmt->execute();
  $post = $stmt->fetch();

  if (!$post) {
    throw new Exception('Post not found');
  }

  // Get the comments
  $stmt = $pdo->prepare('SELECT comments.*, users.user_name AS comment_user_name
                        FROM comments
                        LEFT JOIN users ON comments.comment_user_id = users.user_id
                        WHERE comments.comment_post_id = :post_id
                        ORDER BY comments.comment_id DESC');
  $stmt->bindValue(':post_id', $post_id, PDO::PARAM_INT);
  $stmt->execute();
  $comments = $stmt->fetchAll();

  // Get the likes
  $stmt = $pdo->prepare('SELECT likes.*
                        FROM likes
                        WHERE likes.like_post_id = :post_id');
  $stmt->bindValue(':post_id', $post_id, PDO::PARAM_INT);
  $stmt->execute();
  $likes = $stmt->fetchAll();

  // Check if the user liked the post
  if ($IsLogged) {
    $stmt = $pdo->prepare('SELECT COUNT(*) AS count
                          FROM likes
                          WHERE like_post_id = :post_id AND like_user_id = :user_id');
    $stmt->bindValue(':post_id', $post_id, PDO::PARAM_INT);
    $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->execute();

    $like = $stmt->fetch();
  }

  $prefixPath = "http://" . getenv('SRVR_IP') . ":" . getenv('SRVR_PORT') . "/posts/";

  // Add the full path to the image
  $post['post_image'] = $prefixPath . $post['post_image'];

  echo json_encode([
    'post' => $post,
    'comments' => $IsLogged ? $comments : [],
    'likes' => $likes,
    'liked' => $IsLogged ? $like['count'] > 0 : false,
    'is_author' => $IsLogged ? $post['post_user_id'] === $_SESSION['user_id'] : false
  ]);
} catch (Exception $e) {
  echo json_encode([
    'error' => 'Error: ' . $e->getMessage()
  ]);
}
?>