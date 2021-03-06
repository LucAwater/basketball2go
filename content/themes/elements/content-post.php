<?php
// Content (variables)
$title = get_the_title();
$category = get_the_category( $post->ID );
$category_link = get_category_link( $category[0]->term_id );
$content = wpautop( get_the_content() );
$permalink = get_the_permalink();
$date = get_the_date();
$thumb = get_the_post_thumbnail( $post->ID, 'medium' );
?>

<li>
  <div>
    <a class="post-title" href="<?php echo $permalink; ?>"><h3><?php echo $title; ?></h3></a>

    <?php echo $thumb; ?>
  </div>
</li>