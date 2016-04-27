<?php
// Content (variables)
$h_title = get_sub_field( 'text_b_title' );
$b_text = wpautop( get_sub_field( 'text_b_text' ) );

// Classes
$class_section = 'text';
$class_header = 'section-header';
$class_body = 'section-body';

// Build section
section_start( $class_section );

  // Header
  section_header_text( $class_header, $h_title );

  // Body
  section_body_start( $class_body );

    /*
     * Body content
     * This is the flexible part, that is different for each element
     */
    echo $b_text;

  section_body_end();

section_end();
?>

