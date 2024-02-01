<!-- 
  Get const IS_LOGGED from index.php
-->

<nav>
  <div class="nav-wrapper">
    <a href="index.php" class="brand-logo">Camagru</a>
    <ul id="nav-mobile" class="right hide-on-med-and-down">
      <li>
        <a href="editor.php">
          <img src="../../app/media/icon-editor.png" height="30" />
          Editor
        </a>
      </li>
      <li>
        <a href="index.php">
          <img src="../../app/media/icon-gallery.png" height="30" />
          Gallery
        </a>
      </li>
    </ul>

    <?php if (IS_LOGGED): ?>
      <div class="user-settings">
        <a href="settings.php" class="settings">
          <img src="../../app/media/icon-settings.png" height="30" />
          <? echo $_SESSION['user_name'] ?>
        </a>
        <a href="signin.php" class="sign-out">
          Logout
          <img src="../../app/media/icon-log-out.png" height="30" />
        </a>
      </div>
    <?php else: ?>
      <a href="signin.php" class="sign-in">
        Signin
        <img src="../../app/media/icon-log-in.png" height="30" />
      </a>
    <?php endif; ?>
  </div>
</nav>
