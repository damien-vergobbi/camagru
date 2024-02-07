<?php

try {
  require_once '../config/db.php';

  if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_name']) || !isset($_SESSION['user_email'])) {
    echo '<p>You must be logged in to see this page</p>';
    return;
  }

  // Get all stickers from folder ../app/stickers
  $stickers = array_diff(scandir('../app/stickers'), array('..', '.'));
  $checked = true;

  // Sort by creation date
  usort($stickers, function ($a, $b) {
    return filemtime('../app/stickers/' . $a) > filemtime('../app/stickers/' . $b) ? 1 : -1;
  });

  echo '<div id="stickers_list">';
  foreach ($stickers as $sticker) {
    // Check if image and valid extension
    $mimetype = mime_content_type('../app/stickers/' . $sticker);
    if (strpos($mimetype, 'image/') === false) {
      continue;
    }
    
    // Radio button with sticker
    echo '<label>';
    echo '<input type="radio" name="sticker" value="' . $sticker . '" ' . ($checked ? 'checked' : '') . '>';
    echo '<img src="../app/stickers/' . $sticker . '" alt="' . $sticker . '">';
    echo '</label>';

    $checked = false;
  }
  echo '</div>';
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