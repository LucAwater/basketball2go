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
  <title>Basketball2Go</title>

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

  <!-- Fonts from Typekit -->
  <script src="https://use.typekit.net/ike3vql.js"></script>
  <script>try{Typekit.load({ async: true });}catch(e){}</script>

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
  <div id="fb-root"></div>
  <script>(function(d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) return;
    js = d.createElement(s); js.id = id;
    js.src = "//connect.facebook.net/nl_NL/sdk.js#xfbml=1&version=v2.9";
    fjs.parentNode.insertBefore(js, fjs);
  }(document, 'script', 'facebook-jssdk'));</script>

  <!-- Header -->
  <header>
    <a class="link-logo" href="<?php echo home_url(); ?>">
      <img src="<?php echo bloginfo( 'template_directory' ); ?>/img/logo.svg">
      <img src="<?php echo bloginfo( 'template_directory' ); ?>/img/logo-black.svg">
    </a>

    <a class="nav-open"></a>

    <nav>
      <span class="nav-close"><a></a></span>

      <ul>
        <?php
        $nav = array(
          'theme_location'  => 'menu_primary',
          'container'       => '',
          'items_wrap'      => '%3$s'
        );

        wp_nav_menu( $nav );
        ?>

        <li><a class="button button-sec" href="<?php echo home_url(); ?>/contact">Contact</a></li>
      </ul>
    </nav>
  </header>

  <!-- Main content -->
  <main role="main">
