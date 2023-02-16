<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class EDD_Paypal_Payments_Advanced_Setup {

  public function __construct() {
    add_action( 'init', array( $this, 'load_textdomain' ) );
    register_activation_hook( EDD_EPPA_BASENAME, array( $this, 'create_payments_page' ) );

    add_filter( 'edd_payment_gateways',  array( $this, 'register_gateway' ) );
    add_filter( 'query_vars', array( $this, 'add_query_vars' ) );

    # add_action( 'generate_rewrite_rules', array($this, 'eppa_add_rewrite_rules' ) );
    # register_activation_hook( EDD_EPPA_BASENAME, 'flush_rewrite_rules' );
  }

  /**
   * Load the text domain
   *
   * @return [type]
   */
  public function load_textdomain() {
    load_plugin_textdomain( 'edd_eppa', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
  }

  /**
   * Create a payments page on activation if it doesn't already exist.
   *
   * @return void
   */
  public function create_payments_page() {
    if ( ! get_option('_eppa_payment_page') ) {
      global $edd_options;

      $payment_page = wp_insert_post(
        array(
          'post_title'     => __( 'Payment', 'edd_eppa' ),
          'post_content'   => '<h1>Payment</h1>[eppa_cc_form]',
          'post_status'    => 'publish',
          'post_author'    => 1,
          'post_parent'    => $edd_options['purchase_page'],
          'post_type'      => 'page',
          'comment_status' => 'closed'
        )
      );

      update_option('_eppa_payment_page', $payment_page);
    }
  }


  /**
   * Registers the PayPal Payments Advanced gateway.
   *
   * @param  array $gateways
   * @return array
   */
  public function register_gateway( $gateways ) {
    $gateways['paypal_payments_advanced'] = array(
      'admin_label'    => __( 'PayPal Payments Advanced', 'edd_eppa' ),
      'checkout_label' => __( 'PayPal', 'edd_eppa' )
    );
    return $gateways;
  }

  /**
   * Adds a payment key query var to WP
   * @param  [type] $qvars
   * @return [type]
   */
  public function add_query_vars( $qvars ) {
    $qvars[] = 'eppa_payment_key';
    return $qvars;
  }

  /**
   * [eppa_add_rewrite_rules description]
   * @param  [type] $wp_rewrite
   * @return [type]
   */
  public function eppa_add_rewrite_rules($wp_rewrite) {
    $wp_rewrite->rules = array(
      'payment/(.+)\/?'  => $wp_rewrite->index . '?pagename=payment&eppa_payment_key=' . $wp_rewrite->preg_index( 1 ),
    ) + $wp_rewrite->rules;
  }
}