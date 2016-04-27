<?php
// Content (variables)
$image = get_sub_field( 'image_b_image' );
$caption = get_sub_field( 'image_b_caption' );

// Classes
$class_section = 'image';
$class_body = 'section-body';

// Build section
section_start( $class_section );

  // Body
  section_body_start( $class_body );

    /*
     * Body content
     * This is the flexible part, that is different for each element
     */
    echo '<figure>';

      echo '<img src="' . $image['sizes']['large'] . '" width="' . $image['width'] . '" height="' . $image['height'] . '">';

      if( $caption )
        echo '<figcaption>' . $caption . '</figcaption>';

    echo '</figure>';

  section_body_end();

section_end();
?>