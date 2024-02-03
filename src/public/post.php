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
</head>
<body>
    <? require_once 'components/navbar.php'; ?>

    <div class="infinite-wrapper">
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

          <div id="comments">
            <h2>Comments</h2>
            <div id="comments_list">
              <p>Loading comments...</p>
            </div>

            <form id="comment_form" action="/scripts/comment.php" method="post">
              <input type="hidden" name="post_id" value="0">
              <textarea name="comment" id="comment" placeholder="Write a comment..."></textarea>
              <button type="submit">Send</button>
            </form>
          </div>
        </div>

      </main>
  
      <div id="loader-wrapper" class="loader-wrapper hidden">
        <div class="loader"></div>
      </div>
    </div>

    <script>
      let isLoading = false;
      let postId = window.location.search.split('=')[1];

      console.log('Post ID:', postId);

      const hideLoader = () => {
        isLoading = false;
        const loader = document.querySelector('#loader-wrapper');
        if (loader) {
          loader.classList.add('hidden');
        }
        const post = document.querySelector('#post');
        if (post) {
          post.classList.remove('hidden');
        }
      };

      const showLoader = () => {
        isLoading = true;
        const loader = document.querySelector('#loader-wrapper');
        if (loader) {
          loader.classList.remove('hidden');
        }
        const post = document.querySelector('#post');
        if (post) {
          post.classList.add('hidden');
        }
      };

      function loadMore($postId) {
        console.log('Chargement en cours');
        if (isLoading) {
          return;
        }

        showLoader();

        const xhr = new XMLHttpRequest();
        xhr.open('GET', `/scripts/post-details.php?id=${postId}`);
        xhr.onload = function() {
          try {
            if (xhr.status !== 200) {
              throw new Error('Erreur lors du chargement');
            }

            console.log('Chargement terminé', xhr.responseText);
            const infos = JSON.parse(xhr.responseText);


            if (!infos || infos.length === 0) {
              hideLoader();
              return;
            }

            if (Array.isArray(infos) && infos['error']) {
              console.error('Erreur lors du chargement :', infos['error']);
              hideLoader();
              return;
            }

            if (infos.post && infos.comments && infos.likes) {
              const post = infos.post;
              const comments = infos.comments;
              const likes = infos.likes;

              const postImg = document.querySelector('#post_img');
              if (postImg) {
                postImg.src = post.post_image;
              }

              const postUsername = document.querySelector('#post_username');
              if (postUsername) {
                postUsername.textContent = post.post_user_name;
              }

              const postLikes = document.querySelector('#post_likes');
              if (postLikes) {
                postLikes.textContent = likes.length;
              }

              const postComments = document.querySelector('#post_comments');
              if (postComments) {
                postComments.textContent = comments.length;
              }

              const commentsList = document.querySelector('#comments_list');
              if (commentsList) {
                commentsList.innerHTML = '';
                comments.forEach(comment => {
                  const commentDiv = document.createElement('div');
                  commentDiv.innerHTML = `
                    <p>${comment.comment_user_name}</p>
                    <p>${comment.comment_content}</p>
                  `;
                  commentsList.appendChild(commentDiv);
                });
              }

              if (comments.length === 0) {
                commentsList.innerHTML = '<p>No comments yet</p>';
              }
            } else {
              console.error('Erreur lors du chargement :', xhr.status);
            }
            hideLoader();
          } catch (e) {
              console.error('Erreur lors du chargement :', e);
              hideLoader();
            }
          };
          xhr.onerror = function() {
            console.error('Erreur réseau lors du chargement.');
            hideLoader();
          };
          xhr.send();
        }

        // Load datas
        loadMore();
    </script>
</body>
</html>
