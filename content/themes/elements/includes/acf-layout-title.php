<?php

function my_acf_flexible_content_layout_title( $title, $field, $layout, $i ) {

  // load text sub field
  if( $text = get_sub_field('text_b_title') ) {

    $title = $text;

  }

  // return
  return $title;

}

// name
add_filter('acf/fields/flexible_content/layout_title/key=field_56d6aa8ba4a22', 'my_acf_flexible_content_layout_title', 10, 4);

?>