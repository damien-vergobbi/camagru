<?php

function renderItem($id, $url, $username, $likes, $comments) {
  echo '<a href="/post.php?id='.$id.'">';
  echo '<div class="item-div">';
  echo '<img src="' . $url . '" alt="Logo Camagru">';
  echo '<div class="item-footer">';
  echo '<div>';
  echo '<img src="../app/media/icon-user.png" alt="User" height="20" width="20">';
  echo '<p>' . $username . '</p>';
  echo '</div>';
  echo '<div class="footer-end">';
  echo '<div>';
  echo '<img src="../app/media/icon-like.png" alt="Like" height="20">';
  echo '<p>' . $likes . '</p>';
  echo '</div>';
  echo '<div>';
  echo '<img src="../app/media/icon-comment.png" alt="Like" height="20">';
  echo '<p>' . $comments . '</p>';
  echo '</div>';
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

foreach ($images as $image) {
  renderItem($image['post_id'], $image['post_image'], $image['post_user_name'], $image['like_count'], $image['comment_count']);
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