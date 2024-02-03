<?php

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
    $list[] = [
        'post_id' => $image['post_id'],
        'post_image' => $image['post_image'],
        'post_user_name' => $image['post_user_name'],
        'like_count' => $image['like_count'],
        'comment_count' => $image['comment_count']
    ];
}

echo json_encode($list);
?>