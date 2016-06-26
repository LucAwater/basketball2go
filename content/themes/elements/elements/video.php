<?php
// Content (variables)
$h_title = get_sub_field( 'video_h_title' );
$h_text = get_sub_field( 'video_h_text' );

$b_video = get_sub_field( 'video_b_video' );

// Classes
$class_section = 'video';
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
    echo '<div class="embed-container">';
      echo $b_video;
    echo '</div>';

  section_body_end();

section_end();
?>