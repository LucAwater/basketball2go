<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class tamed_settings {
  /**
   * Holds the values to be used in the fields callbacks
   */
  private $options;

  /**
   * Start up
   */
  public function __construct() {
    add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
    add_action( 'admin_init', array( $this, 'page_init' ) );
  }

  /**
   * Add options page
   */
  public function add_plugin_page() {
    // This page will be under "Settings"
    add_options_page(
      'Tamed Admin Theme / settings',
      'Tamed Admin Theme',
      'manage_options',
      'tamed-admin-theme',
      array( $this, 'create_admin_page' )
    );
  }

  /**
   * Options page callback
   */
  public function create_admin_page() {
    include_once('settings-render.php');
  }

  /**
   * Register and add settings
   */
  public function page_init() {
    register_setting('tamed_options', 'tamed_theme');
    register_setting('tamed_options', 'tamed_logo');
    register_setting('tamed_options', 'tamed_bg');
    register_setting('tamed_options', 'tamed_bg_color');
    register_setting('tamed_options', 'tamed_bg_image');
    register_setting('tamed_options', 'tamed_color_1');
    register_setting('tamed_options', 'tamed_color_2');
    register_setting('tamed_options', 'tamed_color_3');
    register_setting('tamed_options', 'tamed_color_4');
    register_setting('tamed_options', 'tamed_footer_content');
    register_setting('tamed_options', 'tamed_menu_order');
    register_setting('tamed_options', 'tamed_menu_removals');

    if( isset($GLOBALS['menu']) ){
      $items = $GLOBALS['menu'];

      foreach( $items as $item ){
        $item_name = preg_replace('/[0-9]+/', '', $item[0]);
        ( (empty($item[0])) ? $item_slug = 'separator' : $item_slug = $item[5] );
        $item_value = get_option('tamed_menu_name_' . $item_slug, $item_name);
        register_setting('tamed_options', 'tamed_menu_name_' . $item_slug);
      }
    }
  }
}
?>
