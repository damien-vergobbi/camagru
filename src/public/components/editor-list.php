<?php
$envFile = __DIR__ . '/../../.env';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_name']) || !isset($_SESSION['user_email'])) {
  return;
}

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

function renderItem($id, $url) {
  $prefixPath = "http://" . getenv('SRVR_IP') . ":" . getenv('SRVR_PORT') . "/posts/";
  
  echo '<a href="/post.php?id=' . $id . '">';
  echo '<img src="' .  $prefixPath . $url . '" alt="Post" width="200" height="150" onerror="if (this.src !== \'../app/media/error404.png\') this.src = \'../app/media/error404.png\';">';
  echo '</a>';
}

try {
  require_once '../config/db.php';

  // Get user's posts
  $stmt = $pdo->prepare('SELECT post_id, post_image FROM posts WHERE post_user_id = :user_id ORDER BY post_date DESC');
  $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
  $stmt->execute();
  $images = $stmt->fetchAll();


  if (empty($images)) {
    echo '<p>No posts yet</p>';
    return;
  }

  foreach ($images as $image) {
    renderItem($image['post_id'], $image['post_image']);
  }

} catch (Exception $e) {
  echo '<p>' . $e->getMessage() . '</p>';
} catch (Throwable $e) {
  echo '<p>' . $e->getMessage() . '</p>';
} catch (Error $e) {
  echo '<p>' . $e->getMessage() . '</p>';
} catch (PDOException $e) {
  echo '<p>' . $e->getMessage() . '</p>';
}

?>