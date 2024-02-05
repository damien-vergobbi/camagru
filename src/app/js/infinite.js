// Supprimer div.pagination
const pagination = document.querySelector('.pagination');
if (pagination) {
    pagination.remove();
}

// Ajouter le chargement infini
let page = 1;
let isLoading = false;
const main = document.querySelector('main');

const hideLoader = () => {
    isLoading = false;
    const loader = document.querySelector('#loader-wrapper');
    if (loader) {
        loader.classList.add('hidden');
    }
};

const showLoader = () => {
    isLoading = true;
    const loader = document.querySelector('#loader-wrapper');
    if (loader) {
        loader.classList.remove('hidden');
    }
};

const renderItem = (id, url, username, likes, comments, liked) => {
    const itemDiv = document.createElement('a');
    itemDiv.href = `/post.php?id=${id}`;
    itemDiv.classList.add('item-div');
    itemDiv.id = `post-${id}`;
    itemDiv.title = `Click to see this post from ${username}`;

    const img = document.createElement('img');
    img.src = url;
    img.alt = 'Logo Camagru';
    itemDiv.appendChild(img);

    const itemFooter = document.createElement('div');
    itemFooter.classList.add('item-footer');
    itemDiv.appendChild(itemFooter);

    const userDiv = document.createElement('div');
    itemFooter.appendChild(userDiv);

    const userIcon = document.createElement('img');
    userIcon.src = '../app/media/icon-user.png';
    userIcon.alt = 'User';
    userIcon.height = 20;
    userIcon.width = 20;
    userDiv.appendChild(userIcon);

    const userP = document.createElement('p');
    userP.textContent = username;
    userDiv.appendChild(userP);

    const footerEnd = document.createElement('div');
    footerEnd.classList.add('footer-end');
    itemFooter.appendChild(footerEnd);

    const likeDiv = document.createElement('div');
    footerEnd.appendChild(likeDiv);

    const likeIcon = document.createElement('img');
    likeIcon.src = liked ?'../app/media/icon-like-fill.png' : '../app/media/icon-like.png';
    likeIcon.alt = 'Like';
    likeIcon.height = 20;
    likeDiv.appendChild(likeIcon);

    const likeP = document.createElement('p');
    likeP.textContent = likes;
    likeDiv.appendChild(likeP);

    const commentDiv = document.createElement('div');
    footerEnd.appendChild(commentDiv);

    const commentIcon = document.createElement('img');
    commentIcon.src = '../app/media/icon-comment.png';
    commentIcon.alt = 'Comment';
    commentIcon.height = 20;
    commentDiv.appendChild(commentIcon);

    const commentP = document.createElement('p');
    commentP.textContent = comments;
    commentDiv.appendChild(commentP);

    // Append to main list
    if (main.lastElementChild?.id === 'loader-wrapper') {
        main.insertBefore(itemDiv, main.lastElementChild);
    } else {
        main.appendChild(itemDiv);
    }
};


function loadMore() {
    if (isLoading) {
        return;
    }

    showLoader();

    const xhr = new XMLHttpRequest();
    xhr.open('GET', `/scripts/infinite.php?page=${page}`);
    xhr.onload = function() {
        try {
            if (xhr.status !== 200) {
                throw new Error('Erreur lors du chargement');
            }

            const elements = JSON.parse(xhr.responseText);
            const current = document.querySelectorAll('.item-div').length;

            if (elements.length === 0 && current === 0) {
                const noPost = document.createElement('p');
                noPost.id = 'no-post';
                noPost.textContent = 'No post found';
                main.appendChild(noPost);
                hideLoader();
                return;
            }

            if (current > 0 && elements.length === 0) {
                page = 1;
                hideLoader();
                loadMore();
                return;
            }

            if (Array.isArray(elements)) {
                elements.forEach(element => {
                    renderItem(element.post_id, element.post_image, element.post_user_name, element.like_count, element.comment_count, element.liked);
                });
                
                page++;
            }
            hideLoader();
        } catch (e) {
            hideLoader();
        }
    };
    xhr.onerror = function() {
        hideLoader();
    };
    xhr.send();
}

window.addEventListener('scroll', () => {
    const scrollableHeight = document.documentElement.scrollHeight - window.innerHeight;
    const scrolledDistance = window.scrollY;

    if (scrolledDistance >= scrollableHeight) {
        loadMore();
    }
});

// Load the first elements
loadMore();

// Load while main height is less than window height
const interval = setInterval(() => {
    const noPost = document.querySelector('#no-post');
    if (noPost) {
        clearInterval(interval);
        return;
    }

    if (document.documentElement.scrollHeight <= window.innerHeight) {
        loadMore();
    } else {
        clearInterval(interval);
    }
}, 300);

// Clear interval 
window.addEventListener('beforeunload', () => {
    clearInterval(interval);
});
