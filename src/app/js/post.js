let isLoading = false;
let isRefreshing = false;
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

function getDatas() {
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

      if (!infos || infos['error']) {
        console.error('Erreur lors du chargement :', infos['error']);
        hideLoader();

        // Redirect to feed
        if (window.history.length > 1) {
          window.history.back();
        } else {
          window.location.href = 'index.php';
        }
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

        // Check if user liked the post
        const likeButton = document.querySelector('#likes-div');
        const likeImage = document.querySelector('#likes-div img');

        if (infos.liked) {
          if (likeButton) {
            likeButton.classList.add('liked');
            likeImage.src = '../app/media/icon-like-fill.png';
          }
        } else {
          if (likeButton) {
            likeButton.classList.remove('liked');
            likeImage.src = '../app/media/icon-like.png';
          }
        }

        // Show delete button if user is the author
        const deleteButton = document.querySelector('#delete_post');
        if (deleteButton) {
          if (infos.is_author) {
            deleteButton.classList.remove('hidden');
          } else {
            deleteButton.classList.add('hidden');
          }
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
getDatas();

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

function reloadDatas() {
  if (isRefreshing) {
    return;
  }

  isRefreshing = true;

  const xhr = new XMLHttpRequest();
  xhr.open('GET', `/scripts/post-details.php?id=${postId}`);
  xhr.onload = function() {
    isRefreshing = false;

    try {
      if (xhr.status !== 200) {
        throw new Error('Erreur lors du chargement');
      }

      const infos = JSON.parse(xhr.responseText);

      if (!infos || infos['error']) {
        console.error('Erreur lors du chargement :', infos['error']);
        hideLoader();

        // Redirect to feed
        if (window.history.length > 1) {
          window.history.back();
        } else {
          window.location.href = 'index.php';
        }
        return;
      }

      if (infos.post && infos.comments && infos.likes) {
        const post = infos.post;
        const comments = infos.comments;
        const likes = infos.likes;

        const postUsername = document.querySelector('#post_username');
        if (postUsername) {
          // Replace username if different
          if (postUsername.textContent !== post.post_user_name) {
            postUsername.textContent = post.post_user_name;
          }
        }

        const postLikes = document.querySelector('#post_likes');
        if (postLikes) {
          // Replace likes count if different
          if (postLikes.textContent !== likes.length) {
            postLikes.textContent = likes.length;
          }
        }

        const postComments = document.querySelector('#post_comments');
        if (postComments) {
          // Replace comments count if different
          if (postComments.textContent !== comments.length) {
            postComments.textContent = comments.length;
          }
        }

        const commentsList = document.querySelector('#comments_list');
        const commentsLength = document.querySelectorAll('#comments_list .comment').length;
        if (commentsList && (comments.length > commentsLength)) {
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
    } catch (e) {
      console.error('Erreur lors du chargement :', e);
    }
  };
  xhr.send();
}

// Reload datas every 5 seconds
let reload = setInterval(reloadDatas, 5000);

// Clear interval on page unload
window.addEventListener('unload', function() {
  clearInterval(reload);
});

// Handle like button
const likeButton = document.querySelector('#likes-div');

if (likeButton && !likeButton.classList.contains('not_logged')) {
  const likeImage = document.querySelector('#likes-div img');

  likeButton.addEventListener('click', function() {
    // Temp before server response
    if (likeButton.classList.contains('liked')) {
      likeButton.classList.remove('liked');

      // Update image likes count
      const postLikes = document.querySelector('#post_likes');
      if (postLikes) {
        postLikes.textContent = Math.max(0, parseInt(postLikes.textContent) - 1);
      }

      // Update image url
      likeImage.src = '../app/media/icon-like.png';
    } else {
      likeButton.classList.add('liked');
      
      // Update image likes count
      const postLikes = document.querySelector('#post_likes');
      if (postLikes) {
        postLikes.textContent = parseInt(postLikes.textContent) + 1;
      }

      // Update image url
      likeImage.src = '../app/media/icon-like-fill.png';
    }

    const xhr = new XMLHttpRequest();
    xhr.open('POST', '/scripts/post-like.php');
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
      try {
        if (xhr.status !== 200) {
          throw new Error('Erreur lors du chargement');
        }

        const response = JSON.parse(xhr.responseText);

        if (response.error) {
          console.error('Erreur lors du chargement :', response.error);
          return;
        }

        if (response.liked) {
          likeButton.classList.add('liked');
        } else {
          likeButton.classList.remove('liked');
        }
      } catch (e) {
        console.error('Erreur lors du chargement :', e);
      }
    };
    xhr.send(`id=${postId}`);
  });
}

// Handle comment form
const commentForm = document.querySelector('#comment_form');

if (commentForm) {
  commentForm.addEventListener('submit', function(e) {
    e.preventDefault();

    const commentTextarea = document.querySelector('#comment');
    if (!commentTextarea) return;

    const comment = commentTextarea.value;

    if (!comment_regex.test(comment)) {
      return;
    }

    const xhr = new XMLHttpRequest();
    xhr.open('POST', '/scripts/post-comment.php');
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
      try {
        reloadDatas();

        if (xhr.status !== 200) {
          throw new Error('Erreur lors du chargement');
        }

        const response = JSON.parse(xhr.responseText);

        if (response.error) {
          console.error('Erreur lors du chargement :', response.error);
          return;
        }

        // Reset form
        commentTextarea.value = '';
        commentTextarea.dispatchEvent(new Event('input'));

        // Reload datas
        reloadDatas();
      } catch (e) {
        console.error('Erreur lors du chargement :', e);
      }
    };
    xhr.send(`id=${postId}&comment=${comment}`);
  });
}

// Handle delete post
const deleteButton = document.querySelector('#delete_post');
if (deleteButton) {
  deleteButton.addEventListener('click', function() {
    const xhr = new XMLHttpRequest();
    xhr.open('POST', '/scripts/post-delete.php');
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
      try {
        if (xhr.status !== 200) {
          throw new Error('Erreur lors du chargement');
        }

        console.log(xhr.responseText);

        const response = JSON.parse(xhr.responseText);

        if (response.error) {
          console.error('Erreur lors du chargement :', response.error);
          return;
        }

        // Redirect to feed
        if (window.history.length > 1) {
          window.history.back();
        } else {
          window.location.href = 'index.php';
        }
      } catch (e) {
        console.error('Erreur lors du chargement :', e);
      }
    };
    xhr.send(`id=${postId}`);
  });
}