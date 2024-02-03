<?php
session_start();
// Get id
$post_id = $_GET['id'];

$IsLogged = isset($_SESSION['user_id']);

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

  echo json_encode([
    'post' => $post,
    'comments' => $IsLogged ? $comments : [],
    'likes' => $likes,
  ]);
} catch (PDOException $e) {
  echo json_encode([
    'error' => 'Error: ' . $e->getMessage()
  ]);
}
?>