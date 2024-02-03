let isLoading = false;
let postId = window.location.search.split('=')[1];

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

function loadMore() {
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
            commentDiv.className = 'comment';
            commentDiv.innerHTML = `
              <p class="user">
                <img src="../app/media/icon-user.png" alt="User" height="20" width="20">
                ${comment.comment_user_name}
              </p>
              <p>${comment.comment_text}</p>
            `;
            commentsList.appendChild(commentDiv);
          });
        }

        if (commentsList && comments.length === 0) {
          commentsList.innerHTML = '<p>No comments yet.</p>';
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

// Go back to feed
const backToFeed = document.querySelector('#back-to-feed');
if (backToFeed) {
  backToFeed.addEventListener('click', function(e) {
    e.preventDefault();

    // Go previous page if possible
    if (window.history.length > 1) {
      window.history.back();
    } else {
      window.location.href = 'index.php';
    }
  });
}

// Handle comment textarea
const commentTextarea = document.querySelector('#comment');
if (commentTextarea) {
  commentTextarea.addEventListener('input', function() {
    const commentForm = document.querySelector('#comment_form button');
    if (!commentForm) return

    if (comment_regex.test(commentTextarea.value)) {
      commentForm.removeAttribute('disabled');
    } else {
      commentForm.setAttribute('disabled', true);
    }
  });
}