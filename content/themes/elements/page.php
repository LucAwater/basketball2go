<?php
get_header();

/*
 * Check if we are on a WooCommerce page
 * If so, echo content to enable the shortcode
 */
if( have_posts() ):
  while( have_posts() ): the_post();

    if( is_really_woocommerce_page() ){
      the_content();
    }

  endwhile;
endif;

get_elements();

get_footer();
?>
