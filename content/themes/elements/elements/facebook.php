<?php
$b_embed = get_sub_field( 'facebook_embed' );
$b_content = get_sub_field( 'facebook_content' );
?>

<section class="facebook">
  <div class="fb-page" data-href="<?php echo $b_embed; ?>" data-tabs="<?php echo $b_content; ?>" data-small-header="false" data-adapt-container-width="true" data-hide-cover="false" data-show-facepile="true"><blockquote cite="<?php echo $b_embed; ?>" class="fb-xfbml-parse-ignore"><a href="<?php echo $b_embed; ?>">Basketball2Go</a></blockquote></div>
</section>