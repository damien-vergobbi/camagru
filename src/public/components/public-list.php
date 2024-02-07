<?php
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


function renderItem($id, $url, $username, $likes, $comments, $liked) {
  $imageUrl = $liked ? '../app/media/icon-like-fill.png' : '../app/media/icon-like.png';
  $prefixPath = "http://" . getenv('SRVR_IP') . ":" . getenv('SRVR_PORT') . "/posts/";

  echo '<a href="/post.php?id='.$id.'" id="post-'.$id.'" class="item-div" title="Click to see this post from '.$username.'">';
  echo '<img src="' . $prefixPath . $url . '" alt="Post" onerror="if (this.src !== \'../app/media/error404.png\') this.src = \'../app/media/error404.png\';">';
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

function renderPagination($total, $elementsByPage, $page, $user) {
  $pages = ceil($total / $elementsByPage);
  $withUser = $user ? '&user=' . $user : '';
  echo '<div class="pagination">';
  for ($i = 1; $i <= $pages; $i++) {
    $active = $i == $page ? 'active' : '';
    echo '<a href="/index.php?page=' . $i . $withUser . '" class="' . $active . '">' . $i . '</a>';
  }

  echo '</div>';
}

try {
  $elementsByPage = 5;
  $page = isset($_GET['page']) ? $_GET['page'] : 1;
  $user = isset($_GET['user']) ? $_GET['user'] : null;
  $offset = ($page - 1) * $elementsByPage;

  $pdo = require_once '../config/db.php';

  // Get the total number of elements
  $stmt = $pdo->prepare('SELECT COUNT(*) FROM posts');
  $stmt->execute();
  $total = $stmt->fetchColumn();

  if ($user !== null) {
    // get the user id
    $stmt = $pdo->prepare('SELECT user_id FROM users WHERE user_name = :user');
    $stmt->bindValue(':user', $user, PDO::PARAM_STR);
    $stmt->execute();
    $temp = $stmt->fetchColumn();

    if ($temp === false || !$temp) {
      echo '<p>User "'. $user .'" not found</p>';
      echo '<a href="/index.php">See all</a>';
      return;
    }

    // Count the total number of elements
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM posts WHERE post_user_id = :user');
    $stmt->bindValue(':user', $temp, PDO::PARAM_INT);
    $stmt->execute();
    $total = $stmt->fetchColumn();
  }

  // Get the elements for the current page
  if ($user === null) {
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
  } else {
    $stmt = $pdo->prepare('SELECT posts.*, 
                              users.user_name AS post_user_name
                            FROM posts
                            LEFT JOIN users ON posts.post_user_id = users.user_id
                            LEFT JOIN comments ON posts.post_id = comments.comment_post_id
                            LEFT JOIN likes ON posts.post_id = likes.like_post_id
                            WHERE users.user_name = :user
                            GROUP BY posts.post_id
                            ORDER BY posts.post_date DESC
                            LIMIT :limit OFFSET :offset');
    $stmt->bindValue(':limit', $elementsByPage, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->bindValue(':user', $user, PDO::PARAM_STR);
  }

  $stmt->execute();
  $images = $stmt->fetchAll();

  if ($user !== null) {
    echo '<h1 class="posts_from">Posts from <span>' . $user . '</span></h1>';
    echo '<a href="/index.php">See all</a>';
  } else {
    echo '<h1 class="posts_from">All posts</h1>';
  }

  if (empty($images)) {
    echo '<p>No images found</p>';
  }

  // Create the pagination
  renderPagination($total, $elementsByPage, $page, $user);

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

  // Create the pagination
  renderPagination($total, $elementsByPage, $page, $user);
} catch (Exception $e) {
  echo '<p>Error: ' . $e->getMessage() . '</p>';
} catch (Error $e) {
  echo '<p>Error: ' . $e->getMessage() . '</p>';
} catch (Throwable $e) {
  echo '<p>Error: ' . $e->getMessage() . '</p>';
} catch (PDOException $e) {
  echo '<p>Error: ' . $e->getMessage() . '</p>';
}
?>