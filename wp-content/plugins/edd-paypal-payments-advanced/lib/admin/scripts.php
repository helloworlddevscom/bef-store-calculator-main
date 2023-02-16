<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class EDD_Paypal_Payments_Advanced_Admin_Scripts {

  public function __construct() {
    add_action( 'edd_view_order_details_before', array($this, 'admin_js'), 100 );
  }

  /**
   * Load our admin javascript
   *
   * @access      public
   * @since       1.0.5
   * @return      void
   */
  public function admin_js( $payment_id  = 0 ) {

    if ( 'paypal_payments_advanced' !== edd_get_payment_gateway( $payment_id ) ) {
      return;
    }
  ?>
    <script type="text/javascript">
      jQuery(document).ready(function($) {
        $('select[name=edd-payment-status]').change(function() {

          if( 'refunded' == $(this).val() ) {

            $(this).parent().parent().append( '<input type="checkbox" id="edd_refund_in_paypal_payments_advanced" name="edd_refund_in_paypal_payments_advanced" value="1"/>' );
            $(this).parent().parent().append( '<label for="edd_refund_in_paypal_payments_advanced">Refund Charge in PayPal</label>' );

          } else {

            $('#edd_refund_in_paypal_payments_advanced').remove();
            $('label[for="edd_refund_in_paypal_payments_advanced"]').remove();

          }
        });
      });
    </script>
  <?php
  }
}
