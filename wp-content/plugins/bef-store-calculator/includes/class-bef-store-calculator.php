<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://www.helloworlddevs.com
 * @since      1.0.0
 *
 * @package    bef_store_calculator
 * @subpackage bef_store_calculator/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    bef_store_calculator
 * @subpackage bef_store_calculator/includes
 * @author     info <http://www.helloworlddevs.com>
 */
class bef_store_calculator {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      bef_store_calculator_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $bef_store_calculator    The string used to uniquely identify this plugin.
	 */
	protected $bef_store_calculator;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

    private int $business_form_id;

    private int $household_form_id;

    private int $flight_form_id;

    /**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'BEF_STORE_CALCULATOR_VERSION' ) ) {
			$this->version = BEF_STORE_CALCULATOR_VERSION;
		} else {
			$this->version = '1.0.0';
		}

        if ( defined( 'BEF_STORE_BUSINESS' ) ) {
            $this->business_form_id = BEF_STORE_BUSINESS;
        } else {
            $this->business_form_id = 'none';
        }

        if ( defined( 'BEF_STORE_HOUSEHOLD' ) ) {
            $this->household_form_id = BEF_STORE_HOUSEHOLD;
        } else {
            $this->household_form_id = 'none';
        }

        if ( defined( 'BEF_FLIGHT_CALC' ) ) {
            $this->flight_form_id = BEF_FLIGHT_CALC;
        } else {
            $this->flight_form_id = 'none';
        }

		$this->bef_store_calculator = 'bef-store-calculator';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - bef_store_calculator_Loader. Orchestrates the hooks of the plugin.
	 * - bef_store_calculator_i18n. Defines internationalization functionality.
	 * - bef_store_calculator_Admin. Defines all hooks for the admin area.
	 * - bef_store_calculator_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-bef-store-calculator-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-bef-store-calculator-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-bef-store-calculator-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-bef-store-calculator-public.php';

        /**
         * The class responsible for defining all AJAX actions on the public-facing
         * side of the site.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/bef-store-calculator-ajax-entry.php';

		$this->loader = new bef_store_calculator_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the bef_store_calculator_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new bef_store_calculator_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new bef_store_calculator_Admin( $this->get_bef_store_calculator(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		// Here we are adding the admin_menu page to the admin side bar.   When "admin_menu" hook is called
        // do_action('admin_menu'), this function will be called.  bef_calculator_store_setup_menu resides in
        // *_Admin class.  /admin/class-bef-store-calculator-admin.php
        $this->loader->add_action( 'admin_menu', $plugin_admin, 'bef_calculator_store_setup_menu' );

    }

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
     *
     * Only one style sheet at this time.   So enqueue_styles function in the *_Public class is all that's called.
     * For the future, if each css or js file has different files (different files for business vs household), we
     * will add additional "add_action" steps here to call different functions in the *_Public class.
     *
     * IE:
     *    $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_business_styles' );
     *    $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_household_styles' );
     *
     * Then in the *_Public class, we would have seperate fucnction/callbacks where each is referenced.
     * This can be used to add logic to only load specific files when that form is being loaded.
     *
	 * @since    1.0.3
	 * @access   private
	 */
	private function define_public_hooks()
    {

        // General Public Top-Level Class
        $plugin_public = new bef_store_calculator_Public($this->get_bef_store_calculator(), $this->get_version());

        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');

        // Household Calculator Functions
        $public_household = new bef_store_calculator_household_list($this->get_household_form_id());
        $public_diet = new bef_store_calculator_diet_list($this->get_household_form_id());

        $household_form_id = $this->get_household_form_id();

        $this->loader->add_filter('gform_pre_render_' . $household_form_id, $public_household, 'set_household_type_column');
        $this->loader->add_filter('gform_pre_validation_' . $household_form_id, $public_household, 'set_household_type_column');
        $this->loader->add_filter('gform_pre_submission_filter_' . $household_form_id, $public_household, 'set_household_type_column');
        $this->loader->add_filter('gform_admin_pre_render_' . $household_form_id, $public_household, 'set_household_type_column');

        $this->loader->add_filter('gform_pre_render_' . $household_form_id, $public_diet, 'set_diet_type_column');
        $this->loader->add_filter('gform_pre_validation_' . $household_form_id, $public_diet, 'set_diet_type_column');
        $this->loader->add_filter('gform_pre_submission_filter_' . $household_form_id, $public_diet, 'set_diet_type_column');
        $this->loader->add_filter('gform_admin_pre_render_' . $household_form_id, $public_diet, 'set_diet_type_column');

        // Business Calculator Functions
        $public_business = new bef_store_calculator_buildingtype_list($this->get_business_form_id());
        $public_flight = new bef_store_calculator_flightAPI_list($this->get_business_form_id());

        $business_form_id = $this->get_business_form_id();
        $this->loader->add_filter('gform_pre_render_' . $business_form_id, $public_business, 'set_building_type_column');
        $this->loader->add_filter('gform_pre_validation_' . $business_form_id, $public_business, 'set_building_type_column');
        $this->loader->add_filter('gform_pre_submission_filter_' . $business_form_id, $public_business, 'set_building_type_column');
        $this->loader->add_filter('gform_admin_pre_render_' . $business_form_id, $public_business, 'set_building_type_column');

        $this->loader->add_filter('gform_pre_render_' . $business_form_id, $public_flight, 'set_flight_type_column');
        $this->loader->add_filter('gform_pre_validation_' . $business_form_id, $public_flight, 'set_flight_type_column');
        $this->loader->add_filter('gform_pre_submission_filter_' . $business_form_id, $public_flight, 'set_flight_type_column');
        $this->loader->add_filter('gform_admin_pre_render_' . $business_form_id, $public_flight, 'set_flight_type_column');
    }

    /**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_bef_store_calculator() {
		return $this->bef_store_calculator;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    bef_store_calculator_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

    /**
     * Retrieve the household calculator form ID
     *
     * @since     1.0.3
     * @return int|string
     */
    public function get_household_form_id() {
        return $this->household_form_id;
    }

    /**
     * Retrieve the business calculator form ID
     *
     * @since     1.0.3
     * @return int|string
     */
	public function get_business_form_id() {
	    return $this->business_form_id;
    }

    /**
     * Retrieve the flight calculator form ID
     *
     * @since     1.0.3
     * @return int|string
     */
    public function get_flight_form_id() {
        return $this->flight_form_id;
    }
}
