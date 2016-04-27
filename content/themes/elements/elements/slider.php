<?php
global $detect;

// Content (variables)
$h_title = get_sub_field( 'slider_h_title' );
$h_text = wpautop( get_sub_field( 'slider_h_text' ) );
$b_images = get_sub_field( 'slider_b_images' );

// Classes
$class_section = 'slider';
$class_header = 'section-header row';
$class_body = 'section-body';

// Build section
section_start( $class_section );

  // Header
  section_header( $class_header, $h_title, $h_text );

  // Body
  section_body_start( $class_body );

    /*
     * Body content
     * This is the flexible part, that is different for each element
     */
    section_slider_images( $b_images );
    section_slider_bullets( $b_images );
    (! $detect->isMobile() ) ? section_slider_controls( $b_images ) : '';

  section_body_end();

section_end();
?>
