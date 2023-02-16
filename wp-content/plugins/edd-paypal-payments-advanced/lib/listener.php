<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class EDD_Paypal_Payments_Advanced_Listener {

  public function __construct() {
    add_action( 'init', array( $this, 'eppa_listen_for_paypal_silent_post' ) );
  }

  /**
   * Listens for a PayPal Silent Post requests and then sends to the processing function
   *
   * @since 1.0
   * @return void
   */
  public function eppa_listen_for_paypal_silent_post() {
    if ( isset( $_GET['edd-listener'] ) AND $_GET['edd-listener'] == 'eppa-silent-post' ) {
      self::_verify_paypal_silent_post();
    }
  }


  /**
   * Verify PayPal Silent POST
   *
   * @since 1.0
   * @global $edd_options Array of all the EDD Options
   * @return void
   */
  private function _verify_paypal_silent_post() {
    global $edd_options;

    // Check the request method is POST
    if ( isset( $_SERVER['REQUEST_METHOD'] ) AND $_SERVER['REQUEST_METHOD'] != 'POST' ) {
      return;
    }

    # Check if actually from PayPal using Inquiry transaction
    require_once EPPA_PLUGIN_DIR . '/paypal/PayPalAdvanced.php';
    $paypal = new PayPalAdvancedGateway;

    $result = $paypal->ppa_inquiry_transaction( $_POST['SECURETOKEN'] );

    if ( is_wp_error( $result ) ) {
      edd_record_gateway_error( __( 'Silent Post Error', 'edd_eppa' ), sprintf( __( 'Invalid Silent Post verification response. Silent Post data: %s', 'edd_eppa' ), json_encode( $result ) ) );
      return; // Something went wrong
    }

    $response = wp_parse_args( $result['body'] );

    if ( strtoupper($response['RESPMSG']) !== 'APPROVED' ) {
      edd_record_gateway_error( __( 'Silent Post Error', 'edd_eppa' ), sprintf( __( 'Invalid Silent Post verification response. Silent Post data: %s', 'edd_eppa' ), json_encode( $result ) ) );
      return; // Response not okay
    }

    if ( $_POST['PNREF'] !== $response['ORIGPNREF']) {
      edd_record_gateway_error( __( 'Silent Post Error', 'edd_eppa' ), sprintf( __( 'Invalid Silent Post verification response - PNREF does not match. Silent Post data: %s', 'edd_eppa' ), json_encode( $result ) ) );
      return; // Response not okay
    }

    // Set the transient, even if it failed. We'll send the user
    // to the receipt page and give a pretty page explaining what
    // went wrong for this particular transaction.
    #set_transient($_POST['PNREF'], $_POST, 120);
    #wp_redirect( home_url('/receipt/'.$_POST['PNREF'], 'http') ); exit;

    $this->_process_paypal_silent_post($_POST);
    exit;
  }

  /**
   * Process successful payment from silent post data
   *
   * @since 1.3.4
   * @global $edd_options Array of all the EDD Options
   * @param array $data Silent POST Data
   * @return void
   */
  private function _process_paypal_silent_post( $data ) {
    global $edd_options;

    // Collect payment details
    $payment_id     = edd_get_purchase_id_by_key( $data['USER1'] );
    $paypal_amount  = $data['AMT'];
    $payment_status = strtoupper( $data['RESPMSG'] );
    $business_email = $data['EMAIL'];
    $purchase_key   = $data['USER1'];

    // Retrieve the total purchase amount (before PayPal)
    $payment_amount = edd_get_payment_amount( $payment_id );

    if (get_post_status( $payment_id ) == 'publish' ) {
      edd_record_log( $title = 'Silent POST Test', $message = 'Payment already published, void: ' . json_encode($data) );
      http_response_code(500);
      status_header(500);
      return; // Only complete payments once
    }

    if ( edd_get_payment_gateway( $payment_id ) != 'paypal_payments_advanced' ) {
      edd_record_log( $title = 'Silent POST Test', $message = 'Not a PPA request: ' . json_encode($data) );
      http_response_code(500);
      status_header(500);
      return; // this isn't a PayPal Silent POST
    }

    // Verify payment recipient
    // if ( strcasecmp( $business_email, trim( $edd_options['paypal_email'] ) ) != 0 )
    // {
    //   edd_record_gateway_error( __( 'PayPal Silent POST Error', 'eppa' ), sprintf( __( 'Invalid business email in PayPal Silent POST response. PayPal Silent POST data: %s', 'eppa' ), json_encode( $data ) ), $payment_id );
    //   edd_update_payment_status( $payment_id, 'failed' );
    //   http_response_code(500);
    //   status_header(500);
    //   return;
    // }

    # @todo verify currency

    if ( $payment_status == 'REFUNDED' ) {
      // Process a refund
      // edd_process_paypal_refund( $data );
    } else {
      if ( number_format( (float) $paypal_amount, 2 ) < number_format( (float) $payment_amount, 2 ) ) {
        // The prices don't match
        edd_record_gateway_error( __( 'PayPal Silent POST Error', 'edd_eppa' ), sprintf( __( 'Invalid payment amount in PayPal Silent POST response. PayPal Silent POST data: %s', 'edd_eppa' ), json_encode( $data ) ), $payment_id );
        edd_update_payment_status( $payment_id, 'failed' );
        http_response_code(500);
        status_header(500);
        return;
      }
      if ( $purchase_key != edd_get_payment_key( $payment_id ) ) {
        // Purchase keys don't match
        edd_record_gateway_error( __( 'PayPal Silent POST Error', 'edd_eppa' ), sprintf( __( 'Invalid purchase key in PayPal Silent POST response. PayPal Silent POST data: %s', 'edd_eppa' ), json_encode( $data ) ), $payment_id );
        edd_update_payment_status( $payment_id, 'failed' );
        return;
      }

      if ( $payment_status == 'APPROVED' || edd_is_test_mode() ) {
        edd_set_payment_transaction_id( $payment_id, $data['PNREF'] );
        edd_update_payment_status( $payment_id, 'publish' );
        edd_empty_cart();
      }
    }
  }
}