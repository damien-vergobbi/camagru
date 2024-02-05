<button id="goToTop">
    <img src="../../app/media/icon-up.png" alt="Go top" height="40" width="40">
</button>

<script>
    const goTopButton = document.getElementById('goToTop');
    const mainWrapper = document.querySelector('body');

    goTopButton.addEventListener('click', () => {
        mainWrapper.scrollIntoView({ behavior: 'smooth' });
    });

    window.addEventListener('scroll', () => {
        if (window.scrollY > 100) {
            goTopButton.style.display = 'block';
        } else {
            goTopButton.style.display = 'none';
        }
    });
</script>