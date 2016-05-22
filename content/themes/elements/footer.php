  </main>

  <footer>
    <img src="<?php echo bloginfo( 'template_directory' ); ?>/img/logo-black.svg">

    <nav>
      <ul>
        <?php
        $nav = array(
          'theme_location'  => 'menu_primary',
          'container'       => '',
          'items_wrap'      => '%3$s',
          'depth'           => 1
        );

        wp_nav_menu( $nav );
        ?>

        <li><a>Contact</a></li>
      </ul>
    </nav>

    <small>&copy; 2016 - CAB & CAB Kids</small>
  </footer>

  <!-- Scripts -->
  <?php wp_footer(); ?>
</body>
</html>