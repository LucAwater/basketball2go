<?php
/*
 * The template for displaying the header
 *
 * Displays all of the head element and everything up until the main tag.
 */
?>

<!DOCTYPE html>
<!--[if IE 9]>    <html class="no-js lt-ie10" lang="en"> <![endif]-->
<!--[if gt IE 9]><!--> <html <?php language_attributes(); ?>> <!--<![endif]-->
<head>
  <title>YOUR SITE</title>

  <link rel="canonical" href="<?php echo home_url(); ?>">

  <!-- META TAGS -->
  <meta charset="<?php bloginfo( 'charset' ); ?>" />
  <meta name="description" content="">

  <meta property="og:title" content="">
  <meta property="og:site_name" content="">
  <meta property="og:description" content="">
  <meta property="og:image" content="">
  <meta property="og:url" content="">

  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Fonts from Typography.com -->
  <link rel="stylesheet" type="text/css" href="https://cloud.typography.com/6711094/6197352/css/fonts.css" />

  <!-- Stylesheet -->
  <link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/app.css">

  <!-- WP_HEAD() -->
  <?php wp_head(); ?>
</head>

<?php
$hero_elements = array(
  get_field('hero_banner'),
  get_field('hero_b_image'),
  get_field('hero_b_title'),
  get_field('hero_b_text')
);

$hero_elements = array_filter($hero_elements);

( (!empty($hero_elements)) ? $body_class = 'has-hero': $body_class = '');
?>

<body <?php body_class($body_class); ?>>
  <!-- Header -->
  <header>
    <a class="link-logo" href="<?php echo home_url(); ?>">
      <img src="<?php echo bloginfo( 'template_directory' ); ?>/img/logo.svg">
      <img src="<?php echo bloginfo( 'template_directory' ); ?>/img/logo-black.svg">
    </a>

    <nav>
      <ul>
        <?php
        $nav = array(
          'theme_location'  => 'menu_primary',
          'container'       => '',
          'items_wrap'      => '%3$s'
        );

        wp_nav_menu( $nav );
        ?>

        <li><button class="button-sec">Contact</button></li>
      </ul>
    </nav>
  </header>

  <!-- Main content -->
  <main role="main">
