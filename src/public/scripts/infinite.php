<?php
session_start();

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

$pdo = require_once '../../config/db.php';

// Get current page from the URL
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$elementsByPage = 5;
$offset = ($page - 1) * $elementsByPage;

// Get the elements for the current page
$stmt = $pdo->prepare('SELECT posts.*, 
                              users.user_name AS post_user_name,
                              COUNT(comments.comment_id) AS comment_count,
                              COUNT(likes.like_id) AS like_count
                      FROM posts
                      LEFT JOIN users ON posts.post_user_id = users.user_id
                      LEFT JOIN comments ON posts.post_id = comments.comment_post_id
                      LEFT JOIN likes ON posts.post_id = likes.like_post_id
                      GROUP BY posts.post_id
                      ORDER BY posts.post_date DESC
                      LIMIT :limit OFFSET :offset');
$stmt->bindValue(':limit', $elementsByPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$images = $stmt->fetchAll();

$list = [];

foreach ($images as $image) {
    $liked = false;

    // Checked if the user liked the post
    if (isset($_SESSION['user_id'])) {
        $stmt = $pdo->prepare('SELECT COUNT(*) AS count
                              FROM likes
                              WHERE like_post_id = :post_id AND like_user_id = :user_id');
        $stmt->bindValue(':post_id', $image['post_id'], PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->execute();
        $like = $stmt->fetch();

        $liked = $like['count'] > 0;
    }

    // Get comments and likes
    $stmt = $pdo->prepare('SELECT COUNT(*) AS comment_count FROM comments WHERE comment_post_id = :post_id');
    $stmt->bindValue(':post_id', $image['post_id'], PDO::PARAM_INT);
    $stmt->execute();
    $comments = $stmt->fetch();

    $stmt = $pdo->prepare('SELECT COUNT(like_id) AS like_count FROM likes WHERE like_post_id = :post_id');
    $stmt->bindValue(':post_id', $image['post_id'], PDO::PARAM_INT);
    $stmt->execute();
    $likes = $stmt->fetch();

    $prefixPath = "http://" . getenv('SERVER_IP') . ":80/posts/";

    $list[] = [
        'post_id' => $image['post_id'],
        'post_image' => $prefixPath . $image['post_image'],
        'post_user_name' => $image['post_user_name'],
        'like_count' => $likes['like_count'],
        'comment_count' => $comments['comment_count'],
        'liked' => $liked
    ];
}

echo json_encode($list);
?>