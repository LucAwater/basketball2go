<?php
/*
 * This function gets all the custom fields from the backend
 *
 * Important: wpe-acf.json must be imported in order to use this function.
 */
function get_elements(){
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
      elseif( get_row_layout() == 'module' ):
        get_template_part( 'elements/module' );
      elseif( get_row_layout() == 'module' ):
        get_template_part( 'elements/module' );
      elseif( get_row_layout() == 'grid_primary' ):
        get_template_part( 'elements/gridPri' );
      elseif( get_row_layout() == 'slider' ):
        get_template_part( 'elements/slider' );
      endif;

    endwhile;
  endif;
}
?>