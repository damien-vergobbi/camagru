<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== "POST") {
  header('Location: /');
  exit();
}

// echo exec('whoami');
// exit;

if (isset($_FILES["imageData"]) && $_FILES["imageData"]["error"] === UPLOAD_ERR_OK) {
  try {
    $user_id = $_SESSION['user_id'] ?? -1;

    if (!isset($user_id) || $user_id < 0) {
      throw new Exception('User id is required');
    }

    $pdo = require_once '../../config/db.php';

    // Create folder if it doesn't exist
    $uploadDirectory = '../posts/';
    if (!file_exists($uploadDirectory)) {
      mkdir($uploadDirectory, 0777, true);
    }

    $fileName = uniqid('image_') . '.png';

    // Check if image already exists
    while (file_exists($uploadDirectory . $fileName)) {
      $fileName = uniqid('image_') . '.png';
    }

    $tempFilePath = $_FILES["imageData"]["tmp_name"];
    $uploadFilePath = $uploadDirectory . $fileName;

    // Vérifier le type de fichier (optionnel)
    $fileType = strtolower(pathinfo($uploadFilePath, PATHINFO_EXTENSION));
    if ($fileType !== "jpg" && $fileType !== "jpeg" && $fileType !== "png") {
      echo json_encode(['error' => 'Only jpg, jpeg and png files are allowed']);
      exit();
    }
    
    if (move_uploaded_file($tempFilePath, $uploadFilePath)) {
      chmod($uploadFilePath, 0666);
      
      // Insert the post in the database
      $stmt = $pdo->prepare('INSERT INTO posts (post_user_id, post_image) VALUES (:user_id, :image)');
      $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
      $stmt->bindValue(':image', $fileName, PDO::PARAM_STR);
      $stmt->execute();

      // Get the last inserted id
      $lastId = $pdo->lastInsertId();

      echo json_encode([
        'path' => $uploadFilePath,
        'url' => "/post.php?id=$lastId"
      ]);
      exit();
    } else {
      throw new Exception('Failed to save the image');
    }
  } catch (Exception $e) {
    echo json_encode([
      'error' => $e->getMessage()
    ]);
  } catch (Throwable $e) {
    echo json_encode([
      'error' => 'An error occurred'
    ]);
  } catch (Error $e) {
    echo json_encode([
      'error' => 'An error occurred'
    ]);
  } catch (PDOException $e) {
    echo json_encode([
      'error' => 'An error occurred'
    ]);
  }
} else {
  echo json_encode([
    'error' => 'No image provided'
  ]);
}

?>
