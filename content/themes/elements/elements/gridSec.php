<?php
// Content (variables)
$h_title = get_sub_field( 'gridSec_h_title' );
$h_text = wpautop( get_sub_field( 'gridSec_h_text' ) );
$b_item = get_sub_field( 'gridSec_b_item' );

// Classes
$class_section = 'grid grid-sec';
$class_header = 'section-header';
$class_body = 'section-body';

// Build section
section_start( $class_section );

  // Header
  section_header($class_header, $h_title, $h_text);

  // Body
  section_body_start( $class_body );

    /*
     * Body content
     * This is the flexible part, that is different for each element
     */
    section_grid_start( $class_grid );

      if( have_rows('gridSec_b_item') ):
        while( have_rows('gridSec_b_item') ): the_row();

          include( 'gridSec-content.php' );

        endwhile;
      endif;

    section_grid_end();

  section_body_end();

section_end();
?>
