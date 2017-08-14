<?php
/*
 * Paypal Advanced Payment Gateway for WooCoomerce
 * 
 * @package    Woocoomerce Payment Gateway
 * @subpackage WC Paypal Advanced
 * 
 * @since: 1.0.0
 * 
 */

class WC_Gateway_Paypal_Advanced extends WC_Payment_Gateway {
	
	var $_payflow_url = 'https://payflowlink.paypal.com';
	
	var $_test_url = 'https://pilot-payflowpro.paypal.com';
	var $_live_url = 'https://payflowpro.paypal.com';
	
	/**
	 * notify url
	 */
	var $notify_url;
	
	public function __construct() { 
		global $woocommerce;
		
        $this->id			= 'paypal_advanced';
        $this->has_fields 	= false;
		$this->method_title = __("Paypal Advanced", WC_Paypal_Advanced::TEXT_DOMAIN );
		
		// Load the form fields
		$this->init_form_fields();
		
		// Load the settings.
		$this->init_settings();

		// Define user set variables
		foreach ( $this->settings as $setting_key => $setting ) {
			$this->$setting_key = $setting;
		}
		
		$this->notify_url = home_url('/');
		
		// Hooks
		if($this->enabled == 'yes') {
			add_action( 'init', array(&$this, 'response_handler') );
			add_action('woocommerce_receipt_paypal_advanced', array(&$this, 'receipt_page'));
		}
		
		if ( version_compare( WOOCOMMERCE_VERSION, '2.0.0', '<' ) ) {
			add_action('woocommerce_update_options_payment_gateways', array(&$this, 'process_admin_options'));
		} else {
			add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
			
			add_action( 'woocommerce_api_wc_gateway_paypal_advanced', array( $this, 'response_handler' ) );
			$this->notify_url   = add_query_arg( 'wc-api', 'WC_Gateway_Paypal_Advanced', $this->notify_url );
		}
    } 


	/**
     * Initialize Gateway Settings Form Fields
     */
    function init_form_fields() {
    
    	$this->form_fields = array(
			'enabled' => array(
				'title' => __( 'Enable/Disable', WC_Paypal_Advanced::TEXT_DOMAIN ), 
				'label' => __( 'Enable Paypal Advanced Payment Gateway', WC_Paypal_Advanced::TEXT_DOMAIN ), 
				'type' => 'checkbox', 
				'description' => '', 
				'default' => 'no'
			), 
			'title' => array(
				'title' => __( 'Title' ), 
				'type' => 'text', 
				'description' => __( 'This controls the title which the user sees during checkout.', WC_Paypal_Advanced::TEXT_DOMAIN ), 
				'default' => __( 'Paypal Advanced', WC_Paypal_Advanced::TEXT_DOMAIN ),
				'css' => "width: 300px;"
			), 
			'description' => array(
				'title' => __( 'Description', WC_Paypal_Advanced::TEXT_DOMAIN ), 
				'type' => 'textarea', 
				'description' => __( 'This controls the description which the user sees during checkout.', WC_Paypal_Advanced::TEXT_DOMAIN ), 
				'default' => 'Pay via Paypal Advanced.'
			),
			'debug' => array(
				'title' => __( 'Debug', WC_Paypal_Advanced::TEXT_DOMAIN ), 
				'type' => 'checkbox', 
				'label' => __( 'Enable logging (<code>woocommerce/logs/paypal_advanced.txt</code>)', WC_Paypal_Advanced::TEXT_DOMAIN ), 
				'default' => 'no'
			),
			'vendor' => array(
				'title' => __( 'Vendor', WC_Paypal_Advanced::TEXT_DOMAIN ), 
				'type' => 'text', 
				'description' => __( 'Your merchant login ID that you created when you registered for the account.', WC_Paypal_Advanced::TEXT_DOMAIN ), 
				'default' => ''
			),
			'partner' => array(
				'title' => __( 'Partner', WC_Paypal_Advanced::TEXT_DOMAIN ), 
				'type' => 'text', 
				'description' => __( 'The ID provided to you by the authorized PayPal Reseller who registered you for the Gateway gateway. If you purchased your account directly from PayPal, use PayPal.', WC_Paypal_Advanced::TEXT_DOMAIN ), 
				'default' => ''
			), 
			'user' => array(
				'title' => __( 'User', WC_Paypal_Advanced::TEXT_DOMAIN ),
				'type' => 'text', 
				'description' => __( 'The ID of the user authorized to process transactions. If, however, you have not set up additional users on the account, USER has the same value as VENDOR.', WC_Paypal_Advanced::TEXT_DOMAIN ), 
				'default' => ''
			),
			'password' => array(
				'title' => __( 'Password', WC_Paypal_Advanced::TEXT_DOMAIN ), 
				'type' => 'text', 
				'description' => __( 'The password that you defined while registering for the account.', WC_Paypal_Advanced::TEXT_DOMAIN ),
				'default' => '', 
			),
			'template' => array(
				'title' => __( 'Layout templates', WC_Paypal_Advanced::TEXT_DOMAIN ), 
				'type' => 'select', 
				'description' => __( 'Determines whether to use one of the two redirect templates (Layout A or B) or the embedded template (Layout C).', WC_Paypal_Advanced::TEXT_DOMAIN ), 
				'options' => array(
					'MINLAYOUT'	=>'General',
					'TEMPLATEA'	=>'Layout A',
					'TEMPLATEB'	=>'Layout B',
				),
				'default' => 'MINLAYOUT',
			),
			'trx_type' => array(
				'title' => __( 'The type of the transaction', WC_Paypal_Advanced::TEXT_DOMAIN ), 
				'type' => 'select', 
				'description' => __( 'The processing method to use for each transaction.', WC_Paypal_Advanced::TEXT_DOMAIN ), 
				'options' => array(
					'S'=>'Sale',
					'A'=>'Authorization'
				),
				'default' => 'S',
			),
			'trx_server' => array(
				'title' => __( 'Transaction Server', WC_Paypal_Advanced::TEXT_DOMAIN ), 
				'type' => 'select', 
				'description' => __( 'Use the live or testing (sandbox) gateway server to process transactions?', WC_Paypal_Advanced::TEXT_DOMAIN ), 
				'options' => array(
					'live'=>'Live',
					'sandbox'=>'Sandbox'
				),
				'default' => 'live'
			),
		);
    }
	
	/**
	 * Get image icon which display on shopping cart or checkout page
	 *
	 * @since 1.2
	 */
	public function get_icon() {
		global $wc_paypal_advanced;

		// use icon provided by filter
		$icon = '<img src="' . esc_url( $wc_paypal_advanced->force_ssl( $wc_paypal_advanced->plugins_url( 'assets/images/paypal-advanced.png' ) ) ) . '" style="width: auto;" alt="' . esc_attr( $this->title ) . '" />';

		return apply_filters( 'woocommerce_paypal_advanced_icon', $icon, $this->id );
	}
	
	/**
	 * Admin Panel Options 
	 * - Options for bits like 'title' and availability on a country-by-country basis
	 **/
	public function admin_options() {
?>
		<h3><?php _e('Paypal Advanced', WC_Paypal_Advanced::TEXT_DOMAIN ); ?></h3>
    	<p><?php _e('Paypal Advanced works by sending the user to Paypal to enter their payment information.', WC_Paypal_Advanced::TEXT_DOMAIN ); ?></p>
    	
    	<table class="form-table">
    		<?php $this->generate_settings_html(); ?>
		</table><!--/.form-table-->    	
<?php
    }
	
    /**
	 * There are no payment fields for paypal_advanced, but we want to show the description if set.
	 **/
	function payment_fields() {
?>
		<?php if ($this->trx_server == 'sandbox') : ?><p><?php _e('TEST MODE/SANDBOX ENABLED', WC_Paypal_Advanced::TEXT_DOMAIN ); ?></p><?php endif; ?>
		<?php if ($this->description) : ?><p><?php echo wpautop(wptexturize($this->description)); ?></p><?php endif; ?>
<?php
	}
	
	/**
	 * Get args for passing
	 **/
	function get_params( $order) {
		
		$return 	= add_query_arg( '_hdl_ppa', 'return', $this->notify_url );
		$error 		= add_query_arg( '_hdl_ppa', 'error', $this->notify_url );
		$silent 	= add_query_arg( '_hdl_ppa', 'silent', $this->notify_url );
		// $cancel		= $order->get_cancel_order_url();
		$cancel 	= add_query_arg( '_hdl_ppa', 'cancel', $this->notify_url );
		
		// Create request
		$params = array (
			'CUSTIP'			=> $this->get_user_ip(),
			'TEMPLATE'			=> $this->template,
			
			'TRXTYPE' 			=> $this->trx_type,
			'VERBOSITY'			=> 'HIGH',
			'TENDER'			=> 'C', //credit card
			'CREATESECURETOKEN'	=> 'Y',
			// 'SECURETOKENID'	=> uniqid('ppatokenid-'),
			'SECURETOKENID'		=> uniqid(substr($_SERVER['HTTP_HOST'], 0, 9), true),
			
			// get back action
			'URLMETHOD'		=> 'POST',
            'RETURNURL'		=> $return,
			'ERRORURL'		=> $error,
			'SILENTPOSTURL'	=> $silent,
			'CANCELURL'		=> $cancel,
			
			// order info
			'AMT' 				=> $order->get_total(),
			'INVNUM' 			=> $order->order_key,
			'PONUM'				=> $order->id,
			'CURRENCY'			=> get_woocommerce_currency(),
            
			'EMAIL'				=> $order->billing_email,
			
			// Billing
			'BILLTOFIRSTNAME' 	=> $order->billing_first_name,
			'BILLTOLASTNAME' 	=> $order->billing_last_name,
			'BILLTOSTREET' 		=> $order->billing_address_1,
			'BILLTOSTREET2'		=> $order->billing_address_2,
			'BILLTOCITY'  		=> $order->billing_city,
			'BILLTOSTATE'  		=> $order->billing_state,
			'BILLTOZIP'			=> $order->billing_postcode,
			'BILLTOCOUNTRY'  	=> $order->billing_country,
			'BILLTOEMAIL'  		=> $order->billing_email,
			'BILLTOPHONENUM' 	=> $order->billing_phone,
			
			// Shipping
			'SHIPTOFIRSTNAME' 	=> $order->shipping_first_name,
			'SHIPTOLASTNAME' 	=> $order->shipping_last_name,
			'SHIPTOSTREET' 		=> $order->shipping_address_1,
			'SHIPTOSTREET2'		=> $order->shipping_address_2,
			'SHIPTOCITY'  		=> $order->shipping_city,
			'SHIPTOSTATE'  		=> ! empty( $order->shipping_state ) ? $order->shipping_state : $order->shipping_city,
			'SHIPTOZIP' 		=> $order->shipping_postcode,
			'SHIPTOCOUNTRY' 	=> $order->shipping_country,
		);
		
		// Cart content
		// If prices include tax or have order discounts, send the whole order as a single item
		if ( get_option( 'woocommerce_prices_include_tax' ) == 'yes' || $order->get_order_discount() > 0 
			|| ( sizeof( $order->get_items() ) + sizeof( $order->get_fees() ) ) >= 9 ) {

			// Discount
			$params['DISCOUNT'] = $order->get_order_discount();

			// Don't pass items - paypal borks tax due to prices including tax. PayPal has no option for tax inclusive pricing sadly. Pass 1 item for the order items overall
			$item_names = array();

			if ( sizeof( $order->get_items() ) > 0 ) {
				foreach ( $order->get_items() as $item ) {
					if ( $item['qty'] ) {
						$item_names[] = $item['name'] . ' x ' . $item['qty'];
					}
				}
			}
			$items_str = $this->get_item_name( sprintf( __( 'Order %s' , 'woocommerce'), $order->get_order_number() ) . " - " . implode( ', ', $item_names ) );
			
			$params['L_NAME1'] 		= $items_str;
			$params['L_QTY1'] 		= 1;
			$params['L_COST1'] 		= number_format( $order->get_total() - round( $order->get_total_shipping() + $order->get_shipping_tax(), 2 ) + $order->get_order_discount(), 2, '.', '' );

			// Shipping Cost
			// No longer using shipping_1 because
			//		a) paypal ignore it if *any* shipping rules are within paypal
			//		b) paypal ignore anything over 5 digits, so 999.99 is the max
			if ( ( $order->get_total_shipping() + $order->get_shipping_tax() ) > 0 ) {
				$params['L_NAME2'] = $this->get_item_name( __( 'Shipping via', 'woocommerce' ) . ' ' . ucwords( $order->get_shipping_method() ) );
				$params['L_QTY2'] 	= '1';
				$params['L_COST2'] 	= number_format( $order->get_total_shipping() + $order->get_shipping_tax(), 2, '.', '' );
			}

		} else {

			// Tax
			$params['TAXAMT'] = $order->get_total_tax();

			// Cart Contents
			$item_loop = 0;
			if ( sizeof( $order->get_items() ) > 0 ) {
				foreach ( $order->get_items() as $item ) {
					if ( $item['qty'] ) {

						$item_loop++;

						$product = $order->get_product_from_item( $item );

						$item_name 	= $item['name'];

						$item_meta = new WC_Order_Item_Meta( $item['item_meta'] );
						if ( $meta = $item_meta->display( true, true ) ) {
							$item_name .= ' ( ' . $meta . ' )';
						}

						$params[ 'L_NAME' . $item_loop ] 	= $this->get_item_name( $item_name );
						$params[ 'L_QTY' . $item_loop ] 	= $item['qty'];
						$params[ 'L_COST' . $item_loop ] 	= $order->get_item_subtotal( $item, false );

						if ( $product->get_sku() ) {
							$params[ 'L_COMMCODE' . $item_loop ] = $product->get_sku();
						}
					}
				}
			}

			// Discount
			if ( $order->get_cart_discount() > 0 ) {
				$params['DISCOUNT'] = round( $order->get_cart_discount(), 2 );
			}

			// Fees
			if ( sizeof( $order->get_fees() ) > 0 ) {
				foreach ( $order->get_fees() as $item ) {
					$item_loop++;

					$params[ 'L_NAME' . $item_loop ] 	= $this->get_item_name( $item['name'] );
					$params[ 'L_QTY' . $item_loop ] 	= 1;
					$params[ 'L_COST' . $item_loop ] 	= $item['line_total'];
				}
			}

			// Shipping Cost item - paypal only allows shipping per item, we want to send shipping for the order
			if ( $order->get_total_shipping() > 0 ) {
				$item_loop++;
				$params[ 'L_NAME' . $item_loop ] 	= $this->get_item_name( sprintf( __( 'Shipping via %s', 'woocommerce' ), $order->get_shipping_method() ) );
				$params[ 'L_QTY' . $item_loop ] 	= '1';
				$params[ 'L_COST' . $item_loop ] 	= number_format( $order->get_total_shipping(), 2, '.', '' );
			}

		}
		
		return apply_filters( 'woocommerce_paypal_advanced_args', $params, $order->id );
	}
	/**
	 * Process the payment and return the result
	 **/
	function process_payment( $order_id ) {
		global $woocommerce;

		$order = new WC_Order( $order_id );
				
		// Return thank you redirect
		return array(
			'result' 	=> 'success',
			'redirect'	=> $this->get_checkout_payment_url( $order )
		);
	
	}
	
	/**
	 * Validate payment form fields
	**/
	public function validate_fields() {
		return true;
	}
	
	/**
	 * receipt_page
	 **/
	function receipt_page( $order_id ) {
		echo '<p>'.__( 'Thank you for your order.', WC_Paypal_Advanced::TEXT_DOMAIN ).'</p>';
		
		if( 'sandbox' == $this->trx_server ) {
			echo '<p>' . sprintf( __( 'Card number for test Visa: %s or MasterCard: %s', WC_Paypal_Advanced::TEXT_DOMAIN ), '4111111111111111', '5555555555554444' ) . '</p>';
		}
		$order = new WC_Order( $order_id );
		
		// $request = new paypal_advanced_request($this->trx_server);
		$result = $this->get_secure_token( $order );
		
		if( ! empty( $result ) && $result['RESULT'] == 0 ):
			
			// store token
			update_post_meta( $order_id, '_paypal_advanced_securetoken', $result['SECURETOKEN'] );
			
			// if it is layout C, otherwise redirect to paypal site
			if ( $this->template == 'MINLAYOUT' || $this->template == 'C') {
				$location = add_query_arg('SECURETOKEN', $result['SECURETOKEN'], $this->_payflow_url );
				$location = add_query_arg('SECURETOKENID', $result['SECURETOKENID'], $location);
				$location = add_query_arg('MODE', $this->get_mode(), $location);				?>
				<iframe id="paypal-advanced-iframe" src="<?php echo $location;?>" width="100%" height="600px" scrolling="no" frameborder="0" border="0" allowtransparency="true"></iframe>
				<?php
			} else {
				
				wc_enqueue_js( '
					$.blockUI({
							message: "' . esc_js( __( 'Thank you for your order. We are now redirecting you to PayPal to make payment.', 'woocommerce' ) ) . '",
							baseZ: 99999,
							overlayCSS:
							{
								background: "#fff",
								opacity: 0.6
							},
							css: {
								padding:        "20px",
								zindex:         "9999999",
								textAlign:      "center",
								color:          "#555",
								border:         "3px solid #aaa",
								backgroundColor:"#fff",
								cursor:         "wait",
								lineHeight:		"24px",
							}
						});
					jQuery("#submit_paypal_advanced_payment_form").click();
				' );
				?>
				<form action="<?php echo $this->_payflow_url ?>" method="get" id="paypal_advanced_payment_form" target="_top">
					<input type="hidden" name="MODE" value="<?php echo $this->get_mode() ?>" />
					<input type="hidden" name="SECURETOKEN" value="<?php echo $result['SECURETOKEN'] ?>" />
					<input type="hidden" name="SECURETOKENID" value="<?php echo $result['SECURETOKENID'] ?>" />
					<input type="submit" id="submit_paypal_advanced_payment_form" class="button" value="<?php _e( 'Pay via PayPal', 'woocommerce' ) ?>" />
				</form>
				<?php
				
			}
		else:
		?>
			<div class="woocommerce-error woocommerce_error">
				<p><?php printf( __( 'Get token error %s', WC_Paypal_Advanced::TEXT_DOMAIN ), $result['RESPMSG'] ) ?></p>
			</div>
		<?php
		endif;
	}
	
	/**
	 * Check response data
	 */
	public function response_handler() {
		global $woocommerce, $wc_paypal_advanced;
		
		if ( isset( $_GET['_hdl_ppa'] ) ) {
			$hdl = $_GET['_hdl_ppa']; // handle value
			
			$this->add_log( 'Result response: ' . print_r( $_REQUEST, true ));
			
			$location = $wc_paypal_advanced->get_page_url( 'checkout' );
			
			$order = ''; $order_id = 0; $order_key = '';
			if( isset( $_REQUEST['INVOICE'] ) && isset( $_REQUEST['PONUM'] ) ) {
				$order_id = $_REQUEST['PONUM'];
				$order_key = $_REQUEST['INVOICE'];
				$order = new WC_Order( $order_id );
			}
			
			if( 'return' == $hdl || 'silent' == $hdl ) {
				@ob_clean();
				
				$message = '';
				
				if ( isset( $_POST['RESULT'] ) && $_POST['RESULT'] == 0 ) {
					
					if( $order->order_key != $order_key ) {
						$this->add_log( 'Error: Order Key does not match invoice.' );
						
						$message = __( 'Error: Order Key does not match invoice.', WC_Paypal_Advanced::TEXT_DOMAIN );
						
					} elseif ($order->status == 'completed') { // Check order not already completed
	            		 $this->add_log( 'Aborting, Order #' . $order_id . ' is already complete.' );
						 
						 $message = sprintf(__( 'Aborting, Order #%s is already complete.', WC_Paypal_Advanced::TEXT_DOMAIN ), $order_id);
						 
	            	} elseif ( $order->get_total() != $_POST['AMT'] ) { // Validate Amount
				    	$this->add_log( 'Payment error: Amounts do not match (gross ' . $_POST['AMT'] . ')' );
				    
				    	// Put this order on-hold for manual checking
				    	$order->update_status( 'on-hold', sprintf( __( 'Validation error: PayPal amounts do not match (gross %s).', WC_Paypal_Advanced::TEXT_DOMAIN ), $_POST['AMT'] ) );
						
						$message = sprintf( __( 'Validation error: PayPal amounts do not match (gross %s).', WC_Paypal_Advanced::TEXT_DOMAIN ), $_POST['AMT'] );
				    } else {
				    	
						$params = array(
							'ORIGID'		=> $_POST['PNREF'],
							'TENDER'		=> 'C',
							'TRXTYPE'		=> 'I',
						);
						
						/* Using Curl post necessary information to the Paypal Site to generate the secured token */
						$response = $this->remote_post( $params );
						
						if ( is_wp_error($response) )
							throw new Exception(__('There was a problem connecting to the payment gateway.', WC_Paypal_Advanced::TEXT_DOMAIN ));
			
						if ( empty($response['body']) )
							throw new Exception( __('Empty response.', WC_Paypal_Advanced::TEXT_DOMAIN ) );
			
			
						/* Parse and assign to array */
						$result = array(); //stores the response in array format
						parse_str( $response['body'], $result );

						if( $result['RESULT'] == 0 ) {

					    	// Store PP Details
			                if ( ! empty( $result['PNREF'] ) )
			                	update_post_meta( $order_id, '_paypal_advanced_pnref', $result['PNREF'] );
							
							if ( ! empty( $result['ORIGPNREF'] ) )
			                	update_post_meta( $order_id, '_paypal_advanced_origpnref', $result['ORIGPNREF'] );
							
							if ( ! empty( $result['AUTHCODE'] ) )
			                	update_post_meta( $order_id, '_paypal_advanced_authcode', $result['AUTHCODE'] );
							
			            	// Payment completed
			                $order->add_order_note( __( 'Payment completed', WC_Paypal_Advanced::TEXT_DOMAIN ) );
			                $order->payment_complete();
		
			                $this->add_log( 'Payment complete.' );
							
							$location = $this->get_checkout_order_received_url( $order );
							
						}
				    }
					
					if(! empty( $message ) ) {
						$this->add_message( $message );
					} 

				} else {
						
					$message = isset($_POST['RESULT']) ? 'Handler ERROR-'.$_POST['RESULT']. ': '. $_POST['RESPMSG'] : __( 'Unknow error', WC_Paypal_Advanced::TEXT_DOMAIN );
					
					$this->add_log( $message );
					
					$this->add_message( $message );
				}

				$this->client_redirect( $location );
				
			} else if( 'cancel' == $hdl ) {
				
				$this->add_message( __( 'Cancel order &amp; restore cart', WC_Paypal_Advanced::TEXT_DOMAIN ), 'success' );
				wp_redirect( $order->get_cancel_order_url() );
				
			} else { // if error
			
				$message = isset($_GET['msg']) ? $_GET['msg'] : '';
				
				if(empty($message)) {
					$message = isset($_POST['RESPMSG']) ? $_POST['RESPMSG'] : __( 'Unknow error', WC_Paypal_Advanced::TEXT_DOMAIN );
				}
				$woocommerce->add_error($message);
				
				$this->client_redirect( $location );
			}
		}
		
		exit(1);
	}
	
	/**
	 * Client redirect
	 * 
	 * @param $url: Redirect url
	 * 
	 */
	function client_redirect($url=''){
		$url = empty($url) ? 'window.top.location.href' : $url;
?>
		<script type="text/javascript">
		if (window!=top) {top.location.replace(document.location);}
		window.top.location.href = '<?php echo $url; ?>';
		</script>
<?php		
	}
	
	/**
	 * Get token
	 */
	protected function get_secure_token( $order ){
		global $woocommerce;
		
		$params = $this->get_params( $order );
		
		// Handle exceptions using try/catch blocks for the request to get secure tocken from paypal
		try {
			/* Using Curl post necessary information to the Paypal Site to generate the secured token */
			$response = $this->remote_post( $params );
			
			// check to see if the request was valid
			if ( ! is_wp_error( $response ) && $response['response']['code'] >= 200 
				&& $response['response']['code'] < 300 ) {
					
				$this->add_log( 'Received valid response from PayPal' );
				
				parse_str( $response['body'], $result );
				
				// Handle response
				if ( $result['RESULT'] > 0 ) {
					// raise exception
					throw new Exception( __( 'There was an error processing your order - ' . $result['RESPMSG'], WC_Paypal_Advanced::TEXT_DOMAIN ) );
					
				} else {//return the secure token
				
					$this->add_log( 'Result: '. print_r( $result, true )  );
					
					return $result;
				}
			}
			
			$this->add_log( 'Received invalid response from PayPal' );
			
			if ( empty( $response['body'] ) )
				throw new Exception( __( 'Empty response.', WC_Paypal_Advanced::TEXT_DOMAIN ) );
			
			if ( is_wp_error( $response ) ) {
				$this->add_log( 'Error response: ' . $response->get_error_message() );
				
				throw new Exception( $response->get_error_message() );
			}
			
		} catch( Exception $ex ) {
			$this->add_log( 'Exception: '. $ex->getMessage() );
			return array( 'RESPMSG' => $ex->getMessage() );
		}	
	}
	
	/**
	 * remote_post
	 */
	public function remote_post( $params ) {
		global $woocommerce;
		
		try {
			
			$this->add_log( 'Request: ' . print_r( $params, true ));
			
			$params = wp_parse_args( $params, array(
				'USER'			=> $this->user,
				'VENDOR'		=> $this->vendor,
				'PARTNER'   	=> $this->partner,
				'PWD'			=> $this->password,
				'BUTTONSOURCE'	=> 'Woo_Cart',
			) );
			
			$query = $this->to_query_string( $params );
			
			// Send back post vars to paypal
			$args = array(
				'method'  		=> 'POST',
				'body' 			=> $query,
				'sslverify' 	=> false,
				'timeout' 		=> 60,
				'httpversion'   => '1.1',
				'user-agent'	=> 'WooCommerce/' . $woocommerce->version,
				'headers'       => array( 'host' => 'www.paypal.com' ),
			);
			
			/* Using Curl post necessary information to the Paypal Site to generate the secured token */
			$response = wp_remote_post( $this->get_gateway_url(), $args );
			
			return $response;
			
		} catch( Exception $ex ) {
			$this->add_log( 'Exception: '. $ex->getMessage() );
			return array( 'RESPMSG' => $ex->getMessage() );
		}
	}
	
	/**
	 * Limit the length of item names
	 * @param  string $item_name
	 * @return string
	 */
	public function get_item_name( $item_name ) {
		if ( strlen( $item_name ) > 127 ) {
			$item_name = substr( $item_name, 0, 124 ) . '...';
		}
		return html_entity_decode( $item_name, ENT_NOQUOTES, 'UTF-8' );
	}
	
	/**
	 * Get query string from array param
	 */
	protected function to_query_string( $params ){
		$paramList = array();
	    foreach( $params as $key => $value) {
	    	if( is_numeric( $value ) ) {
	        	$paramList[] = $key . '=' . ( $value );
			} else {
				$paramList[] = $key . '[' . strlen($value) . ']=' . ( $value );
			}
	    }

	    return implode( '&', $paramList );
		
	}
	
	/**
	 * Gateway URL 
	 */
	protected function get_gateway_url(){
		if( 'sandbox' == $this->trx_server ) {
			return $this->_test_url;
		}
		
		return $this->_live_url;
	}
	
	/**
	 * Get checkout payment url
	 */
	protected function get_checkout_payment_url( $order ){
		global $wc_paypal_advanced;
		if ( version_compare( WOOCOMMERCE_VERSION, '2.1', '<' ) ) {
			return add_query_arg('order', $order->id, add_query_arg('key', $order->order_key, $wc_paypal_advanced->get_page_url('pay')));
		}
		
		return $order->get_checkout_payment_url( true );
	}
	
	/**
	 * Thanks page
	 */
	protected function get_checkout_order_received_url( $order ){
		global $wc_paypal_advanced;
		
		if ( version_compare( WOOCOMMERCE_VERSION, '2.1', '<' ) ) {
			return add_query_arg('order', $order->id, add_query_arg('key', $order->order_key, $wc_paypal_advanced->get_page_url('thanks')));
		}
		return $order->get_checkout_order_received_url();
	}
	
	/**
	 * return mode
	 */
	protected function get_mode(){
		if( 'sandbox' == $this->trx_server ) {
			return 'TEST';
		}
		
		return 'LIVE';
		
	}
	
	/**
	 * Adds debug messages to the page as a WC message/error, and / or to the WC Error log
	 *
	 * @since 1.2
	 * @param array $errors error messages to add
	 */
	public function add_log( $errors ) {
		global $wc_paypal_advanced;

		if ( $this->debug != 'yes' ) return; 
		
		// do nothing when debug mode is off
		if ( empty( $errors ) )
			return;

		$message = implode( ', ', ( is_array( $errors ) ) ? $errors : array( $errors ) );

		// add debug message to checkout page
		$wc_paypal_advanced->log( $message );			
	}
	
	/**
	 * Show messages
	 */
	public function add_message( $message, $type='error' ) {
		global $wc_paypal_advanced;
		
		// do nothing when debug mode is off
		if ( empty( $message ) )
			return;

		$message = implode( ', ', ( is_array( $message ) ) ? $message : array( $message ) );

		// add debug message to checkout page
		$wc_paypal_advanced->add_message( $message, $type );			
	}
	
	/**
     * Get user's IP address
     */
	function get_user_ip() {
		if($_SERVER['SERVER_NAME'] == 'localhost') {
			return '127.0.0.1';
		}
		return $_SERVER['REMOTE_ADDR'];
	}
}

