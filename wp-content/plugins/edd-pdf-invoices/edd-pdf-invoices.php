<?php
/**
 * Plugin Name: Easy Digital Downloads - PDF Invoices
 * Plugin URL: https://easydigitaldownloads.com/downloads/pdf-invoices/
 * Description: Creates PDF Invoices for each purchase available to both admins and customers
 * Author: Sandhills Development, LLC
 * Author URI: https://sandhillsdev.com/
 * Version: 2.2.28
 * Requires at least: 4.0
 * Tested up to: 5.5.2
 *
 * Text Domain: eddpdfi
 * Domain Path: languages
 *
 * Copyright 2020 Sandhills Development, LLC
 *
 * @package  EDD_PDF_Invoices
 * @category Core
 * @author   Sandhills Development, LLC
 * @version  2.2.28
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'EDD_PDF_Invoices' ) ) :

/**
 * EDD_PDF_Invoices Class
 *
 * @package EDD_PDF_Invoices
 * @since   2.1
 * @version 2.1
 * @author  Sunny Ratilal
 */
final class EDD_PDF_Invoices {
	/**
	 * Holds the instance
	 *
	 * Ensures that only one instance of EDD Reviews exists in memory at any one
	 * time and it also prevents needing to define globals all over the place.
	 *
	 * TL;DR This is a static property property that holds the singleton instance.
	 *
	 * @var object
	 * @static
	 * @since 1.0
	 */
	private static $instance;

	/**
	 * Boolean whether or not to use the singleton, comes in handy
	 * when doing testing
	 *
	 * @var bool
	 * @static
	 * @since 1.0
	 */
	public static $testing = false;

	/**
	 * Holds the version number
	 *
	 * @var string
	 * @since 1.0
	 */
	public $version = '2.2.28';

	/**
	 * Get the instance and store the class inside it. This plugin utilises
	 * the PHP singleton design pattern.
	 *
	 * @since 1.0
	 * @static
	 * @staticvar array $instance
	 * @access public
	 * @see edd_pdf_invoices();
	 * @uses EDD_PDF_Invoices::includes() Loads all the classes
	 * @uses EDD_PDF_Invoices::hooks() Setup hooks and actions
	 * @return object self::$instance Instance
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof EDD_PDF_Invoices ) || self::$testing ) {
			self::$instance = new EDD_PDF_Invoices;
			self::$instance->setup_globals();

			$requirements_met = self::$instance->requirements_met();

			if ( ! $requirements_met ) {
				add_action( 'admin_notices', array( self::$instance, 'admin_notices' ) );
			} else {
				self::$instance->includes();
				self::$instance->hooks();
				self::$instance->licensing();
			}
		}

		return self::$instance;
	}

	/**
	 * Throw error on object clone
	 *
	 * The whole idea of the singleton design pattern is that there is a single
	 * object therefore, we don't want the object to be cloned.
	 *
	 * @since 1.6
	 * @access protected
	 * @return void
	 */
	public function __clone() {
		// Cloning instances of the class is forbidden
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'eddpdfi' ), '1.6' );
	}

	/**
	 * Disable unserializing of the class
	 *
	 * @since 1.6
	 * @access protected
	 * @return void
	 */
	public function __wakeup() {
		// Unserializing instances of the class is forbidden
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'eddpdfi' ), '1.6' );
	}

	/*--------------------------------------------*
	 * Constructor
	 *--------------------------------------------*/

	/**
	 * Constructor Function
	 *
	 * @since 1.0
	 * @access protected
	 * @see EDD_PDF_Invoices::init()
	 * @see EDD_PDF_Invoices::activation()
	 */
	public function __construct() {
		self::$instance = $this;

		add_action( 'init', array( $this, 'init' ), -1 );
	}

	/**
	 * Reset the instance of the class
	 *
	 * @since 1.0
	 * @access public
	 * @static
	 */
	public static function reset() {
		self::$instance = null;
	}

	public static function requirements_met() {
		$edd = class_exists( 'Easy_Digital_Downloads' );
		$edd_version = defined( 'EDD_VERSION' ) && version_compare( EDD_VERSION, '1.7', '>' );
		return $edd && $edd_version;
	}

	public function setup_globals() {
		/**
		 * Define Plugin Directory
		 *
		 * @since 1.0
		 */
		if ( ! defined( 'EDDPDFI_PLUGIN_DIR' ) ) define( 'EDDPDFI_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

		/**
		 * Define Plugin URL
		 *
		 * @since 1.0
		 */
		if ( ! defined( 'EDDPDFI_PLUGIN_URL' ) ) define( 'EDDPDFI_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

		/**
		 * Define Plugin File Name
		 *
		 * @since 1.0
		 */
		if ( ! defined( 'EDDPDFI_PLUGIN_FILE' ) )  define( 'EDDPDFI_PLUGIN_FILE', __FILE__ );

		/**
		 * Software Licensing
		 *
		 * Integrates the plugin with the Easy Digital Downloads Software Licensing
		 * Add-On in order to streamline the update process.
		 *
		 * @since 1.0
		 */
		if ( ! defined( 'EDDPDFI_STORE_URL' ) ) define( 'EDDPDFI_STORE_URL', 'http://easydigitaldownloads.com' );

		if ( ! defined( 'EDDPDFI_ITEM_NAME' ) ) define( 'EDDPDFI_ITEM_NAME', 'PDF Invoices' );

		$this->file        = __FILE__;
		$this->basename    = apply_filters( 'edd_pdfi_plugin_basenname', plugin_basename( $this->file ) );
		$this->plugin_url  = plugin_dir_url( __FILE__ );
		$this->plugin_path = plugin_dir_path( __FILE__ );
		$this->lang_dir    = apply_filters( 'edd_pdfi_lang_dir', trailingslashit( $this->plugin_path . 'languages' ) );
	}

	/**
	 * Function fired on init
	 *
	 * This function is called on WordPress 'init'. It's triggered from the
	 * constructor function.
	 *
	 * @since 1.0
	 * @access public
	 *
	 * @uses EDD_PDF_Invoices::load_plugin_textdomain()
	 *
	 * @return void
	 */
	public function init() {
		do_action( 'eddpdfi_before_init' );

		$this->load_plugin_textdomain();

		do_action( 'eddpdfi_after_init' );
	}

	/**
	 * Includes
	 *
	 * @since 1.0
	 * @access private
	 * @return void
	 */
	private function includes() {
		require_once( EDDPDFI_PLUGIN_DIR . 'includes/templates/template-blue-stripe.php' );
		require_once( EDDPDFI_PLUGIN_DIR . 'includes/templates/template-colors.php' );
		require_once( EDDPDFI_PLUGIN_DIR . 'includes/templates/template-default.php' );
		require_once( EDDPDFI_PLUGIN_DIR . 'includes/templates/template-lines.php' );
		require_once( EDDPDFI_PLUGIN_DIR . 'includes/templates/template-minimal.php' );
		require_once( EDDPDFI_PLUGIN_DIR . 'includes/templates/template-traditional.php' );

		do_action( 'eddpdfi_load_templates' );

		require_once( EDDPDFI_PLUGIN_DIR . 'includes/email-template-tag.php' );
		require_once( EDDPDFI_PLUGIN_DIR . 'includes/email-templates.php' );
		require_once( EDDPDFI_PLUGIN_DIR . 'includes/i18n.php' );
		require_once( EDDPDFI_PLUGIN_DIR . 'includes/settings.php' );
		require_once( EDDPDFI_PLUGIN_DIR . 'includes/template-functions.php' );
	}

	/**
	 * Hooks
	 */
	public function hooks() {
		add_action( 'edd_purchase_history_header_after', array( $this, 'purchase_history_header' )        );
		add_action( 'edd_purchase_history_row_end',      array( $this, 'purchase_history_link'   ), 10, 2 );
		add_action( 'edd_generate_pdf_invoice',          array( $this, 'generate_pdf_invoice'    )        );
		add_action( 'edd_payment_receipt_after',         array( $this, 'receipt_shortcode_link'  ), 10    );

		if ( version_compare( EDD_VERSION, '3.0.0-beta-1.0012', '>=' ) ) {
			add_filter( 'edd_order_row_actions', array( $this, 'invoice_link' ), 10, 2 );
		} else {
			add_filter( 'edd_payment_row_actions', array( $this, 'invoice_link' ), 10, 2 );
		}
	}

	/**
	 * Implement EDD Licensing
	 */
	private function licensing() {
		if( class_exists( 'EDD_License' ) ) {
			$license = new EDD_License( __FILE__, 'PDF Invoices', $this->version, 'Sandhills Development, LLC', null, null, 6175 );
		}
	}

	/**
	 * Load Plugin Text Domain
	 *
	 * Looks for the plugin translation files in certain directories and loads
	 * them to allow the plugin to be localised
	 *
	 * @since 1.0
	 * @access public
	 * @return bool True on success, false on failure
	 */
	public function load_plugin_textdomain() {
		// Traditional WordPress plugin locale filter
		$locale = apply_filters( 'plugin_locale',  get_locale(), 'eddpdfi' );
		$mofile = sprintf( '%1$s-%2$s.mo', 'eddpdfi', $locale );

		// Setup paths to current locale file
		$mofile_local  = trailingslashit( plugin_dir_path( __FILE__ ) . 'languages' ) . $mofile;

		if ( file_exists( $mofile_local ) ) {
			// Look in the /wp-content/plugins/edd-pdf-invoices/languages/ folder
			load_textdomain( 'eddpdfi', $mofile_local );
		} else {
			// Load the default language files
			load_plugin_textdomain( 'eddpdfi', false, trailingslashit( plugin_dir_path( __FILE__ ) . 'languages' ) );
		}

		return false;
	}

	/**
	 * Handles the displaying of any notices in the admin area
	 *
	 * @since 1.0
	 * @access public
	 * @return void
	 */
	public static function admin_notices() {
		global $edd_options;

		if ( ! class_exists( 'Easy_Digital_Downloads' ) ) {

			echo '<div class="error"><p>' . sprintf( __( 'You must install and activate %sEasy Digital Downloads%s for the PDF Invoices Add-On to work.', 'eddpdfi' ), '<a href="http://easydigitaldownloads.com" title="Easy Digital Downloads">', '</a>' ) . '</p></div>';

		} else {

			$edd_plugin_data = get_plugin_data( WP_PLUGIN_DIR . '/easy-digital-downloads/easy-digital-downloads.php', false, false );
			$is_edd_settings = ( isset( $_GET['page'] ) && $_GET['page'] == 'edd-settings' ) ? true : false;

			if ( $edd_plugin_data['Version'] < '1.7' ) {
				echo '<div class="error"><p>' . __( 'The Easy Digital Downloads PDF Invoices plugin requires at least Easy Digital Downloads Version 1.7. Please update Easy Digital Downloads for the PDF Invoices Add-On to work correctly.', 'eddpdfi' ) . '</p></div>';
			}

			if ( ! isset ( $edd_options['eddpdfi_templates'] ) && ! $is_edd_settings ) {
				echo '<div class="updated"><p>' . sprintf( __( 'Please visit the %sPDF Invoice Settings%s to configure the plugin. Currently the settings have not been configured correctly therefore you will may issues when trying to generate invoices.', 'eddpdfi' ), '<a href="edit.php?post_type=download&page=edd-settings&tab=misc">', '</a>' ) . '</p></div>';
			}

		}
	}

	/**
	 * Creates Link to Download Invoice
	 *
	 * Creates a link on the Payment History admin page for each payment to
	 * allow the ability to download an invoice for that payment
	 *
	 * @since 1.0
	 *
	 * @param array $row_actions All the row actions on the Payment History page
	 * @param object $eddpdfi_payment Payment object containing all the payment data
	 *
	 * @return array Modified row actions with Download Invoice link
	*/
	public function invoice_link( $row_actions, $eddpdfi_payment ) {
		$row_actions_pdf_invoice_link = array( );

		$eddpdfi_generate_invoice_nonce = wp_create_nonce( 'eddpdfi_generate_invoice' );

		if ( $this->is_invoice_link_allowed( $eddpdfi_payment->ID ) ) {
			$invoice_url = wp_nonce_url( edd_pdf_invoices()->get_pdf_invoice_url( $eddpdfi_payment->ID, admin_url() ), 'eddpdfi_generate_invoice' );

			$row_actions_pdf_invoice_link = array(
				'invoice' => '<a href="' . esc_url( $invoice_url ) . '">' . __( 'Download Invoice', 'eddpdfi' ) . '</a>',
			);
		}

		return array_merge( $row_actions, $row_actions_pdf_invoice_link );
	}

	/**
	 * Purhcase History Page Table Heading
	 *
	 * Appends to the table header (<thead>) on the Purchase History page for the
	 * Invoice column to be displayed
	 *
	 * @since 1.0
	 */
	function purchase_history_header() {
		echo '<th class="edd_invoice">' . __( 'Invoice', 'eddpdfi' ) . '</th>';
	}

	/**
	 * Outputs the Invoice link
	 *
	 * Adds the invoice link to the [purchase_history] shortcode underneath the
	 * previously created Invoice header
	 *
	 * @since 1.0
	 *
	 * @param int   $payment_id    Payment ID
	 * @param array $purchase_data All the purchase data
	 */
	function purchase_history_link( $payment_id, $purchase_data ) {
		if ( ! $this->is_invoice_link_allowed( $payment_id ) ) {
			echo '<td>-</td>';
			return;
		}
		?>
		<td class="edd_invoice">
			<?php $this->render_download_invoice_link( $payment_id ); ?>
		</td>
		<?php
	}

	/**
	 * Receipt Shortcode Invoice Link
	 *
	 * Adds the invoice link to the [edd_receipt] shortcode
	 *
	 * @since 1.0.4
	 *
	 * @param object $payment All the payment data
	 */
	public function receipt_shortcode_link( $payment ) {
		if ( ! $this->is_invoice_link_allowed( $payment->ID ) ) {
			return;
		}
		?>
		<tr>
			<td><strong><?php _e( 'Invoice', 'eddpdfi' ); ?>:</strong></td>
			<td>
				<?php $this->render_download_invoice_link( $payment->ID ); ?>
			</td>
		</tr>
		<?php
	}

	/**
	 * Renders the "Download Invoice" HTML for a given payment.
	 *
	 * @param int $payment_id Payment ID
	 *
	 * @since 2.2.28
	 * @return void
	 */
	private function render_download_invoice_link( $payment_id ) {
		?>
		<a class="edd_invoice_link" href="<?php echo esc_url( edd_pdf_invoices()->get_pdf_invoice_url( $payment_id ) ); ?>">
			<?php _e( 'Download Invoice', 'eddpdfi' ); ?>
			<span class="screen-reader-text"><?php echo esc_html( sprintf( __( 'for order %s', 'eddpdfi' ), edd_get_payment_number( $payment_id ) ) ); ?></span>
		</a>
		<?php
	}

	/**
	 * Gets the Invoice URL
	 *
	 * Generates an invoice URL and adds the necessary query arguments
	 *
	 * @since 1.0
	 *
	 * @param int $payment_id Payment ID
	 * @param string $base_url Base to use for the invoice URL. Defaults to home URL.
	 *
	 * @return string $invoice Invoice URL
	 */
	public function get_pdf_invoice_url( $payment_id, $base_url = '' ) {
		if ( empty( $base_url ) ) {
			$base_url = home_url();
		}

		$eddpdfi_params = array(
			'edd_action'   => 'generate_pdf_invoice',
			'purchase_id'  => urlencode( $payment_id ),
			'email'        => urlencode( edd_get_payment_user_email( $payment_id ) ),
			'purchase_key' => urlencode( edd_get_payment_key( $payment_id ) ),
		);

		return add_query_arg( $eddpdfi_params, $base_url );
	}

	/**
	 * Verify Invoice Link
	 *
	 * Verifies the invoice link submitted from the front-end
	 *
	 * @since 1.0
	*/
	public function verify_invoice_link() {
		if ( ! isset( $_GET['purchase_id'] ) || ! isset( $_GET['email'] ) || ! isset( $_GET['purchase_key'] ) ) {
			return false;
		}

		$purchase_id = absint( $_GET['purchase_id'] );

		if ( ! $this->is_invoice_link_allowed( $purchase_id ) ) {
			return false;
		}

		$key   = sanitize_text_field( $_GET['purchase_key'] );
		$email = sanitize_email( $_GET['email'] );

		$meta_query = array(
			'relation'  => 'AND',
			array(
				'key'   => '_edd_payment_purchase_key',
				'value' => $key
			),
			array(
				'key'   => '_edd_payment_user_email',
				'value' => $email
			)
		);

		$args = array(
			'meta_query' => $meta_query,
		);

		$payments_query = new EDD_Payments_Query( $args );
		$payments = $payments_query->get_payments();

		if ( $payments ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Generate PDF Invoice
	 *
	 * Loads and stores all of the data for the payment.  The HTML2PDF class is
	 * instantiated and do_action() is used to call the invoice template which goes
	 * ahead and renders the invoice.
	 *
	 * @since 1.0
	 * @uses HTML2PDF
	 * @uses wp_is_mobile()
	*/
	public function generate_pdf_invoice() {
		global $edd_options;

		// Verify that the link is what we are expecting
		$verified_link = $this->verify_invoice_link();

		// If the link is not verified, kill the page with an error.
		if ( ! $verified_link ) {
			wp_die( __( 'The invoice that you requested was not found.', 'eddpdfi' ), __( 'Invoice Not Found', 'eddpdfi' ) );
		}

		include_once( EDDPDFI_PLUGIN_DIR . '/tcpdf/tcpdf.php' );
		include_once( EDDPDFI_PLUGIN_DIR . '/includes/EDD_PDF_Invoice.php' );

		$purchase_id = absint( $_GET['purchase_id'] );

		do_action( 'edd_pdfi_generate_pdf_invoice', $purchase_id );

		$eddpdfi_invoice_nonce = isset( $_GET['_wpnonce'] ) ? sanitize_text_field( $_GET['_wpnonce'] ) : null;

		if ( is_admin() && wp_verify_nonce( $eddpdfi_invoice_nonce, 'eddpdfi_generate_invoice' ) ) {
			$eddpdfi_payment         = edd_get_payment( $purchase_id );
			$eddpdfi_payment_meta    = $eddpdfi_payment->payment_meta;
			$eddpdfi_buyer_info      = $eddpdfi_payment_meta[ 'user_info' ];
			$eddpdfi_payment_gateway = $eddpdfi_payment->gateway;
			$eddpdfi_payment_method  = edd_get_gateway_admin_label( $eddpdfi_payment_gateway );

			$company_name = isset( $edd_options['eddpdfi_company_name'] ) ? apply_filters( 'eddpdfi_company_name', $edd_options['eddpdfi_company_name'] ) : '';

			$eddpdfi_payment_date = date_i18n( get_option( 'date_format' ), strtotime( $eddpdfi_payment->date ) );
			$eddpdfi_payment_status = edd_get_payment_status( $eddpdfi_payment, true );

			// WPML Support
			if ( defined( 'ICL_SITEPRESS_VERSION' ) ) {
				$lang = ! empty( $eddpdfi_payment->payment_meta['wpml_language'] ) ? $eddpdfi_payment->payment_meta['wpml_language'] : false;
				if ( ! empty( $lang ) ) {
					global $sitepress;
					$sitepress->switch_lang( $lang );
				}
			}

			$eddpdfi_pdf = new EDD_PDF_Invoice( 'P', 'mm', 'A4', true, 'UTF-8', false );
			$eddpdfi_pdf->SetDisplayMode( 'real' );
			$eddpdfi_pdf->setJPEGQuality( 100 );

			$eddpdfi_pdf->SetTitle( __( 'Invoice ' . eddpdfi_get_payment_number( $eddpdfi_payment->ID ), 'eddpdfi' ) );
			$eddpdfi_pdf->SetCreator( __( 'Easy Digital Downloads', 'eddpdfi' ) );
			$eddpdfi_pdf->SetAuthor( get_option( 'blogname' ) );

			$address_line_2_line_height = isset( $edd_options['eddpdfi_address_line2'] ) ? 6 : 0;

			if ( ! isset( $edd_options['eddpdfi_templates'] ) ) {
				$edd_options['eddpdfi_templates'] = 'default';
			}

			do_action( 'eddpdfi_pdf_template_' . $edd_options['eddpdfi_templates'], $eddpdfi_pdf, $eddpdfi_payment, $eddpdfi_payment_meta, $eddpdfi_buyer_info, $eddpdfi_payment_gateway, $eddpdfi_payment_method, $address_line_2_line_height, $company_name, $eddpdfi_payment_date, $eddpdfi_payment_status );

			if( ob_get_length() ) {
				ob_end_clean();
			}

			if ( wp_is_mobile() ) {
				$eddpdfi_pdf->Output( apply_filters( 'eddpdfi_invoice_filename_prefix', 'Invoice-' ) . eddpdfi_get_payment_number( $eddpdfi_payment->ID ) . '.pdf', apply_filters( 'eddpdfi_invoice_destination', 'I' ) );
			} else {
				$eddpdfi_pdf->Output( apply_filters( 'eddpdfi_invoice_filename_prefix', 'Invoice-' ) . eddpdfi_get_payment_number( $eddpdfi_payment->ID ) . '.pdf', apply_filters( 'eddpdfi_invoice_destination', 'I' ) );
			}
		} else if ( isset( $_GET['purchase_id'] )  && isset( $_GET['email'] ) && isset( $_GET['purchase_key'] ) ) {
			$eddpdfi_payment = edd_get_payment( $purchase_id );
			$eddpdfi_payment_meta = edd_get_payment_meta( $purchase_id );
			$eddpdfi_buyer_info = edd_get_payment_meta_user_info( $eddpdfi_payment->ID );
			$eddpdfi_payment_gateway = edd_get_payment_gateway( $eddpdfi_payment->ID );
			$eddpdfi_payment_method = edd_get_gateway_admin_label( $eddpdfi_payment_gateway );

			$company_name = isset( $edd_options['eddpdfi_company_name'] ) ? apply_filters( 'eddpdfi_company_name', $edd_options['eddpdfi_company_name'] ) : '';

			$eddpdfi_payment_date = date_i18n( get_option( 'date_format' ), strtotime( $eddpdfi_payment->date ) );
			$eddpdfi_payment_status = edd_get_payment_status( $eddpdfi_payment, true );

			// WPML Support
			if ( defined( 'ICL_SITEPRESS_VERSION' ) ) {
				$lang = ! empty( $eddpdfi_payment->payment_meta['wpml_language'] ) ? $eddpdfi_payment->payment_meta['wpml_language'] : false;
				if ( ! empty( $lang ) ) {
					global $sitepress;
					$sitepress->switch_lang( $lang );
				}
			}

			$eddpdfi_pdf = new EDD_PDF_Invoice( 'P', 'mm', 'A4', true, 'UTF-8', false );
			$eddpdfi_pdf->SetDisplayMode( 'real' );
			$eddpdfi_pdf->setJPEGQuality( 100 );

			$eddpdfi_pdf->SetTitle( __( 'Invoice ' . eddpdfi_get_payment_number( $eddpdfi_payment->ID ), 'eddpdfi' ) );
			$eddpdfi_pdf->SetCreator( __( 'Easy Digital Downloads', 'eddpdfi' ) );
			$eddpdfi_pdf->SetAuthor( get_option( 'blogname' ) );

			$address_line_2_line_height = isset( $edd_options['eddpdfi_address_line2'] ) ? 6 : 0;

			if ( ! isset( $edd_options['eddpdfi_templates'] ) )
				$edd_options['eddpdfi_templates'] = 'default';

			do_action( 'eddpdfi_pdf_template_' . $edd_options['eddpdfi_templates'], $eddpdfi_pdf, $eddpdfi_payment, $eddpdfi_payment_meta, $eddpdfi_buyer_info, $eddpdfi_payment_gateway, $eddpdfi_payment_method, $address_line_2_line_height, $company_name, $eddpdfi_payment_date, $eddpdfi_payment_status );

			if( ob_get_length() ) {
				ob_end_clean();
			}

			if ( wp_is_mobile() ) {
				$eddpdfi_pdf->Output( apply_filters( 'eddpdfi_invoice_filename_prefix', 'Invoice-' ) . eddpdfi_get_payment_number( $eddpdfi_payment->ID ) . '.pdf', apply_filters( 'eddpdfi_invoice_destination', 'I' ) );
			} else {
				$eddpdfi_pdf->Output( apply_filters( 'eddpdfi_invoice_filename_prefix', 'Invoice-' ) . eddpdfi_get_payment_number( $eddpdfi_payment->ID ) . '.pdf', apply_filters( 'eddpdfi_invoice_destination', 'D' ) );
			}
		}

		die(); // Stop the rest of the page from processsing and being sent to the browser
	}

	/**
	 * Check is invoice link is allowed
	 *
	 * @since 2.1.2
	 * @access private
	 * @global $edd_options
	 * @param int $id Payment ID to verify total
	 * @return bool
	 */
	public function is_invoice_link_allowed( $id = null ) {
		global $edd_options;

		$ret = true;

		if ( isset( $edd_options['eddpdfi_disable_invoices_on_free_downloads'] ) && ! is_null( $id ) && ! empty( $id ) ) {
			$amount = edd_get_payment_amount( $id );

			if ( $amount > 0 ) {
				$ret = true;
			} else {
				$ret = false;
			}
		}

		if ( ! edd_is_payment_complete( $id ) ) {
			$ret = false;
		}

		return apply_filters( 'eddpdfi_is_invoice_link_allowed', $ret, $id );
	}
}

/**
 * Loads a single instance of EDD PDF Invoices
 *
 * This follows the PHP singleton design pattern.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * @example <?php $edd_pdf_invoices = edd_pdf_invoices(); ?>
 *
 * @since 1.0
 *
 * @see EDD_PDF_Invoices::get_instance()
 *
 * @return object Returns an instance of the EDD_PDF_Invoices class
 */
function edd_pdf_invoices() {
	return EDD_PDF_Invoices::get_instance();
}

/**
 * Loads plugin after all the others have loaded and have registered their
 * hooks and filters
 */
add_action( 'plugins_loaded', 'edd_pdf_invoices', apply_filters( 'eddpdfi_action_priority', 10 ) );

endif;
