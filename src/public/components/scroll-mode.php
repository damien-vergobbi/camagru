<?php
// Get current route
$pagename = basename($_SERVER['REQUEST_URI']);
?>

<aside id="scroll-mode" class='<?= isset($_GET['user']) ? "hidden" : "" ?>'>
    <a href="/index.php" class="<? echo $pagename !== "infinite.php" ? 'active' : '' ?>">
        <img src="../../app/media/icon-pages.png" height="30" />
        Pages mode
    </a>

    <a href="/infinite.php" class="<? echo $pagename === "infinite.php" ? 'active' : '' ?>">
        <img src="../../app/media/icon-infinity.png" height="30" />
        Infinite scroll
    </a>
</aside>
