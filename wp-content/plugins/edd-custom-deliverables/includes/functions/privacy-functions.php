<?php
/**
 * Privacy Functions
 *
 * @package     EDD/CustomDeliverables
 * @subpackage  Functions
 * @copyright   Copyright (c) 2018, Easy Digital Downloads, LLC
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/** Anonymizer/Erasure Functions */

/**
 * When a payment status action is requested, checks if the payment still needs to be delivered, and if so, tells WordPress
 * and Easy Digital Downloads to take no action on the payment.
 *
 * Custom Deliverables is using this at the moment when published, pending, and processing payments
 * are being modified for a privacy erasure request. That modification being made to the payment
 * in question is stored as a verb here in the $action variable.
 * It could be 1 of these 3 verbs: 'anonymize', 'delete', or 'none'.
 * If the $action is set to 'none', it will automatically prevent the customer from being deleted/anonymized,
 * because we know that at least 1 of the customer's payments could not be deleted/anonymized.
 *
 * @param $action
 * @param $payment
 *
 * @return string
 */
function edd_custom_deliverables_modify_payment_privacy_action( $action, $payment ) {

	$fulfillment_status = $payment->get_meta( '_eddcd_fulfillment_status', true );
	$fulfillment_status = empty( $fulfillment_status ) ? 1 : $fulfillment_status;

	// If this has not yet been fulfilled (1 is not fulfulled, 2 is fulfilled, and blank means it doesn't apply.)
	if( 1 == $fulfillment_status ) {
		// Since we can't delete this payment, we definitely can't anonymize the customer. Prevent that by setting the payment action to "none".
		$action = 'none';
	}

    return $action;
}
add_filter( 'edd_privacy_payment_status_action_publish',    'edd_custom_deliverables_modify_payment_privacy_action', 10, 2 );
add_filter( 'edd_privacy_payment_status_action_pending',    'edd_custom_deliverables_modify_payment_privacy_action', 10, 2 );
add_filter( 'edd_privacy_payment_status_action_processing', 'edd_custom_deliverables_modify_payment_privacy_action', 10, 2 );

/**
 * During the process of anonymizing or deleting a payment detects if an item has custom deliverables information, and if so, takes
 * no action on the payment and returns a message stating why it was not processed.
 *
 * @param array $should_anonymize_payment
 * @param       $payment
 *
 * @return array
 */
function edd_custom_deliverables_should_anonymize_payment( $should_anonymize_payment = array(), $payment ) {

	$fulfillment_status = $payment->get_meta( '_eddcd_fulfillment_status', true );
	$fulfillment_status = empty( $fulfillment_status ) ? 1 : $fulfillment_status;

	// If this has not yet been fulfilled (1 is not fulfulled, 2 is fulfilled, and blank means it doesn't apply.)
	if( 1 == $fulfillment_status ) {
		return array(
			'should_anonymize' => false,
			'message' => sprintf( __( 'This payment (%d) could not be anonymized because there are outstanding Custom Deliverables', 'edd-custom-deliverables' ), $payment->ID )
		);
	}

	 return $should_anonymize_payment;

}
add_filter( 'edd_should_anonymize_payment', 'edd_custom_deliverables_should_anonymize_payment', 10, 2 );

/**
 * When a payment is anonymized or deleted, these hooks remove the attached custom deliverables
 *
 * @param $payment
 */
add_action( 'edd_anonymize_payment', 'edd_custom_deliverables_delete_all_custom_deliverables_for_payment' );
add_action( 'edd_payment_delete', 'edd_custom_deliverables_delete_all_custom_deliverables_for_payment' );

/**
 * Register eraser for EDD Custom Deliverables Data
 *
 * @param array $erasers
 *
 * @return array
 */
function edd_custom_deliverables_register_privacy_erasers( $erasers = array() ) {

	$erasers[] = array(
		'eraser_friendly_name' => __( 'Custom Deliverables', 'edd-custom-deliverables' ),
		'callback'             => 'edd_privacy_custom_deliverables_eraser',
	);

	return $erasers;

}
add_filter( 'wp_privacy_personal_data_erasers', 'edd_custom_deliverables_register_privacy_erasers', 11, 1 );

/**
 * Leave a note that lets the eraser know we removed all Custom Deliverables
 *
 * @param string $email_address
 * @param int    $page
 *
 * @return array
 */
function edd_privacy_custom_deliverables_eraser( $email_address, $page = 1 ) {

	return array(
		'items_removed'  => true,
		'items_retained' => false,
		'messages'       => array(
			sprintf( __( 'All eligible Custom Deliverables for %s were removed.', 'edd-custom-deliverables' ), $email_address ),
		),
		'done'           => true,
	);

}
