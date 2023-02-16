<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class EDD_Paypal_Payments_Advanced_Deprecated {

  public function __construct() {
    add_action( 'admin_init', array( $this, 'copy_deprecated_setting' ) );
  }


  /**
   * [copy_deprecated_setting description]
   * @return [type] [description]
   */
  public function copy_deprecated_setting() {
    $edd_settings = get_option('edd_settings');

    if ( isset($edd_settings['eppa_license_key']) ) {
      $edd_settings['edd_paypal_payments_advanced_license_key'] = $edd_settings['eppa_license_key'];

      update_option( 'edd_paypal_payments_advanced_license_active', 'valid' );
      unset($edd_settings['eppa_license_key']);
      update_option('edd_settings', $edd_settings);
    }
  }
}