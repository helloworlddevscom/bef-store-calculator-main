<?php
/**
 * Plugin Name: Easy Digital Downloads - Authorize.net Gateway
 * Plugin URL: https://easydigitaldownloads.com/downloads/authorize-net-gateway/
 * Description: Adds a payment gateway for Authorize.net
 * Version: 2.0.1
 * Author: Sandhills Development, LLC
 * Author URI: https://sandhillsdev.com
 * Textdomain: edda
 * Contributors: mordauk, easydigitaldownloads
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * The main plugin requirements checker
 *
 * @since 1.0
 */
final class EDD_Authorize_Net_Requirements_Check {

	/**
	 * Plugin file
	 *
	 * @since 1.0
	 * @var string
	 */
	private $file = '';

	/**
	 * Plugin basename
	 *
	 * @since 1.0
	 * @var string
	 */
	private $base = '';

	/**
	 * Requirements array
	 *
	 * @todo Extend WP_Dependencies
	 * @var array
	 * @since 1.0
	 */
	private $requirements = array(

		// PHP
		'php' => array(
			'minimum' => '5.6.0',
			'name'    => 'PHP',
			'exists'  => true,
			'current' => false,
			'checked' => false,
			'met'     => false
		),

		// WordPress
		'wp' => array(
			'minimum' => '4.4.0',
			'name'    => 'WordPress',
			'exists'  => true,
			'current' => false,
			'checked' => false,
			'met'     => false
		),

		// Easy Digital Downloads
		'easy-digital-downloads' => array(
			'minimum' => '2.8',
			'name'    => 'Easy Digital Downloads',
			'exists'  => false,
			'current' => false,
			'checked' => false,
			'met'     => false
		)
	);

	/**
	 * Setup plugin requirements
	 *
	 * @since 1.0
	 */
	public function __construct() {

		// Setup file & base
		$this->setup_constants();
		$this->file = __FILE__;
		$this->base = plugin_basename( $this->file );

		// Always load translations
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );

		// Load or quit
		$this->met()
			? $this->load()
			: $this->quit();
	}

	/**
	 * Setup plugin constants.
	 *
	 * @access private
	 * @since 1.0.0
	 * @return void
	 */
	private function setup_constants() {

		// Plugin version.
		if ( ! defined( 'EDDA_VERSION' ) ) {
			define( 'EDDA_VERSION', '2.0.1' );
		}

		// Plugin Root File.
		if ( ! defined( 'EDDA_PLUGIN_FILE' ) ) {
			define( 'EDDA_PLUGIN_FILE', __FILE__ );
		}

		// Plugin Base Name.
		if ( ! defined( 'EDDA_PLUGIN_BASE' ) ) {
			define( 'EDDA_PLUGIN_BASE', plugin_basename( EDDA_PLUGIN_FILE ) );
		}

		// Plugin Folder Path.
		if ( ! defined( 'EDDA_PLUGIN_DIR' ) ) {
			define( 'EDDA_PLUGIN_DIR', plugin_dir_path( EDDA_PLUGIN_FILE ) );
		}

		// Plugin Folder URL.
		if ( ! defined( 'EDDA_PLUGIN_URL' ) ) {
			define( 'EDDA_PLUGIN_URL', plugin_dir_url( EDDA_PLUGIN_FILE ) );
		}

	}

	/**
	 * Quit without loading
	 *
	 * @since 1.0
	 */
	private function quit() {
		add_action( 'admin_head',                        array( $this, 'admin_head'        ) );
		add_action( "after_plugin_row_{$this->base}",    array( $this, 'plugin_row_notice' ) );
	}

	/** Specific Methods ******************************************************/

	/**
	 * Load normally
	 *
	 * @since 1.0
	 */
	private function load() {

		// Maybe include the bundled bootstrapper
		if ( ! class_exists( 'Easy_Digital_Downloads_Authorize_Net' ) ) {
			require_once dirname( $this->file ) . '/init.php';
		}

		// Maybe hook-in the bootstrapper
		if ( class_exists( 'Easy_Digital_Downloads_Authorize_Net' ) ) {

			// Bootstrap to plugins_loaded before priority 101 because Recurring Payments waits until 100
			add_action( 'plugins_loaded', array( $this, 'bootstrap' ), 101 );

			// Register the activation hook
			register_activation_hook( $this->file, array( $this, 'install' ) );
		}
	}

	/**
	 * Install, usually on an activation hook.
	 *
	 * @since 1.0
	 */
	public function install() {

		// Bootstrap to include all of the necessary files
		$this->bootstrap();

		// Network wide?
		$network_wide = ! empty( $_GET['networkwide'] )
			? (bool) $_GET['networkwide']
			: false;

		// Call the installer directly during the activation hook
		edd_install( $network_wide );
	}

	/**
	 * Bootstrap everything.
	 *
	 * @since 1.0
	 */
	public function bootstrap() {
		Easy_Digital_Downloads_Authorize_Net::instance( $this->file );
	}

	/**
	 * Plugin specific URL for an external requirements page.
	 *
	 * @since 1.0
	 * @return string
	 */
	private function unmet_requirements_url() {
		return '';
	}

	/**
	 * Plugin specific text to quickly explain what's wrong.
	 *
	 * @since 1.0
	 * @return string
	 */
	private function unmet_requirements_text() {
		esc_html_e( 'This plugin is not fully active.', 'edda' );
	}

	/**
	 * Plugin specific text to describe a single unmet requirement.
	 *
	 * @since 1.0
	 * @return string
	 */
	private function unmet_requirements_description_text() {
		return esc_html__( 'Requires %s (%s), but (%s) is installed.', 'edda' );
	}

	/**
	 * Plugin specific text to describe a single missing requirement.
	 *
	 * @since 1.0
	 * @return string
	 */
	private function unmet_requirements_missing_text() {
		return esc_html__( 'Requires %s (%s), but it appears to be missing.', 'edda' );
	}

	/**
	 * Plugin specific text used to link to an external requirements page.
	 *
	 * @since 1.0
	 * @return string
	 */
	private function unmet_requirements_link() {
		return esc_html__( 'Requirements', 'edda' );
	}

	/**
	 * Plugin specific aria label text to describe the requirements link.
	 *
	 * @since 1.0
	 * @return string
	 */
	private function unmet_requirements_label() {
		return esc_html__( 'Easy Digital Downloads Authorize.net Checkout Requirements', 'edda' );
	}

	/**
	 * Plugin specific text used in CSS to identify attribute IDs and classes.
	 *
	 * @since 1.0
	 * @return string
	 */
	private function unmet_requirements_name() {
		return 'eddppc-requirements';
	}

	/** Agnostic Methods ******************************************************/

	/**
	 * Plugin agnostic method to output the additional plugin row
	 *
	 * @since 1.0
	 */
	public function plugin_row_notice() {
		?><tr class="active <?php echo esc_attr( $this->unmet_requirements_name() ); ?>-row">
		<th class="check-column">
			<span class="dashicons dashicons-warning"></span>
		</th>
		<td class="column-primary">
			<?php $this->unmet_requirements_text(); ?>
		</td>
		<td class="column-description">
			<?php $this->unmet_requirements_description(); ?>
		</td>
		</tr><?php
	}

	/**
	 * Plugin agnostic method used to output all unmet requirement information
	 *
	 * @since 1.0
	 */
	private function unmet_requirements_description() {
		foreach ( $this->requirements as $properties ) {
			if ( empty( $properties['met'] ) ) {
				$this->unmet_requirement_description( $properties );
			}
		}
	}

	/**
	 * Plugin agnostic method to output specific unmet requirement information
	 *
	 * @since 1.0
	 * @param array $requirement
	 */
	private function unmet_requirement_description( $requirement = array() ) {

		// Requirement exists, but is out of date
		if ( ! empty( $requirement['exists'] ) ) {
			$text = sprintf(
				$this->unmet_requirements_description_text(),
				'<strong>' . esc_html( $requirement['name']    ) . '</strong>',
				'<strong>' . esc_html( $requirement['minimum'] ) . '</strong>',
				'<strong>' . esc_html( $requirement['current'] ) . '</strong>'
			);

			// Requirement could not be found
		} else {
			$text = sprintf(
				$this->unmet_requirements_missing_text(),
				'<strong>' . esc_html( $requirement['name']    ) . '</strong>',
				'<strong>' . esc_html( $requirement['minimum'] ) . '</strong>'
			);
		}

		// Output the description
		echo '<p>' . $text . '</p>';
	}

	/**
	 * Plugin agnostic method to output unmet requirements styling
	 *
	 * @since 1.0
	 */
	public function admin_head() {

		// Get the requirements row name
		$name = $this->unmet_requirements_name(); ?>

		<style id="<?php echo esc_attr( $name ); ?>">
			.plugins tr[data-plugin="<?php echo esc_html( $this->base ); ?>"] th,
			.plugins tr[data-plugin="<?php echo esc_html( $this->base ); ?>"] td,
			.plugins .<?php echo esc_html( $name ); ?>-row th,
			.plugins .<?php echo esc_html( $name ); ?>-row td {
				background: #fff5f5;
			}
			.plugins tr[data-plugin="<?php echo esc_html( $this->base ); ?>"] th {
				box-shadow: none;
			}
			.plugins .<?php echo esc_html( $name ); ?>-row th span {
				margin-left: 6px;
				color: #dc3232;
			}
			.plugins tr[data-plugin="<?php echo esc_html( $this->base ); ?>"] th,
			.plugins .<?php echo esc_html( $name ); ?>-row th.check-column {
				border-left: 4px solid #dc3232 !important;
			}
			.plugins .<?php echo esc_html( $name ); ?>-row .column-description p {
				margin: 0;
				padding: 0;
			}
			.plugins .<?php echo esc_html( $name ); ?>-row .column-description p:not(:last-of-type) {
				margin-bottom: 8px;
			}
		</style>
		<?php
	}

	/** Checkers **************************************************************/

	/**
	 * Plugin specific requirements checker
	 *
	 * @since 1.0
	 */
	private function check() {

		// Condintially add Recurring Payments requirements
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		$recurring_active = is_plugin_active( 'edd-recurring/edd-recurring.php' );
		if ( $recurring_active ) {
			$this->requirements['edd-recurring'] = array(
				'minimum' => '2.9.6',
				'name'    => 'Easy Digital Downloads - Recurring Payments',
				'exists'  => false,
				'current' => false,
				'checked' => false,
				'met'     => false,
			);
		}

		// Loop through requirements
		foreach ( $this->requirements as $dependency => $properties ) {

			// Which dependency are we checking?
			switch ( $dependency ) {

				// PHP
				case 'php' :
					$version = phpversion();
					break;

				// WP
				case 'wp' :
					$version = get_bloginfo( 'version' );
					break;

				// Easy Digital Downloads
				case 'easy-digital-downloads' :
					$version = defined( 'EDD_VERSION' ) ? EDD_VERSION : false;
					$exists  = defined( 'EDD_VERSION' ) ? true : false;
					break;


				// Recurring Payments
				case 'edd-recurring' :
					$recurring_data = get_plugin_data( WP_PLUGIN_DIR . '/edd-recurring/edd-recurring.php' );
					$version        = ! empty( $recurring_data['Version'] ) ? $recurring_data['Version'] : false;
					$exists         = ! empty( $recurring_data['Version'] ) ? true : false;
					break;

				// Unknown
				default :
					$version = false;
					break;
			}

			// Merge to original array
			if ( ! empty( $version ) ) {
				$this->requirements[ $dependency ] = array_merge( $this->requirements[ $dependency ], array(
					'current' => $version,
					'checked' => true,
					'met'     => version_compare( $version, $properties['minimum'], '>=' ),
					'exists'  => isset( $exists ) ? $exists : $this->requirements[ $dependency ]['exists'],
				) );
			}

		}


	}

	/**
	 * Have all requirements been met?
	 *
	 * @since 1.0
	 *
	 * @return boolean
	 */
	public function met() {

		// Run the check
		$this->check();

		// Default to true (any false below wins)
		$retval  = true;
		$to_meet = wp_list_pluck( $this->requirements, 'met' );

		// Look for unmet dependencies, and exit if so
		foreach ( $to_meet as $met ) {
			if ( empty( $met ) ) {
				$retval = false;
				continue;
			}
		}

		// Return
		return $retval;
	}

	/** Translations **********************************************************/

	/**
	 * Plugin specific text-domain loader.
	 *
	 * @since 1.4
	 * @return void
	 */
	public function load_textdomain() {

		/*
		 * Due to the introduction of language packs through translate.wordpress.org,
		 * loading our textdomain is complex.
		 *
		 *
		 * We must look for translation files in several places and under several names.
		 *
		 * - wp-content/languages/plugins/edd-authorize-net (introduced with language packs)
		 * - wp-content/plugins/edd-authorize-net/languages/
		 *
		 * In wp-content/languages/edd-authorize-net/ we look for:
		 * - "edd-authorize-net-{lang}_{country}.mo"
		 *
		 * In wp-content/languages/plugins/edd-authorize-net/ we look for:
		 * - "edd-authorize-net-{lang}_{country}.mo"
		 *
		 */

		// Set filter for plugin's languages directory.
		$edd_lang_dir = dirname( $this->base ) . '/languages/';
		$get_locale   = function_exists( 'get_user_locale' )
			? get_user_locale()
			: get_locale();

		/**
		 * Defines the plugin language locale used in Easy Digital Downloads - Authorize.net Checkout.
		 *
		 * @var $get_locale The locale to use. Uses get_user_locale()` in WordPress 4.7 or greater,
		 *                  otherwise uses `get_locale()`.
		 */
		$locale = apply_filters( 'plugin_locale', $get_locale, 'edd-authorize-net' );
		$mofile = sprintf( '%1$s-%2$s.mo', 'edd-authorize-net', $locale );

		// Look in wp-content/languages/plugins/edd-authorize-net
		$mofile_global1 = WP_LANG_DIR . "/plugins/edd-authorize-net/{$mofile}";

    // Look in wp-content/languages/edd-authorize-net
		$mofile_global2 = WP_LANG_DIR . "/edd-authorize-net/{$mofile}";

		// Try to load from first global location
		if ( file_exists( $mofile_global1 ) ) {
			load_textdomain( 'edda', $mofile_global1 );

			// Try to load from next global location
		} elseif ( file_exists( $mofile_global2 ) ) {
			load_textdomain( 'edda', $mofile_global2 );

			// Load the default language files
		} else {
			load_plugin_textdomain( 'edda', false, $edd_lang_dir );
		}
	}

}

/**
 * Load the requirements checker.
 *
 * @since 2.0.1
 * @return void
 */
function edd_authorizenet_load_requirements_check() {
	new EDD_Authorize_Net_Requirements_Check();
}
add_action( 'plugins_loaded', 'edd_authorizenet_load_requirements_check' );
