<?php
$image = get_sub_field( 'gridSec_b_item_image' );
$title = get_sub_field('gridSec_b_item_title');
$text = wpautop( get_sub_field('gridSec_b_item_text') );
?>

<li>
  <img src="<?php echo $image['sizes']['medium']; ?>" width="<?php echo $image['width']; ?>" height="<?php echo $image['height']; ?>">
  <h2><?php echo $title; ?></h2>
  <?php echo $text; ?>
</li>