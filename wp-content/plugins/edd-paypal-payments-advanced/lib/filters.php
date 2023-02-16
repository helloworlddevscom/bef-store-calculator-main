<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class EDD_Paypal_Payments_Advanced_Filters {

  public function __construct() {
    add_filter( 'edd_checkout_button_purchase', array( $this, 'override_purchase_button_text') );
    add_filter( 'edd_get_payment_transaction_id-paypal_payments_advanced', array( $this, 'get_payment_transaction_id' ), 10, 1 );
    add_filter( 'edd_payment_confirm_paypal_payments_advanced', array( $this, 'confirm_payment' ) );
  }

  /**
   * Changes the purchase button text on the Checkout page
   * to reflect that the payment will be collected on the next page.
   *
   * @since  1.0.8
   * @return string
   */
  public function override_purchase_button_text( $input ) {

    $payment_mode = edd_get_chosen_gateway();

    if ( $payment_mode != 'paypal_payments_advanced' ) {
      return $input;
    }

    $pattern = '%value="([a-zA-Z]+)"%';
    $matches = array();
    $result = preg_match($pattern, $input, $matches);

    // Only replace the text if a payment is required
    if ( $result == 1 && $matches[1] == 'Purchase' ) {
      $modified_input = str_replace( $matches[1], __('Continue to Payment', 'edd_eppa'), $input );
      return $modified_input;
    }

    return $input;
  }

  /**
   * Given a Payment ID, extract the transaction ID
   *
   * @since  1.0.7
   * @param  string $payment_id       Payment ID
   * @return string                   Transaction ID
   */
  public function get_payment_transaction_id( $payment_id ) {

    $transaction_id = '';
    $notes = edd_get_payment_notes( $payment_id );

    foreach ( $notes as $note ) {
      if ( preg_match( '/^PayPal Payments Advanced Transaction ID: ([^\s]+)/', $note->comment_content, $match ) ) {
        $transaction_id = $match[1];
        continue;
      }
    }

    return apply_filters( 'eppa_set_payment_transaction_id', $transaction_id, $payment_id );
  }


  /**
   * Redirect the parent window from the iframe if the customer
   * is checking out using PayPal's Template C (MINLAYOUT)
   *
   * Otherwise, return the original content.
   *
   * @param  string $content "Thank you for your purchase!"
   * @return string
   */
  public function confirm_payment( $content ) {
    global $edd_options;

    $payment_key = isset( $_POST['USER1'] ) ? $_POST['USER1'] : $_GET['eppa_payment_key'];
    $payment_id  = edd_get_purchase_id_by_key( $payment_key );
    $payment     = get_post( $payment_id );

    if ( $payment_id == 0 || ! edd_is_payment_complete( $payment_id ) ) {
      edd_send_back_to_checkout( '?payment-mode=paypal_payments_advanced' );
    }

    edd_empty_cart();

    # Returning from PayPal
    if ( $edd_options['eppa_manager_layout'] == "MINLAYOUT" ) {
      # Javascript
      ob_start(); ?>
      <script type="text/javascript">parent.location = '<?php echo add_query_arg( 'eppa_payment_key', sanitize_key( $payment_key ), edd_get_success_page_url() ); ?>';</script>
      <?php return ob_get_clean();
    }

    return $content;
  }
}
