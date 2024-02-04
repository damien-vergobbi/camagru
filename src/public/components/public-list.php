<?php

function renderItem($id, $url, $username, $likes, $comments, $liked) {
  $imageUrl = $liked ? '../app/media/icon-like-fill.png' : '../app/media/icon-like.png';

  echo '<a href="/post.php?id='.$id.'" id="post-'.$id.'" class="item-div" title="Click to see this post from '.$username.'">';
  echo '<img src="' . $url . '" alt="Logo Camagru">';
  echo '<div class="item-footer">';
  echo '<div>';
  echo '<img src="../app/media/icon-user.png" alt="User" height="20" width="20">';
  echo '<p>' . $username . '</p>';
  echo '</div>';
  echo '<div class="footer-end">';
  echo '<div>';
  echo '<img src="'.$imageUrl.'" alt="Like" height="20">';
  echo '<p>' . $likes . '</p>';
  echo '</div>';
  echo '<div>';
  echo '<img src="../app/media/icon-comment.png" alt="Like" height="20">';
  echo '<p>' . $comments . '</p>';
  echo '</div>';
  echo '</div>';
  echo '</div>';
  echo '</a>';
}

$elementsByPage = 5;
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($page - 1) * $elementsByPage;

$pdo = require_once '../config/db.php';

// Get the total number of elements
$stmt = $pdo->prepare('SELECT COUNT(*) FROM posts');
$stmt->execute();
$total = $stmt->fetchColumn();

// Get the elements for the current page
$stmt = $pdo->prepare('SELECT posts.*, 
                              users.user_name AS post_user_name
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

foreach ($images as $image) {
  // Get comments and likes
  $stmt = $pdo->prepare('SELECT COUNT(*) AS comment_count FROM comments WHERE comment_post_id = :post_id');
  $stmt->bindValue(':post_id', $image['post_id'], PDO::PARAM_INT);
  $stmt->execute();
  $comments = $stmt->fetch();

  $stmt = $pdo->prepare('SELECT COUNT(like_id) AS like_count FROM likes WHERE like_post_id = :post_id');
  $stmt->bindValue(':post_id', $image['post_id'], PDO::PARAM_INT);
  $stmt->execute();
  $likes = $stmt->fetch();

  // Check if the user liked the post
  if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare('SELECT COUNT(*) AS count
                          FROM likes
                          WHERE like_post_id = :post_id AND like_user_id = :user_id');
    $stmt->bindValue(':post_id', $image['post_id'], PDO::PARAM_INT);
    $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->execute();
    $like = $stmt->fetch();
  }

  renderItem($image['post_id'], $image['post_image'], $image['post_user_name'], $likes['like_count'], $comments['comment_count'], $like['count'] ?? 0 > 0);
}

if (empty($images)) {
  echo '<p>No images found</p>';
}

// Create the pagination
$pages = ceil($total / $elementsByPage);
echo '<div class="pagination">';
for ($i = 1; $i <= $pages; $i++) {
  echo '<a href="?page=' . $i . '">' . $i . '</a>';
}

echo '</div';
?>