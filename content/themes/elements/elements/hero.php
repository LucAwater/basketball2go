<?php
// Content (variables)
$banner = get_field( 'hero_banner' );
$b_title = get_field( 'hero_b_title' );

// Classes
$class_section = 'hero';
$class_banner = 'hero-banner';
$class_body = 'section-body';
?>

<section class="<?php echo $class_section; ?>">
  <div class="<?php echo $class_banner; ?>"
       style="background-image: url('<?php echo $banner['sizes']['large']; ?>');"
       width="<?php echo $banner['width']; ?>"
       height="<?php echo $banner['height']; ?>">
  </div>

  <?php if( $b_title ): ?>
    <div class="<?php echo $class_body; ?>">
      <h1><?php echo $b_title; ?></h1>
    </div>
  <?php endif; ?>

  <a class="arrow-scroll">Find out more</a>
</section>
