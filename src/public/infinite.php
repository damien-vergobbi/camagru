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
    <title>Camagru - Infinite</title>

    <link rel="stylesheet" href="../app/css/global.css">
    
    <!-- Load style from ../app/css/signin.css -->
    <link rel="stylesheet" href="../app/css/navbar.css">
    <link rel="stylesheet" href="../app/css/main.css">
</head>
<body>
    <? require_once 'components/navbar.php'; ?>

    <div class="infinite-wrapper">
        <main></main>
    
        <div id="loader-wrapper" class="loader-wrapper hidden">
            <div class="loader"></div>
        </div>
    </div>



    <script>
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

        const renderItem = (id, url, username, likes, comments) => {
            const itemDiv = document.createElement('div');
            itemDiv.classList.add('item-div');

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
            likeIcon.src = '../app/media/icon-like.png';
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


            // Create main wrapper 'a'
            const a = document.createElement('a');
            a.href = `/post.php?id=${id}`;
            a.appendChild(itemDiv);

            // Append to main list
            if (main.lastElementChild?.id === 'loader-wrapper') {
                main.insertBefore(a, main.lastElementChild);
            } else {
                main.appendChild(a);
            }
        };


        function loadMore() {
            console.log('Chargement en cours');
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

                    console.log('Chargement terminé', xhr.responseText);
                    const elements = JSON.parse(xhr.responseText);

                    console.log(page, 'Elements chargés :', elements);

                    if (elements === 'end') {
                        page = 1;
                        hideLoader();
                        loadMore();
                        return;
                    }

                    if (Array.isArray(elements)) {
                        elements.forEach(element => {
                            renderItem(element.post_id, element.post_image, element.post_user_name, element.like_count, element.comment_count);
                        });
                        
                        page++;
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

        window.addEventListener('scroll', () => {
            const scrollableHeight = document.documentElement.scrollHeight - window.innerHeight;
            const scrolledDistance = window.scrollY;

            if (scrolledDistance >= scrollableHeight) {
                loadMore();
            }
        });

        // Load the first elements
        loadMore();
    </script>

</body>
</html>
