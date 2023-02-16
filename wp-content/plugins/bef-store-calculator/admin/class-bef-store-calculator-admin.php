<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://www.helloworlddevs.com
 * @since      1.0.0
 *
 * @package    bef_store_calculator
 * @subpackage bef_store_calculator/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    bef_store_calculator
 * @subpackage bef_store_calculator/admin
 * @author     info <http://www.helloworlddevs.com>
 */
class bef_store_calculator_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $bef_store_calculator    The ID of this plugin.
	 */
	private $bef_store_calculator;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $bef_store_calculator       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $bef_store_calculator, $version ) {

		$this->bef_store_calculator = $bef_store_calculator;
		$this->version = $version;

        $this->load_dependencies();
    }

	private function load_dependencies() {
        /**
         * The class responsible for defining any calculator admin display page information
         * Load any additional classes that are needed for admin functionality
         *
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-bef-store-calculator-mainPage.php';
    }

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in bef_store_calculator_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The bef_store_calculator_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->bef_store_calculator, plugin_dir_url( __FILE__ ) . 'css/bef-store-calculator-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
         * It is only a single file that is called from the Loader class.
		 *
		 * An instance of this class is passed to the run() function
		 * defined in bef_store_calculator_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The bef_store_calculator_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->bef_store_calculator, plugin_dir_url( __FILE__ ) . 'js/bef-store-calculator-admin.js', array( 'jquery' ), $this->version, false );

	}


    /**
     *  Adds hook where the admin menu is loaded.
     */
    public function bef_calculator_store_setup_menu() {

        add_menu_page('BEF Calculator','BEF Calculator','manage_options','bef-store-calculator', 'bef_store_calculator_mainPage::bef_store_display_page');
    }

}
