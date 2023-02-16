<?php
/**
 * Handle subscription cancellation options on EDD Payments when applying a refund.
 *
 * This class is for working with payments in EDD.
 *
 * @package     EDD Recurring
 * @subpackage  Refunds
 * @copyright   Copyright (c) 2019, Sandhills Development
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.9.3
 */

/**
 * Load the javascript which shows the "cancel subscription" checkbox while refunding a payment.
 * This is being done here instead of through wp_enqueue_scripts because it matches the way the
 * button for "Refund Charge in Stripe" is output. See the function called "edd_stripe_admin_js".
 *
 * @access      public
 * @since       2.9.3
 * @param       int $payment_id The id of the payment being viewed, and potentially refunded.
 * @return      void
 */
function edd_recurring_cancel_subscription_during_refund_option( $payment_id = 0 ) {

	if ( ! current_user_can( 'edit_shop_payments', $payment_id ) ) {
		return;
	}

	$payment = edd_get_payment( $payment_id );

	$is_sub = edd_get_payment_meta( $payment_id, '_edd_subscription_payment' );

	// If this payment is the parent payment of a subscription.
	if ( $is_sub ) {

		$subs_db = new EDD_Subscriptions_DB();
		$subs    = $subs_db->get_subscriptions(
			array(
				'parent_payment_id' => $payment_id,
				'order'             => 'ASC',
			)
		);

		// If there's no subscriptions here, don't output any JS.
		if ( ! $subs ) {
			return;
		}

		// If this payment has a parent payment, and is possibly a renewal payment.
	} elseif ( $payment->parent_payment ) {

		// Check if there's a sub ID attached to this payment.
		$sub_id = $payment->get_meta( 'subscription_id', true );

		// If no subscription was found attached to this payment, try searching subscriptions using the parent payment ID.
		if ( ! $sub_id ) {
			$subs_db = new EDD_Subscriptions_DB();
			$subs    = $subs_db->get_subscriptions(
				array(
					'parent_payment_id' => $payment->parent_payment,
					'order'             => 'ASC',
				)
			);
			$sub     = reset( $subs );
			$sub_id  = $sub->id;
		}

		// If there's really just no subscription here, don't output any JS.
		if ( ! $sub_id ) {
			return;
		}
	}

	wp_enqueue_script( 'jquery' );

	?>
	<script type="text/javascript">
		jQuery(document).ready(function($) {
			$('select[name=edd-payment-status]').change(function() {

				if( 'refunded' == $(this).val() ) {

					var cancel_sub_container = $(this).parent().parent().append( '<div class="edd-recurring-cancel-sub"></div>' );

					cancel_sub_container.append( '<input type="checkbox" id="edd_recurring_cancel_subscription" name="edd_recurring_cancel_subscription" value="1" style="margin-top: 0;" />' );
					cancel_sub_container.append( '<label for="edd_recurring_cancel_subscription"><?php echo esc_js( __( 'Cancel Subscription', 'edd-recurring' ) ); ?></label></div>' );

				} else {

					$('#edd_recurring_cancel_subscription').remove();
					$('label[for="edd_recurring_cancel_subscription"]').remove();

				}

			});
		});
	</script>
	<?php

}
add_action( 'edd_view_order_details_before', 'edd_recurring_cancel_subscription_during_refund_option', 100 );

/**
 * Cancel subscription when refunding a payment, if that was selected by the admin.
 *
 * @access      public
 * @since       2.9.3
 * @param       EDD_Payment $payment The EDD_Payment object being viewed, and potentially refunded.
 * @return      void
 */
function edd_recurring_cancel_subscription_during_refund( $payment ) {

	if ( ! current_user_can( 'edit_shop_payments' ) ) {
		return;
	}

	if ( empty( $_POST['edd_recurring_cancel_subscription'] ) ) {
		return;
	}

	$should_cancel_subscription = apply_filters( 'edd_recurring_should_cancel_subscription', true, $payment->ID );

	if ( false === $should_cancel_subscription ) {
		return;
	}

	$is_sub = edd_get_payment_meta( $payment->ID, '_edd_subscription_payment' );

	// If this payment is the parent payment of a subscription.
	if ( $is_sub ) {

		$subs_db = new EDD_Subscriptions_DB();
		$subs    = $subs_db->get_subscriptions(
			array(
				'parent_payment_id' => $payment->ID,
				'order'             => 'ASC',
			)
		);

		// If there's no subscriptions here, don't do anything here.
		if ( ! $subs ) {
			return;
		}

		// Loop through each subscription in this parent payment, cancelling each one.
		foreach ( $subs as $edd_sub ) {

			// Run the cancel method in the EDD_Subscription class. This also cancels the sub at the gateway.
			$edd_sub->cancel();

			$payment->add_note( sprintf( __( 'Subscription %d cancelled because of refund.', 'edd-recurring' ), $edd_sub->id ) );

		}

		// If this payment has a parent payment, and is possibly a renewal payment.
	} elseif ( $payment->parent_payment ) {

		// Check if there's a sub ID attached to this payment.
		$sub_id = $payment->get_meta( 'subscription_id', true );

		// If no subscription was found attached to this payment, try searching subscriptions using the parent payment ID.
		if ( ! $sub_id ) {
			$subs_db = new EDD_Subscriptions_DB();
			$subs    = $subs_db->get_subscriptions(
				array(
					'parent_payment_id' => $payment->parent_payment,
					'order'             => 'ASC',
				)
			);
			$sub     = reset( $subs );
			$sub_id  = $sub->id;
		}

		// If there's really just no subscription here, don't do anything here.
		if ( ! $sub_id ) {
			return;
		}

		// Get the EDD Subscription object that we want to cancel.
		$edd_sub = new EDD_Subscription( $sub_id );

		// Run the cancel method in the EDD_Subscription class. This also cancels the sub at the gateway.
		$edd_sub->cancel();

		$payment->add_note( sprintf( __( 'Subscription %d cancelled because of refund.', 'edd-recurring' ), $sub_id ) );

	}

}
add_action( 'edd_post_refund_payment', 'edd_recurring_cancel_subscription_during_refund' );
