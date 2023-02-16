<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://www.helloworlddevs.com
 * @since      1.0.0
 *
 * @package    bef_store_calculator
 * @subpackage bef_store_calculator/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    bef_store_calculator
 * @subpackage bef_store_calculator/public
 * @author     info <http://www.helloworlddevs.com>
 */
class bef_store_calculator_Public
{

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $bef_store_calculator The ID of this plugin.
     */
    private $bef_store_calculator;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @param string $bef_store_calculator The name of the plugin.
     * @param string $version The version of this plugin.
     * @since    1.0.0
     */
    public function __construct($bef_store_calculator, $version)
    {

        $this->bef_store_calculator = $bef_store_calculator;
        $this->version = $version;

        $this->load_dependencies();
    }

    private function load_dependencies()
    {
        /**
         * The class responsible for defining any calculator public display page information
         * Load any additional classes that are needed for calculator functionality
         *
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-bef-store-calculator-business.php';

        /**
         * The class responsible for defining custom list top-level component to be added to form
         *
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/class-bef-store-calculator-buildingtype-list.php';

        /**
         * The class responsible for defining custom list top-level component to be added to form
         *
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/class-bef-store-calculator-household-list.php';

        /**
         * The class responsible for defining custom list top-level component to be added to form
         *
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/class-bef-store-calculator-flightAPI-list.php';

        /**
         * The class responsible for defining calculator table logic
         *
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/class-bef-store-calculator-commuting.php';

        /**
         * The class responsible for defining calculator table logic
         *
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/class-bef-store-calculator-owned-vehicle.php';

        /**
         * The class responsible for defining calculator table logic
         *
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/class-bef-store-calculator-personal-vehicle.php';

        /**
         * The class responsible for defining calculator table logic
         *
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/class-bef-store-calculator-diet-list.php';

        /**
         * The class responsible for defining calculator table logic
         *
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/class-bef-store-flight-calculator-annual-num-of-flights.php';

        /**
         * The class responsible for calling out to get the eGRID value via AJAX
         *
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/electricity/class-bef-store-calculator-electricity-grid.php';

        /**
         * The class responsible for defining calculator table logic
         *
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/flight/class-bef-store-flight-calculator-airports.php';

    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {

        /**
         * This function a single file that is called from the Loader class.
         *
         * An instance of this class is passed to the run() function
         * defined in bef_store_calculator_Loader as all of the hooks are defined
         * in that particular class.
         *
         *
         * The bef_store_calculator_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        wp_enqueue_style($this->bef_store_calculator, plugin_dir_url(__FILE__) . 'css/bef-store-calculator-public.css', array(), $this->version, 'all');

    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {

        /**
         * This function a single file that is called from the Loader class.
         *
         * An instance of this class is passed to the run() function
         * defined in bef_store_calculator_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The bef_store_calculator_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        // Generate Nonce to verify request is coming from the site.
        $params = array(
            'ajaxurl'    =>  admin_url( 'admin-ajax.php' ),
            'nonce' => wp_create_nonce('bef-store-calculator'),
        );

        wp_enqueue_script($this->bef_store_calculator, plugin_dir_url(__FILE__) . 'js/bef-store-calculator-public.js', array('jquery', 'jquery-ui-autocomplete'), $this->version, false);
        wp_localize_script( $this->bef_store_calculator, 'jsParameters', $params );

    }

}
