<?php // Redirect to signin.php if the user is not logged in
session_start();

define('IS_LOGGED', isset($_SESSION['user_id'])
    && isset($_SESSION['user_name'])
    && isset($_SESSION['user_email']));

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Camagru</title>

    <link rel="stylesheet" href="../app/css/global.css">
    
    <!-- Load style from ../app/css/signin.css -->
    <link rel="stylesheet" href="../app/css/navbar.css">
    <link rel="stylesheet" href="../app/css/main.css">
    <link rel="stylesheet" href="../app/css/post.css">
</head>
<body>
    <? require_once 'components/navbar.php'; ?>

    <div class="infinite-wrapper">

      <aside id="scroll-mode" class="lefter">
        <a href="index.php" id="back-to-feed">
          <img src="../../app/media/icon-return.png" height="30" />
          Back to feed
        </a>
      </aside>

      <main>

        <div id="post" class="item-div hidden">
          <img id="post_img" src="../app/media/icon-user.png" alt="Logo Camagru">
          <div class="item-footer">
            <div>
              <img src="../app/media/icon-user.png" alt="User" height="20" width="20">
              <p id="post_username">loading</p>
            </div>

            <div class="footer-end">
              <div>
                <img src="../app/media/icon-like.png" alt="Like" height="20">
                <p id="post_likes">0</p>
              </div>
              <div>
                <img src="../app/media/icon-comment.png" alt="Like" height="20">
                <p id="post_comments">0</p>
              </div>
            </div>
          </div>

          <div id="share-div">
            <p>Share this post</p>
            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>" target="_blank">
              <img src="../app/media/logo-facebook.svg" alt="Facebook" height="20">
            </a>

            <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo urlencode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>" target="_blank">
              <img src="../app/media/logo-linkedin.png" alt="Linkedin" height="20">
            </a>

            <a href="mailto:?subject=Camagru Share&body=See this post : <?php echo urlencode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>">
              <img src="../app/media/logo-mails.png" alt="Email" height="20">
            </a>
          </div>

          <?php if (IS_LOGGED): ?>
            <div id="comments">
              <h2>Comments</h2>
              <div id="comments_list">
                <p>Loading comments...</p>
              </div>

              <form id="comment_form" action="/scripts/comment.php" method="post">
                <textarea name="comment" id="comment" placeholder="Write a comment..." minlength="3" maxlength="150"></textarea>
                <button type="submit" disabled>Send</button>
              </form>
            </div>
          <?php else: ?>
            <p class="not-logged">
              <a href="signin.php">Sign in</a>
              to like or comment
            </p>
          <?php endif; ?>
        </div>

      </main>
  
      <div id="loader-wrapper" class="loader-wrapper hidden">
        <div class="loader"></div>
      </div>
    </div>

    <script src="../app/js/utils.js" defer></script>
    <script src="../app/js/post.js" defer></script>
</body>
</html>
