<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class EDD_Paypal_Payments_Advanced_Settings {

  public function __construct() {
    add_filter( 'edd_settings_gateways', array( $this, 'add_settings' ) );
    add_filter( 'edd_settings_sections_gateways', array( $this, 'add_settings_section') );

    add_action( 'plugins_loaded', array($this, 'conditional_bill_address_fields') );
  }


  /**
   * Adds the PPA settings to the Payment Gateways section
   *
   * @param  array $settings
   * @return array
   */
  public function add_settings( $settings ) {
    $settings['paypal_payments_advanced'] = array(
      'paypal_payments_advanced_header' => array(
        'id' => 'paypal_payments_advanced_header',
        'name' => '<strong>' . __( 'PayPal Payments Advanced - Manager Settings', 'edd_eppa' ) . '</strong>',
        'desc' => __( 'Configure your PayPal Payments Advanced settings', 'edd_eppa' ),
        'type' => 'header'
      ),
      'live_paypal_manager_partner' => array(
        'id' => 'live_paypal_manager_partner',
        'name' => __( 'Live PayPal Manager Partner', 'edd_eppa' ),
        'desc' => __( 'Enter your live PayPal Manager partner (default: "PayPal")', 'edd_eppa' ),
        'type' => 'text',
        'size' => 'regular'
      ),
      'live_paypal_manager_vendor' => array(
        'id' => 'live_paypal_manager_vendor',
        'name' => __( 'Live PayPal Manager Vendor', 'edd_eppa' ),
        'desc' => __( 'Enter your live PayPal Manager vendor', 'edd_eppa' ),
        'type' => 'text',
        'size' => 'regular'
      ),
      'live_paypal_manager_username' => array(
        'id' => 'live_paypal_manager_username',
        'name' => __( 'Live PayPal Manager Username', 'edd_eppa' ),
        'desc' => __( 'Enter your live PayPal Manager username', 'edd_eppa' ),
        'type' => 'text',
        'size' => 'regular'
      ),
      'live_paypal_manager_password' => array(
        'id' => 'live_paypal_manager_password',
        'name' => __( 'Live PayPal Manager Password', 'edd_eppa' ),
        'desc' => __( 'Enter your live PayPal Manager password', 'edd_eppa' ),
        'type' => 'text',
        'size' => 'regular'
      ),
      'test_paypal_manager_username' => array(
        'id' => 'test_paypal_manager_username',
        'name' => __( 'Test PayPal Manager Username', 'edd_eppa' ),
        'desc' => __( 'Enter your test PayPal Manager username', 'edd_eppa' ),
        'type' => 'text',
        'size' => 'regular'
      ),
      'test_paypal_manager_password' => array(
        'id' => 'test_paypal_manager_password',
        'name' => __( 'Test PayPal Manager Password', 'edd_eppa' ),
        'desc' => __( 'Enter your test PayPal manager password', 'edd_eppa' ),
        'type' => 'text',
        'size' => 'regular'
      ),
      'eppa_manager_layout' => array(
        'id' => 'eppa_manager_layout',
        'name' => __( 'PayPal Template', 'edd_eppa' ),
        'desc' => __( 'Select the template to use for your checkout process', 'edd_eppa' ),
        'type' => 'select',
        'options' => array(
          'TEMPLATEA' => __('Template A', 'edd_eppa'),
          'TEMPLATEB' => __('Template B', 'edd_eppa'),
          'MINLAYOUT' => __('Template C', 'edd_eppa'), # Or MOBILE
        ),
      ),
      'eppa_require_billing_address' => array(
        'id' => 'eppa_require_billing_address',
        'name' => __( 'Require Billing Address', 'edd' ),
        'desc' => __( 'Check this to display billing address fields during checkout.', 'edd_eppa' ),
        'type' => 'checkbox'
      ),
    );

    return $settings;
  }

  /**
   * Add the settings section to the General Settings tab layout.
   *
   * @param array $sections Registered settings sections
   * @since 1.0.8
   */
  public function add_settings_section($sections) {
    $sections['paypal_payments_advanced'] = __( 'PayPal Payments Advanced', 'edd_eppa' );
    return $sections;
  }

  /**
   * Require the bill address fields if the user chooses to show them.
   * This increases compatibility with layout C.
   *
   * @since  1.0.4
   * @return void
   */
  public function conditional_bill_address_fields() {
    global $edd_options;

    if ( isset( $edd_options['eppa_require_billing_address'] ) ) {

      // Don't show if taxes are enabled - EDD will automatically show the address fields,
      // so we don't want to duplicate them.
      if ( ! edd_use_taxes() ) {
        add_action( 'edd_paypal_payments_advanced_cc_form', 'edd_default_cc_address_fields' );
      }
    } else {
      add_action( 'edd_paypal_payments_advanced_cc_form', '__return_false' );
    }
  }
}
