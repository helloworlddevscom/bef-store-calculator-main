<?php

namespace ForGravity\Fillable_PDFs;

use GFCommon;
use GFFormsModel;

/**
 * Fillable PDFs Server class.
 *
 * @since     1.0
 * @package   FillablePDFs
 * @author    ForGravity
 * @copyright Copyright (c) 2017, ForGravity
 */
class Server {

	/**
	 * Contains an instance of this class, if available.
	 *
	 * @since  1.0
	 * @access private
	 * @var    object $_instance If available, contains an instance of this class.
	 */
	private static $_instance = null;

	/**
	 * Get instance of this class.
	 *
	 * @since  1.0
	 * @access public
	 * @static
	 *
	 * @return Server
	 */
	public static function get_instance() {

		if ( null === self::$_instance ) {
			self::$_instance = new self;
		}

		return self::$_instance;

	}

	/**
	 * Add needed hooks.
	 *
	 * @since  1.0
	 */
	public function __construct() {

		add_action( 'wp', array( $this, 'serve_pdf' ) );

	}

	/**
	 * Server PDF to user.
	 *
	 * @since  1.0
	 */
	public function serve_pdf() {

		// Get PDF ID.
		$pdf_id = rgget( 'fgpdf' );

		// If no PDF ID exists, return.
		if ( rgblank( $pdf_id ) ) {
			return;
		}

		// Get PDF meta.
		$pdf_meta = Fillable_PDFs::get_pdf_meta( $pdf_id );

		// If PDF was not found, return error.
		if ( ! is_array( $pdf_meta ) ) {
			wp_die( esc_html__( 'The PDF could not be found.', 'forgravity_fillablepdfs' ) );
		}

		// Get access token.
		$token = rgget( 'token' );
		$token = trim( $token );

		// Verify access.
		$can_access = $this->can_access_pdf( $pdf_meta, $token );

		// If user cannot access PDF, return error.
		if ( ! $can_access ) {
			wp_die( esc_html__( 'Access denied.', 'forgravity_fillablepdfs' ) );
		}

		// Get file path.
		$file_path = fg_fillablepdfs()->get_physical_file_path( $pdf_meta );

		// If PDF file cannot be retrieved, return error.
		if ( ! file_exists( $file_path ) ) {
			wp_die( esc_html__( 'The PDF file you are trying to download could not be found.', 'forgravity_fillablepdfs' ) );
		}

		// If fileinfo extension does not exist, return error to prevent fatal error.
		if ( ! function_exists( 'mime_content_type' ) ) {
			wp_die( esc_html__( 'Unable to validate file type.', 'forgravity_fillablepdfs' ) );
		}

		// If provided file is not a PDF, return error.
		if ( mime_content_type( $file_path ) !== 'application/pdf' ) {
			wp_die( esc_html__( 'Invalid file provided.', 'forgravity_fillablepdfs' ) );
		}

		/**
		 * Define if PDFs should be downloaded or viewed.
		 *
		 * @since 2.3
		 *
		 * @param bool $force_download
		 */
		$force_download = apply_filters( 'fg_fillablepdfs_force_download', ! ( isset( $_GET['dl'] ) && $_GET['dl'] === '0' ) );

		// Enable output buffering.
		ob_start();

		// Set headers.
		header( 'X-Robots-Tag: noindex, nofollow', true );
		header( 'Content-Description: File Transfer' );
		header( 'Content-Transfer-Encoding: binary' );
		header( 'Cache-Control: public, must-revalidate, max-age=0' );
		header( 'Pragma: public' );
		header( 'Expires: Sat, 26 Jul 1997 05:00:00 GMT' );
		header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
		header( 'Content-Type: application/force-download' );
		header( 'Content-Type: application/octet-stream', false );
		header( 'Content-Type: application/download', false );
		header( 'Content-Type: application/pdf', false );
		if ( ! isset( $_SERVER['HTTP_ACCEPT_ENCODING'] ) || empty( $_SERVER['HTTP_ACCEPT_ENCODING'] ) ) {
			// Do not use length if server is using compression.
			header( 'Content-Length: ' . filesize( $file_path ) );
		}

		// Set force download header.
		header(
			sprintf(
				'Content-Disposition: %s; filename="%s"',
				$force_download ? 'attachment' : 'inline',
				sanitize_file_name( $pdf_meta['file_name'] )
			)
		);

		// Flush output buffer.
		while( ob_get_level() ) {
			ob_end_clean();
		}

		// Server PDF.
		readfile( $file_path );

		die();

	}





	// # HELPERS -------------------------------------------------------------------------------------------------------

	/**
	 * Verify if user can access PDF.
	 *
	 * @since  1.0
	 *
	 * @param array  $pdf_meta PDF meta.
	 * @param string $token    Access token.
	 *
	 * @return bool
	 */
	public function can_access_pdf( $pdf_meta, $token = '' ) {

		// Get entry object.
		$entry = fg_fillablepdfs()->get_entry_for_pdf( $pdf_meta );

		// If PDF is publicly accessible, return.
		if ( rgar( $pdf_meta, 'access' ) === 'anyone' || rgar( $pdf_meta, 'public' ) ) {
			return true;
		}

		// If the token was provided and matches, return.
		if ( ! empty( $token ) && $token === rgar( $pdf_meta, 'token' ) ) {
			return true;
		}

		/**
		 * WordPress user capability required to view all PDFs.
		 *
		 * @since 2.3
		 *
		 * @param string|array $capability Capability required to view all PDFs.
		 */
		$capability = apply_filters( 'fg_fillablepdfs_view_pdf_capabilities', 'gravityforms_view_entries' );

		// If user has access to "gravityforms_view_entries" capability, return.
		if ( is_user_logged_in() && GFCommon::current_user_can_any( $capability ) ) {
			return true;
		}

		// If user is logged in and owns the entry, return.
		if ( is_user_logged_in() && get_current_user_id() === (int) rgar( $entry, 'created_by' ) ) {
			return true;
		}

		// If user is not logged in but has the same IP address, return.
		if ( ! is_user_logged_in() ) {

			// Get current user, server IP addresses.
			$user_ip   = trim( GFFormsModel::get_ip() );
			$server_ip = isset( $_SERVER['SERVER_ADDR'] ) ? $_SERVER['SERVER_ADDR'] : '127.0.0.1';

			// If the user IP matches and is not the server IP address, check time since entry was submitted.
			if ( rgar( $entry, 'ip' ) === $user_ip && rgar( $entry, 'ip' ) !== $server_ip && strlen( $user_ip ) > 0 ) {

				/**
				 * How many minutes after submission user can view PDF.
				 *
				 * @since 2.3
				 *
				 * @param int $timeout Time in minutes user can view PDF after submission.
				 */
				$timeout = apply_filters( 'fg_fillablepdfs_logged_out_timeout', 20 );
				$timeout *= MINUTE_IN_SECONDS;

				// If entry creation in within the timeout window, return.
				if ( time() <= ( strtotime( rgar( $entry, 'date_created' ) ) + $timeout ) ) {
					return true;
				}

			}

		}

		return false;

	}

}
