<?php
/*
Plugin Name: WC Paypal Advanced Gateway
Plugin URI: http://codecanyon.net/item/paypal-advanced-payment-gateway-for-woocommerce/3945577
Description: Extends WooCommerce. Provides a Paypal Advanced gateway for WooCommerce.
Version: 1.2.1
Author: Buif.Dw <support@browsepress.com>
Author URI: http://codecanyon.net/user/browsepress
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Required functions
if ( ! function_exists( 'woothemes_queue_update' ) ) {
	require_once( 'woo-includes/woo-functions.php' );
}

// Check if WooCommerce is active
if ( ! is_woocommerce_active() )
	return;

/**
 * The WC_Paypal_Advanced global object
 * @name $wc_paypal_advanced
 * @global WC_Paypal_Advanced $GLOBALS['wc_paypal_advanced']
 */
$GLOBALS['wc_paypal_advanced'] = new WC_Paypal_Advanced;

class WC_Paypal_Advanced {
	
	/** plugin version number */
	const VERSION = '1.2.1';
	
	/** plugin text domain */
	const TEXT_DOMAIN = 'wc-gateway-paypal-advanced';

	/** @var string class to load as gateway, can be base or add-ons class */
	var $gateway_class_name = 'WC_Gateway_Paypal_Advanced';

	/** @var bool helper for lazy subscriptions active check */
	var $subscriptions_active;

	/** @var bool helper for lazy pre-orders active check */
	var $pre_orders_active;

	/** @var string the plugin path */
	var $plugin_path;

	/** @var string the plugin url */
	var $plugin_url;

	/** @var \WC_Logger instance */
	var $logger;
	
	var $dependencies = array( 'curl', );
	
	/**
	 * Initializes the plugin
	 *
	 * @since 1.2
	 */
	public function __construct() {
		// include required files
		add_action( 'plugins_loaded', array( $this, 'loaded' ) );

		// load translation
		add_action( 'init', array( $this, 'load_translation' ) );
		
		// admin
		if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {

			// dependency check
			add_action( 'admin_notices', array( $this, 'gateway_notices' ) );

			// add a 'Configure' link to the plugin action links
			add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'add_plugin_setup_link' ) );
			
			// run every time
			$this->install();
		}
	}
	
	/**
	 * Include required files
	 *
	 * @since 1.2
	 */
	public function loaded() {
		// base gateway class
		require( 'classes/class-wc-gateway-paypal-advanced.php' );
		
		// load add-ons class if subscriptions and/or pre-orders are active
		if( $settings = get_option( 'woocommerce_paypal_advanced_settings' ) ) {
			
		}

		// add to WC payment methods
		add_filter( 'woocommerce_payment_gateways', array( $this, 'load_gateway' ) );
		
	}
	
	/**
	 * Adds 2Checkout the list of available payment gateways
	 *
	 * @since 1.2
	 * @param array $gateways
	 * @return array $gateways
	 */
	public function load_gateway( $gateways ) {
		
		$gateways[] = $this->gateway_class_name;
		
		return $gateways;
	}

	/**
	 * Handle localization, WPML compatible
	 *
	 * @since 1.2
	 */
	public function load_translation() {
		// localization in the init action for WPML support
		load_plugin_textdomain( self::TEXT_DOMAIN, false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}

	/** Admin methods ******************************************************/

	/**
	 * Checks if required PHP extensions are loaded and SSL is enabled.
	 * Adds an admin notice if either check fails
	 *
	 * @since 1.2
	 */
	public function gateway_notices() {
		
		$errors = array();
		if ( version_compare( PHP_VERSION, '5.2.0', '<' ) ) {
			$errors[] = sprintf( __( "%sPaypal Error%s: This plugin required PHP at least 5.2.0. PHP currently version: %s", self::TEXT_DOMAIN ), '<strong>', '</strong>', PHP_VERSION );
		}
		
		if( ! empty( $errors) ) {
			echo '<div class="error"><p>' . implode( '</p><p>', $errors ) , '</p></div>';
		}
		
		$missing_extensions = $this->get_missing_dependencies();

		if ( count( $missing_extensions ) > 0 ) {

			$message = sprintf(
				_n( 'WooCommerce Paypal Advanced Gateway requires the %s PHP extension to function.  Contact your host or server administrator to configure and install the missing extension.',
					'WooCommerce Paypal Advanced Gateway requires the following PHP extensions to function: %s.  Contact your host or server administrator to configure and install the missing extensions.',
					count( $missing_extensions ), self::TEXT_DOMAIN ),
			  '<strong>' . implode( ', ', $missing_extensions ) . '</strong>'
			);

			echo '<div class="error"><p>' . $message . '</p></div>';
		}
		
		$settings = get_option( 'woocommerce_paypal_advanced_settings' );

	}

	/**
	 * Gets the string name of any required PHP extensions that are not loaded
	 *
	 * @since 1.2
	 * @return array
	 */
	public function get_missing_dependencies() {

		$missing_extensions = array();
		foreach ( $this->dependencies as $ext ) {
			if ( ! extension_loaded( $ext ) )
				$missing_extensions[] = $ext;
		}

		return $missing_extensions;
	}

	/**
	 * Return the plugin action links.  This will only be called if the plugin
	 * is active.
	 *
	 * @since 1.2
	 * @param array $actions associative array of action names to anchor tags
	 * @return array associative array of plugin action links
	 */
	public function add_plugin_setup_link( $actions ) {

		$manage_url = admin_url( 'admin.php' );

		if ( version_compare( WOOCOMMERCE_VERSION, '2.1', '<' ) ) {
			$manage_url = add_query_arg( array( 'page' => 'woocommerce_settings', 'tab' => 'payment_gateways', 'section' => 'WC_Gateway_Paypal_Advanced' ), $manage_url ); // WC 1.6.6
		} else {
			$manage_url = add_query_arg( array( 'page' => 'wc-settings', 'tab' => 'checkout', 'section' => $this->gateway_class_name ), $manage_url ); // WC 2.0+
		}

		// add the link to the front of the actions list
		return ( array_merge( array( 'configure' => sprintf( '<a href="%s">%s</a>', $manage_url, __( 'Configure', self::TEXT_DOMAIN ) ) ), $actions ) );
	}
	
	/** Helper methods ******************************************************/
	
	/**
	 * This will ensure any links output to a page (when viewing via HTTPS) are also served over HTTPS.
	 *
	 * @since 1.2
	 * @return string url
	 */
	public function force_ssl( $url ) {
		
		if ( version_compare( WOOCOMMERCE_VERSION, '2.1', '<' ) ) {
			global $woocommerce;
			// For older than version 2.1
			return $woocommerce->force_ssl( $url );
		}
		
		return WC_HTTPS::force_https_url( $url );
	}

	/**
	 * Gets the absolute plugin path without a trailing slash, e.g.
	 * /path/to/wp-content/plugins/plugin-directory
	 *
	 * @since 1.2
	 * @return string plugin path
	 */
	public function plugins_path( $path='' ) {
		// Besure without the first slash
		$path = ltrim($path, '/');
		
		if( ! empty( $this->plugin_path )) {
			return $this->plugin_path . $path;
		}

		$this->plugin_path = plugin_dir_path( __FILE__ );
		
		return $this->plugin_path . $path;
	}

	/**
	 * Gets the plugin url without a trailing slash
	 *
	 * @since 1.2
	 * 
	 * @param $path: Path to the plugin file of which URL you want to retrieve
	 * @return string the plugin url
	 */
	public function plugins_url( $path='' ) {
		// Besure without the first slash
		$path = ltrim($path, '/');
		
		if( ! empty( $this->plugin_url )) {
			return $this->plugin_url . $path;
		}

		$this->plugin_url = plugins_url( '/', __FILE__ );
		
		return $this->plugin_url . $path;
	}
	
	/**
	 * Log errors / messages to WooCommerce error log (/wp-content/woocommerce/logs/)
	 *
	 * @since 1.2
	 * @param string $message
	 */
	public function log( $message ) {
		
		if ( version_compare( WOOCOMMERCE_VERSION, '2.1', '<' ) ) {
			global $woocommerce;
	
			if ( ! is_object( $this->logger ) )
				$this->logger = $woocommerce->logger();
		} else {
			if ( ! is_object( $this->logger ) )
				$this->logger = new WC_Logger();
		}
		
		$this->logger->add( 'paypal_advanced', $message );
	}
	
	/**
	 * Add message
	 *
	 * @since 1.2
	 * @param string $message
	 */
	public function add_message( $message='', $type='error' ) {
		global $woocommerce;
		
		if ( version_compare( WOOCOMMERCE_VERSION, '2.1', '<' ) ) {
			if( 'error' != $type ) {
				$woocommerce->add_message( $message );
			} else {
				$woocommerce->add_error( $message );
			}
		} else {
			wc_add_notice( $message, $type );
		}
	}

	/** Install ******************************************************/


	/**
	 * Run every time.  Used since the activation hook is not executed when updating a plugin
	 *
	 * @since 1.2
	 */
	private function install() {

		// get current version to check for upgrade
		$installed_version = get_option( 'wc_paypal_advanced_version' );
		
		// upgrade if installed version lower than plugin version
		if ( -1 === version_compare( $installed_version, self::VERSION ) )
			$this->upgrade( $installed_version );
	}


	/**
	 * Perform any version-related changes.
	 *
	 * @since 1.2
	 * @param int $installed_version the currently installed version of the plugin
	 */
	private function upgrade( $installed_version ) {

		// pre-version upgrade
		if ( version_compare( $installed_version, self::VERSION, '<' ) ) {
			global $wpdb;
			
			
			// update from pre-2.1 2Checkout version
			if ( $settings = get_option( 'woocommerce_paypal_advanced_settings' ) ) {
				
				// migrate from old settings
				$settings['trx_type'] 		= $settings['trxtype'];
				$settings['trx_server'] 	= $settings['trxserver'];

				// remove unused settings
				foreach ( array( 'trxtype', 'trxserver' ) as $key ) {

					if ( isset( $settings[ $key ] ) )
						unset( $settings[ $key ] );
				}
				
				// update to new settings
				update_option( 'woocommerce_paypal_advanced_settings', $settings );
			}
		}

		// update the installed version option
		update_option( 'wc_paypal_advanced_version', self::VERSION );
	}
	
	/**
	 * Get page id of woocommerce
	 * @param string $page the page name
	 * @return int page id
	 */
	public function get_page_id( $page='' ) {
		if( ! empty( $page ) ) {
			if ( version_compare( WOOCOMMERCE_VERSION, '2.1', '<' ) ) {
				return woocommerce_get_page_id( $page );
			} else {
				return wc_get_page_id( $page );
			}
		}
	}
	
	/**
	 * Get page link url of woocommerce
	 * @param string $page the page name
	 * @return int page id
	 */
	public function get_page_url( $page='' ) {
		return get_permalink( $this->get_page_id( $page ) );
	}
	
	/**
	 * Safely get and trim data from $_POST
	 *
	 * @since 1.2
	 * @param string $key array key to get from $_POST array
	 * @return string value from $_REQUEST or blank string if $_POST[ $key ] is not set
	 */
	public function get_post( $key ) {

		if ( isset( $_REQUEST[ $key ] ) )
			return trim( $_REQUEST[ $key ] );

		return '';
	}
	
	/**
	 * Safely get and trim data from $_POST
	 *
	 * @since 1.2
	 * @param string $key array key to get from $_POST array
	 * @return string value from $_POST or blank string if $_POST[ $key ] is not set
	 */
	public function set_post( $key, $value='' ) {
		$value = ! empty( $value ) ? $value : '';
		
		$_POST[$key] 	= $value;
		$_REQUEST[$key] = $value;
	}
} // end \WC_Paypal_Advanced
