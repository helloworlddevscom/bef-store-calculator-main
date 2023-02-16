<?php
/**
 * Helper Functions
 *
 * @package     EDD\EDDCustomDeliverables\Functions
 * @since       1.0.0
 */


// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * When the customer is viewing the custom deliverables, this is the function which makes sure they see the correct files.
 *
 * @since 1.0.0
 * @param $payment
 * @param $download_id
 * @param $price_id
 * @param $default_download_files
 *
 * @return void
 */
function edd_custom_deliverables_get_download_files( $payment, $download_id, $price_id = 0, $default_download_files ){

	// Get which files we should show, just custom files? Just default files? Both?
	$available_files = edd_custom_deliverables_get_available_files_meta( $payment );

	// Get our array of customized deliverable files for this payment
	$custom_deliverables = edd_custom_deliverables_get_custom_files_meta( $payment );

	// If this is a single price product, use 0 as the key
	if ( empty( $price_id ) ){
		$price_id = 0;
	}

	// Figure out which files we should show based on the available_files saved setting
	if ( 'custom_only' == $available_files ) {
		$return_files = $custom_deliverables[$download_id][$price_id];
	} elseif ( 'default_only' == $available_files ) {
		$return_files = $default_download_files;
	} else {

		if ( ! empty( $custom_deliverables ) ) {

			if ( isset( $custom_deliverables[$download_id][$price_id] ) ) {
				foreach( $custom_deliverables[$download_id][$price_id] as $file_key => $custom_file_data ){
					$default_download_files[$file_key] = $custom_file_data;
				}
			}

		}

		$return_files = $default_download_files;
	}

	// If no custom deliverables are currently set up, return an empty array
	if ( ! isset( $return_files ) ){
		return array();
	}

	return $return_files;
}

/**
 * This function retrieves the custom deliverables from the payment and runs them through a filter.
 * Anywhere you retrieve custom deliverables payment meta, you should do it using this function.
 *
 * @since 1.0.0
 * @param $payment
 *
 * @return void
 */
function edd_custom_deliverables_get_custom_files_meta( $payment ){

	// Get the custom files meta from the database
	$custom_deliverables = $payment->get_meta( '_eddcd_custom_deliverables_custom_files', true );

	// Filter those
	$custom_deliverables = apply_filters( 'eddcd_custom_deliverables_custom_files', $custom_deliverables, $payment );

	return $custom_deliverables;

}

/**
 * This function retrieves the available files setting from the payment and runs them through a filter.
 * Anywhere you retrieve available files payment meta, you should do it using this function.
 *
 * @since 1.0.0
 * @param $payment
 *
 * @return void
 */
function edd_custom_deliverables_get_available_files_meta( $payment ){

	// Get the available files meta from the database
	$available_files = $payment->get_meta( '_eddcd_custom_deliverables_available_files', true );

	// Filter it
	$available_files = apply_filters( 'eddcd_custom_deliverables_available_files', $available_files, $payment );

	return $available_files;

}

/**
 * This function retrieves the fulfilles jobs setting from the payment and runs them through a filter.
 * Anywhere you retrieve fulfilled jobs payment meta, you should do it using this function.
 *
 * @since 1.0.0
 * @param $payment
 *
 * @return void
 */
function edd_custom_deliverables_get_fulfilled_jobs_meta( $payment ){

	// Get the custom files meta from the database
	$fulfilled_jobs = $payment->get_meta( '_eddcd_custom_deliverables_fulfilled_jobs', true );

	// Filter those
	$fulfilled_jobs = apply_filters( 'eddcd_custom_deliverables_fulfilled_jobs', $fulfilled_jobs, $payment );

	return $fulfilled_jobs;

}

/**
 * This function will delete all uploaded custom deliverables which are attached to a payment.
 * Note that it deletes the actual files, and the references to them.
 *
 * @since 1.0.2
 * @param $payment
 *
 * @return void
 */
function edd_custom_deliverables_delete_all_custom_deliverables_for_payment( $payment_id_or_payment_object ){

	$payment = is_numeric( $payment_id_or_payment_object ) ? edd_get_payment( $payment_id_or_payment_object ) : $payment_id_or_payment_object;

	// Get our array of customized deliverable files for this payment
	$custom_deliverables = edd_custom_deliverables_get_custom_files_meta( $payment );

	if ( ! empty( $custom_deliverables ) ) {

		foreach( $custom_deliverables as $download_id => $price_ids_and_files ) {
			foreach( $price_ids_and_files as $price_id => $files ) {
				foreach( $files as $file_key => $custom_file_data ){

					$wp_attachment_metadata = get_post_meta( $custom_file_data['attachment_id'], '_wp_attachment_metadata', true );

					// Delete each variation of this attachment, which are stored in the attachment meta
					if ( is_array( $wp_attachment_metadata ) ) {
						foreach( $wp_attachment_metadata as $attachment_variations ) {
							if ( is_array( $attachment_variations ) ) {
								foreach( $attachment_variations as $attachment_variation ) {
									if( isset( $attachment_variation['file'] ) ) {
										$result = wp_delete_file( $attachment_variation['file'] );
									}
								}
							}
						}
					}

					// Now we can delete the attachment itself
					wp_delete_attachment( $custom_file_data['attachment_id'], true );

					// Remove this file from the custom deliverables array as well
					unset( $custom_deliverables[$download_id][$price_id][$file_key] );

				}
			}
		}
	}

	// Save the updated files array
	edd_update_payment_meta( $payment->ID, '_eddcd_custom_deliverables_custom_files', $custom_deliverables );

	$edd_cd_deleting_custom_deliverables = false;
}
