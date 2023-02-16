<?php
/**
 * Plugin Name: Fillable PDFs for Gravity Forms
 * Plugin URI: https://forgravity.com/plugins/fillable-pdfs/
 * Description: Generate PDFs from Gravity Forms quickly and easily. Store locally, and import PDFs to use as the basis of a new Gravity Forms.
 * Version: 3.0.3
 * Author: ForGravity
 * Author URI: https://forgravity.com
 * Text Domain: forgravity_fillablepdfs
 * Domain Path: /languages
 */

define( 'FG_FILLABLEPDFS_VERSION', '3.0.3' );
define( 'FG_FILLABLEPDFS_EDD_ITEM_ID', 169 );
define( 'FG_FILLABLEPDFS_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

if ( ! defined( 'FG_EDD_STORE_URL' ) ) {
	define( 'FG_EDD_STORE_URL', 'https://forgravity.com' );
}

if ( ! defined( 'FG_FILLABLEPDFS_API_URL' ) ) {
	define( 'FG_FILLABLEPDFS_API_URL', 'https://forgravity.com/wp-json/pdf/v2/' );
}

if ( ! defined( 'FG_FILLABLEPDFS_PATH_CHECK_ACTION' ) ) {
	define( 'FG_FILLABLEPDFS_PATH_CHECK_ACTION', 'forgravity_fillablepdfs_check_base_pdf_path_public' );
}

// Initialize the autoloader.
require_once( 'includes/autoload.php' );

// Initialize plugin updater.
add_action( 'init', [ 'FillablePDFs_Bootstrap', 'updater' ], 0 );

// Bootstrap Fillable PDFs, register template downloader.
add_action( 'gform_loaded', [ 'FillablePDFs_Bootstrap', 'load' ], 5 );
add_action( 'gform_loaded', [ 'ForGravity\Fillable_PDFs\Templates', 'maybe_download_template' ], 6 );

// Include Gravity Flow step.
add_action( 'gravityflow_loaded', [ 'FillablePDFs_Bootstrap', 'load_gravityflow' ], 5 );

// Remove public folder checking on deactivation.
register_deactivation_hook( __FILE__, [ 'ForGravity\Fillable_PDFs\Fillable_PDFs', 'clear_scheduled_events' ] );

/**
 * Class FillablePDFs_Bootstrap
 * Handles the loading of the Fillable PDFs Add-On and registers with the Add-On framework.
 *
 * @since 1.0
 */
class FillablePDFs_Bootstrap {

	/**
	 * If the Feed Add-On Framework exists, Fillable PDFs Add-On is loaded.
	 *
	 * @static
	 */
	public static function load() {

		if ( ! method_exists( 'GFForms', 'include_feed_addon_framework' ) ) {
			return;
		}

		// Register GravityView field.
		if ( class_exists( 'GravityView_Field' ) ) {
			include( dirname( __FILE__ ) . '/includes/integrations/gravityview/class-field-link.php' );
		}

		if ( ! version_compare( GFCommon::$version, '2.5-dev-1', '>=' ) ) {
			GFAddOn::register( 'ForGravity\Fillable_PDFs\Legacy\Fillable_PDFs' );
		} else {
			GFAddOn::register( 'ForGravity\Fillable_PDFs\Fillable_PDFs' );
		}

	}

	/**
	 * If the Gravity Flow exists, Fillable PDFs Step is loaded.
	 *
	 * @since 1.0
	 */
	public static function load_gravityflow() {

		try {

			Gravity_Flow_Steps::register( new \ForGravity\Fillable_PDFs\Integrations\Gravity_Flow\Step() );

		} catch ( Exception $e ) {

			fg_fillablepdfs()->log_error( __METHOD__ . '(): Unable to load Gravity Flow step.' );

		}

	}

	/**
	 * Initialize plugin updater.
	 *
	 * @access public
	 * @static
	 */
	public static function updater() {

		// Get license key.
		$license_key = trim( fg_fillablepdfs()->get_plugin_setting( 'license_key' ) );

		new ForGravity\Fillable_PDFs\EDD_SL_Plugin_Updater(
			FG_EDD_STORE_URL,
			__FILE__,
			[
				'version' => FG_FILLABLEPDFS_VERSION,
				'license' => $license_key,
				'item_id' => FG_FILLABLEPDFS_EDD_ITEM_ID,
				'author'  => 'ForGravity',
			]
		);

	}

}

/**
 * Returns an instance of the Fillable_PDFs class
 *
 * @since 1.0
 *
 * @return ForGravity\Fillable_PDFs\Fillable_PDFs|ForGravity\Fillable_PDFs\Legacy\Fillable_PDFs
 */
function fg_fillablepdfs() {

	// If running on Gravity Forms 2.4.x, run legacy version.
	if ( ! version_compare( GFCommon::$version, '2.5-dev-1', '>=' ) ) {
		return ForGravity\Fillable_PDFs\Legacy\Fillable_PDFs::get_instance();
	}

	return ForGravity\Fillable_PDFs\Fillable_PDFs::get_instance();

}

/**
 * Returns an instance of the Import class
 *
 * @esince 1.0
 *
 * @return ForGravity\Fillable_PDFs\Import|ForGravity\Fillable_PDFs\Legacy\Import
 */
function fg_fillablepdfs_import() {

	// If running on Gravity Forms 2.4.x, run legacy version.
	if ( ! version_compare( GFCommon::$version, '2.5-dev-1', '>=' ) ) {
		return ForGravity\Fillable_PDFs\Legacy\Import::get_instance();
	}

	return ForGravity\Fillable_PDFs\Import::get_instance();

}

/**
 * Returns an instance of the Server class
 *
 * @since 1.0
 *
 * @return ForGravity\Fillable_PDFs\Server
 */
function fg_fillablepdfs_server() {
	return ForGravity\Fillable_PDFs\Server::get_instance();
}

/**
 * Returns an instance of the Templates class
 *
 * @since 1.0
 *
 * @return ForGravity\Fillable_PDFs\Templates|ForGravity\Fillable_PDFs\Legacy\Templates
 */
function fg_fillablepdfs_templates() {

	// If running on Gravity Forms 2.4.x, run legacy version.
	if ( ! version_compare( GFCommon::$version, '2.5-dev-1', '>=' ) ) {
		return ForGravity\Fillable_PDFs\Legacy\Templates::get_instance();
	}

	return ForGravity\Fillable_PDFs\Templates::get_instance();

}
