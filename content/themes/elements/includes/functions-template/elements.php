<?php
/*
 * This function gets all the custom fields from the backend
 *
 * Important: wpe-acf.json must be imported in order to use this function.
 */
function get_elements_hero(){

  /*
   * Check for hero fields
   */
  $hero_elements = array(
    get_field('hero_banner'),
    get_field('hero_b_image'),
    get_field('hero_b_title'),
    get_field('hero_b_text')
  );

  $hero_elements = array_filter($hero_elements);

  if (!empty($hero_elements)) {
    get_template_part( 'elements/hero' );
  }

}

function get_elements(){

  /*
   * Start the ACF page elements loop
   */
  if( have_rows('elements') ):
    while( have_rows('elements') ): the_row();

      if( get_row_layout() == 'text' ):
        get_template_part( 'elements/text' );
      elseif( get_row_layout() == 'quote' ):
        get_template_part( 'elements/quote' );
      elseif( get_row_layout() == 'image' ):
        get_template_part( 'elements/image' );
      elseif( get_row_layout() == 'video' ):
        get_template_part( 'elements/video' );
      elseif( get_row_layout() == 'module' ):
        get_template_part( 'elements/module' );
      elseif( get_row_layout() == 'module' ):
        get_template_part( 'elements/module' );
      elseif( get_row_layout() == 'grid_primary' ):
        get_template_part( 'elements/gridPri' );
      elseif( get_row_layout() == 'slider' ):
        get_template_part( 'elements/slider' );
      elseif( get_row_layout() == 'map' ):
        get_template_part( 'elements/map' );
      elseif ( get_row_layout() == 'facebook_embed' ):
        get_template_part( 'elements/facebook' );
      endif;

    endwhile;
  endif;

}
?>