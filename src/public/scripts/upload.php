<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== "POST") {
  header('Location: /');
  exit();
}

$backgroundWidth = $_POST['backgroundWidth'] ?? null;
$backgroundHeight = $_POST['backgroundHeight'] ?? null;
$backgroundX = $_POST['backgroundX'] ?? null;
$backgroundY = $_POST['backgroundY'] ?? null;
$stickerWidth = $_POST['stickerWidth'] ?? null;
$stickerHeight = $_POST['stickerHeight'] ?? null;
$stickerX = $_POST['stickerX'] ?? null;
$stickerY = $_POST['stickerY'] ?? null;

try {
  if (!isset($backgroundWidth, $backgroundHeight, $backgroundX, $backgroundY, $stickerWidth, $stickerHeight, $stickerX, $stickerY, $_FILES['backgroundData'], $_FILES['stickerData'])) {
    echo json_encode([
      'error' => 'Invalid parameters'
    ]);
    exit;
  }

  $background = [
    'width' => (int) $backgroundWidth,
    'height' => (int) $backgroundHeight,
    'x' => (int) $backgroundX,
    'y' => (int) $backgroundY,
    'file' => $_FILES['backgroundData']
  ];

  $sticker = [
    'width' => (int) $stickerWidth,
    'height' => (int) $stickerHeight,
    'x' => (int) $stickerX,
    'y' => (int) $stickerY,
    'file' => $_FILES['stickerData']
  ];

  $user_id = $_SESSION['user_id'] ?? -1;

  if (!isset($user_id) || $user_id < 0) {
    throw new Exception('User id is required');
  }

  // Check if folder exists
  $uploadDirectory = '../posts/';

  if (!file_exists($uploadDirectory)) {
    throw new Exception('Posts folder does not exist');
  }

  $fileName = uniqid('image_') . '.png';

  // Check if image already exists
  while (file_exists($uploadDirectory . $fileName)) {
    $fileName = uniqid('image_') . '.png';
  }

  // Create the background image
  $backgroundImage = imagecreatefrompng($background['file']['tmp_name']);
  $stickerImage = imagecreatefrompng($sticker['file']['tmp_name']);
  $outputImage = imagecreatetruecolor($background['width'], $background['height']);

  // Copy the background image
  imagecopy($outputImage, $backgroundImage, 0, 0, 0, 0, $background['width'], $background['height']);

  // Copy the sticker image
  imagecopy($outputImage, $stickerImage, $sticker['x'], $sticker['y'], 0, 0, $sticker['width'], $sticker['height']);

  // Save the image
  imagepng($outputImage, $uploadDirectory . $fileName);

  // Change the permissions
  chmod($uploadDirectory . $fileName, 0666);

  // Free up memory
  imagedestroy($backgroundImage);
  imagedestroy($stickerImage);
  imagedestroy($outputImage);

  require_once '../../config/db.php';

  // Insert the post in the database
  $stmt = $pdo->prepare('INSERT INTO posts (post_user_id, post_image) VALUES (:user_id, :image)');
  $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
  $stmt->bindValue(':image', $fileName, PDO::PARAM_STR);
  $stmt->execute();

  // Get the last inserted id
  $lastId = $pdo->lastInsertId();

  echo json_encode([
    'path' => $uploadDirectory . $fileName,
    'url' => "/post.php?id=$lastId"
  ]);

} catch (Exception $e) {
  echo json_encode([
    'error' => $e->getMessage()
  ]);
  exit;
} catch (Throwable $e) {
  echo json_encode([
    'error' => 'An error occurred'
  ]);
  exit;
} catch (Error $e) {
  echo json_encode([
    'error' => 'An error occurred'
  ]);
  exit;
} catch (PDOException $e) {
  echo json_encode([
    'error' => 'An error occurred'
  ]);
  exit;
}

?>