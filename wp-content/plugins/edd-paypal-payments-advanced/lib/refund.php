<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class EDD_Paypal_Payments_Advanced_Refund {

  public function __construct() {
    add_action( 'edd_update_payment_status', array($this, 'process_refund'), 200, 3 );
  }

  /**
   * Process refund in Paypal
   *
   * @access      public
   * @since       1.0.5
   * @return      void
   */
  public function process_refund( $payment_id, $new_status, $old_status ) {

    global $edd_options;

    if ( empty( $_POST['edd_refund_in_paypal_payments_advanced'] ) ) {
      return;
    }

    if ( 'publish' != $old_status && 'revoked' != $old_status ) {
      return;
    }

    if ( 'refunded' != $new_status ) {
      return;
    }

    $transaction_id = edd_get_payment_transaction_id( $payment_id );

    // Bail if no charge ID was found
    if ( empty( $transaction_id ) || ! $transaction_id ) {
      return;
    }

    if ( ! class_exists( 'PayPalAdvancedGateway' ) ) {
      require_once EPPA_PLUGIN_DIR . '/paypal/PayPalAdvanced.php';
    }

    try {
      $paypal = new PayPalAdvancedGateway;
      $paypal->refund_transaction( $transaction_id );

      edd_insert_payment_note( $payment_id, __( 'Charge refunded in PayPal', 'edd_eppa' ) );

    } catch ( Exception $e ) {

      $error  = $e->getMessage();
      wp_die( $error );
    }
  }
}
