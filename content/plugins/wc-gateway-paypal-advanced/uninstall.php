<?php
/**
 * Paypal Advanced Payment Gateway for Woocommerce
 * By Buif.Dw <support@browsepress.com>
 * 
 * Uninstall - removes all Paypal Advanced options from DB when user deletes the plugin via WordPress backend.
 * @since 1.0.0
 **/
 
if ( !defined('WP_UNINSTALL_PLUGIN') ) {
    exit();
}

delete_option( 'woocommerce_paypal_advanced_settings' );		
