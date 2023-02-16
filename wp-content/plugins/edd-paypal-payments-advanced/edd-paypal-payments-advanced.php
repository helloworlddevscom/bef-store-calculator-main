<?php
/*
Plugin Name: Easy Digital Downloads - PayPal Payments Advanced
Plugin URL: http://easydigitaldownloads.com/extension/paypal-payments-advanced
Description: Adds a payment gateway for PayPal Payments Advanced to Easy Digital Downloads
Version: 1.1.1
Author: Dave Kiss
Author URI: http://davekiss.com
Contributors: davekiss
Text Domain: edd_eppa
*/

class EDD_Paypal_Payments_Advanced {

  /**
   * The EDD PayPal Payments Advanced instance
   *
   * @var object
   */
  private static $instance = NULL;

  public function __construct() {
    add_action( 'template_redirect', array( $this, 'failed_payment' ) );
    add_action( 'edd_gateway_paypal_payments_advanced', array( $this, 'process_payment' ) );
    add_action( 'parse_request', array( $this, 'load_order_details' ) );
  }


  /**
   * Creates or returns an instance of this class.
   *
   * @return  EDD_Paypal_Payments_Advanced A single instance of this class.
   */
  public static function get_instance() {
    if ( ! isset( self::$instance ) AND ! ( self::$instance instanceof EDD_Paypal_Payments_Advanced ) ) {
      self::$instance = new self;
      self::$instance->_define_constants();
      self::$instance->_include_files();

      if ( class_exists( 'EDD_License' ) ) {
        $edd_stripe_license = new EDD_License( __FILE__, EDD_EPPA_PRODUCT_NAME, EDD_EPPA_VERSION, 'Dave Kiss', NULL, EDD_EPPA_STORE_API_URL );
      }

      if ( is_admin() ) {
        new EDD_Paypal_Payments_Advanced_Admin_Scripts;
      }

      // Can save these in public vars if need to access
      new EDD_Paypal_Payments_Advanced_Deprecated;
      new EDD_Paypal_Payments_Advanced_Filters;
      new EDD_Paypal_Payments_Advanced_Listener;
      new EDD_Paypal_Payments_Advanced_Refund;
      new EDD_Paypal_Payments_Advanced_Settings;
      new EDD_Paypal_Payments_Advanced_Setup;
      new EDD_Paypal_Payments_Advanced_Shortcode;
    }

    return self::$instance;
  }


  /**
   * Define all of the constants used throughout the plugin.
   *
   * @return void
   */
  private function _define_constants() {
    define( 'EDD_EPPA_STORE_API_URL', 'https://easydigitaldownloads.com' );
    define( 'EDD_EPPA_PRODUCT_NAME', 'PayPal Payments Advanced' );
    define( 'EDD_EPPA_VERSION', '1.1.1' );
    define( 'EDD_EPPA_BASENAME', plugin_basename( __FILE__ ) );

    if ( ! defined( 'EPPA_PLUGIN_DIR' ) ) {
      define( 'EPPA_PLUGIN_DIR', dirname( __FILE__ ) );
    }

    if ( ! defined( 'EDD_EPPA_PLUGIN_URL' ) ) {
      define( 'EDD_EPPA_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
    }
  }

  /**
   * Include the files required by this plugin.
   * @return [type]
   */
  private function _include_files() {
    require_once EPPA_PLUGIN_DIR . '/lib/deprecated.php';
    require_once EPPA_PLUGIN_DIR . '/lib/filters.php';
    require_once EPPA_PLUGIN_DIR . '/lib/listener.php';
    require_once EPPA_PLUGIN_DIR . '/lib/refund.php';
    require_once EPPA_PLUGIN_DIR . '/lib/settings.php';
    require_once EPPA_PLUGIN_DIR . '/lib/setup.php';
    require_once EPPA_PLUGIN_DIR . '/lib/shortcode.php';

    if ( is_admin() ) {
      require_once EPPA_PLUGIN_DIR . '/lib/admin/scripts.php';
    }
  }


  /**
   * User has clicked the "Continue to Payment" button
   * with the intention of checking out.
   *
   * @param  array $purchase_data
   * @global $edd_options
   * @return [type]
   */
  public function process_payment( $purchase_data ) {
    global $edd_options;

    $payment_data = array(
      'price'        => $purchase_data['price'],
      'date'         => $purchase_data['date'],
      'user_email'   => $purchase_data['user_email'],
      'purchase_key' => $purchase_data['purchase_key'],
      'currency'     => edd_get_currency(),
      'downloads'    => $purchase_data['downloads'],
      'cart_details' => $purchase_data['cart_details'],
      'user_info'    => $purchase_data['user_info'],
      'status'       => 'pending',
      'gateway'      => 'paypal_payments_advanced'
    );

    // record the pending payment
    $payment_id = edd_insert_payment( $payment_data );

    // make sure we don't have any left over errors present
    edd_clear_errors();

    if ( $edd_options['eppa_manager_layout'] == "TEMPLATEA" || $edd_options['eppa_manager_layout'] == "TEMPLATEB" ) {
      require_once EPPA_PLUGIN_DIR . '/paypal/PayPalAdvanced.php';
      $paypal = new PayPalAdvancedGateway($purchase_data);

      try {
        $token  = $paypal->retrieve_token();

        $query = urldecode(
          http_build_query(
            array(
              'SECURETOKEN'   => $token['SECURETOKEN'],
              'SECURETOKENID' => $token['SECURETOKENID'],
              'VERBOSITY'     => 'HIGH'
            )
          )
        );
        $paypal_url = $paypal->checkout_url . '?' . $query;
        edd_empty_cart();
        wp_redirect( $paypal_url );
        exit;

      } catch (Exception $e) {
        # Get rid of the pending purchase
        edd_update_payment_status( $payment_id, 'failed' );

        edd_set_error( 0, __( 'Could not retrieve the secure token. Reason: ' . $e->getMessage(), 'edd_eppa' ) );
        edd_send_back_to_checkout( '?payment-mode=paypal_payments_advanced' );
      }
    } else {
      # Send to payment page, where the Layout C will be presented.
      wp_redirect( add_query_arg( 'eppa_payment_key', $payment_data['purchase_key'], get_permalink( get_option('_eppa_payment_page') ) ) );
      exit;
    }
  }


  /**
   * Mark a payment as failed if a user cancels from in PayPal
   *
   * @return [type]
   */
  public function failed_payment() {
    global $edd_options;

    if ( is_admin()
      OR ! is_page( $edd_options['failure_page'] )
      OR ! isset( $_GET['USER1'] )
    ) return;

    $payment_key = ( isset( $_GET['USER1'] ) ) ? urldecode( $_GET['USER1'] ) : '';
    $payment_id = edd_get_purchase_id_by_key($payment_key);

    if ($payment_id == 0) return;

    $status = get_post_field( 'post_status', $payment_id );

    if ( $status != 'pending' ) return;

    edd_update_payment_status( $payment_id, 'failed' );

    if ( function_exists( 'edd_insert_payment_note' ) ) {
      edd_insert_payment_note( $payment_id, __( 'The user cancelled payment after going to PayPal', 'edd_eppa' ) );
    }

    edd_empty_cart();
    edd_send_back_to_checkout();
  }


  /**
   * Load up the order details based on the payment key that
   * exists in the query vars.
   *
   * @param  [type] $wp
   * @return [type]
   */
  public function load_order_details($wp) {
    if ( array_key_exists('eppa_payment_key', $wp->query_vars) ) {
      global $order_details;
      $order_details = get_transient($wp->query_vars['eppa_payment_key']);
    }
  }
}

EDD_Paypal_Payments_Advanced::get_instance();