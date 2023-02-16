<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class EDD_Paypal_Payments_Advanced_Shortcode {

  public function __construct() {
    add_shortcode( 'eppa_cc_form', array( $this, 'cc_form_shortcode' ) );
  }


  /**
   * [cc_form_shortcode description]
   *
   * @return [type]
   */
  public function cc_form_shortcode() {
    $key        = get_query_var('eppa_payment_key');
    $payment_id = edd_get_purchase_id_by_key($key);

    if ( $payment_id == 0 ) {
      edd_send_back_to_checkout( '?payment-mode=paypal_payments_advanced' );
    }

    $purchase_meta = edd_get_payment_meta( $payment_id  );
    $cart_details  = edd_get_payment_meta_cart_details( $payment_id, TRUE );

    $purchase_data = array(
      'price'        => edd_get_payment_amount( $payment_id ),
      'date'         => $purchase_meta['date'],
      'user_email'   => edd_get_payment_user_email( $payment_id ),
      'purchase_key' => $key,
      'downloads'    => edd_get_payment_meta_downloads( $payment_id ),
      'cart_details' => edd_get_payment_meta_cart_details( $payment_id, TRUE ),
      'user_info'    => edd_get_payment_meta_user_info( $payment_id ),
      'fees'         => edd_get_payment_fees( $payment_id ),
      'subtotal'     => edd_get_payment_subtotal( $payment_id ),
      'discount'     => $cart_details[0]['discount'],
      'tax'          => edd_get_payment_tax( $payment_id ),
    );

    require_once EPPA_PLUGIN_DIR . '/paypal/PayPalAdvanced.php';
    $paypal = new PayPalAdvancedGateway($purchase_data);

    try {
      $token  = $paypal->retrieve_token();
      $query = '?' . urldecode(
        http_build_query(
          array(
            'SECURETOKEN'   => $token['SECURETOKEN'],
            'SECURETOKENID' => $token['SECURETOKENID'],
            'VERBOSITY'     => 'HIGH'
          )
        )
      );
      $paypal_url = $paypal->checkout_url . $query;

      ob_start(); ?>

      <iframe src="<?php echo $paypal_url; ?>" width="480" height="565" scrolling="no" frameborder="0" border="0" allowtransparency="true" style="margin: 0 auto; display: block;"></iframe>

      <?php return ob_get_clean();

    } catch (Exception $e) {
      ob_start(); ?>

      <div class="edd_errors">
        <p class="edd_error"><?php _e( 'Could not retrieve the secure token. Reason: ' . $e->getMessage(), 'edd_eppa' ); ?></p>
      </div>

      <?php return ob_get_clean();
    }
  }
}