<?php
// Content (variables)
$h_title = get_sub_field( 'gridPri_h_title' );
$h_text = wpautop( get_sub_field( 'gridPri_h_text' ) );
$b_images = get_sub_field( 'gridPri_b_images' );

// Classes
$class_section = 'grid grid-pri';
$class_header = 'section-header';
$class_body = 'section-body';
$class_grid = 's-grid-1 m-grid-2 l-grid-4 row';

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

      foreach( $b_images as $image ):

        include( 'gridPri-content.php' );

      endforeach;

    section_grid_end();

  section_body_end();

section_end();
?>