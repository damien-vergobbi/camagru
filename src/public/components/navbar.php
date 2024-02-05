<!-- 
  Get const IS_LOGGED from index.php
-->

<?php
// Get current route
$pagename = basename($_SERVER['REQUEST_URI']);
?>

<nav>
  <div class="nav-wrapper">
    <a href="/index.php" class="brand-logo">Camagru</a>

    <?php if (IS_LOGGED): ?>
      <a href="/signin.php" class="mobile-sign-out sign-out">
        Logout
        <img src="../../app/media/icon-log-out.png" height="30" />
      </a>
    <?php endif; ?>

    <!-- Hamburger button -->
    <a href="#" data-target="mobile-demo" class="sidenav-trigger">
      <i class="material-icons">menu</i>
    </a>

    <div class="sidenav">
      <ul id="nav-mobile" class="right hide-on-med-and-down">
        <li>
          <a href="/editor.php" class="<? echo $pagename === "editor.php" ? 'active' : '' ?>">
            <img src="../../app/media/icon-editor.png" height="30" />
            Editor
          </a>
        </li>
        <li>
          <a href="/index.php" class="<? echo $pagename === "index.php" ? 'active' : '' ?>">
            <img src="../../app/media/icon-gallery.png" height="30" />
            Gallery
          </a>
        </li>
      </ul>

      <?php if (IS_LOGGED): ?>
        <div class="user-settings">
          <a href="/settings.php" class="settings <? echo $pagename === "settings.php" ? 'active' : '' ?>">
            <img src="../../app/media/icon-settings.png" height="30" />
            <? echo $_SESSION['user_name'] ?>
          </a>
          <a href="/signin.php" class="sign-out">
            Logout
            <img src="../../app/media/icon-log-out.png" height="30" />
          </a>
        </div>
      <?php else: ?>
        <a href="/signin.php" class="sign-in">
          Signin
          <img src="../../app/media/icon-log-in.png" height="30" />
        </a>
      <?php endif; ?>
    </div>
  </div>
</nav>


<script>
  // Hide sidenav on load if mobile
  if (window.innerWidth < 992) {
    document.querySelector('.sidenav').style.display = 'none';
  }
  
  // Listen resize event
  window.addEventListener('resize', () => {
    // Get window width
    const width = window.innerWidth;

    // If window width is greater than 992px
    if (width > 992) {
      // Hide mobile menu
      document.querySelector('.sidenav').style.display = 'none';
    }
  });

  // Listen click event on hamburger button
  document.querySelector('.sidenav-trigger').addEventListener('click', () => {
    // Get mobile menu
    const mobileMenu = document.querySelector('.sidenav');

    // If mobile menu is visible
    if (mobileMenu.style.display === 'block') {
      // Hide mobile menu
      mobileMenu.style.display = 'none';
    } else {
      // Show mobile menu
      mobileMenu.style.display = 'block';
    }
  });
</script>