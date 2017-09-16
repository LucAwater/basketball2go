<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

get_header();

if ( have_posts() ):

    ?>
    <section class="hero-small">
        <div class="hero-body">
            <h1>STORE</h1>
            <h2>Basketballs & Freestyle Basketballs</h2>
        </div>
    </section>
    <?php

    section_start('grid grid-products');
        section_body_start('section-body');
            section_grid_start('s-grid-1 m-grid-2 l-grid-4 row');

                while ( have_posts() ): the_post();
                    wc_get_template_part( 'content', 'product' );
                endwhile;

            section_grid_end();
        section_body_end();
    section_end();

endif;

get_footer();
?>
