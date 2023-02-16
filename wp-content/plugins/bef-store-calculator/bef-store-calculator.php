<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * This file contains the top-level version information.   It is read in main bef_store_calculator class
 * Through the defined variable BEF_STORE_CALCULATOR_VERSION
 *
 * @link              http://www.helloworlddevs.com
 * @since             1.0.1
 * @package           bef_store_calculator
 *
 * @wordpress-plugin
 * Plugin Name:       BEF calculator
 * Plugin URI:        /bef-store-calculator-uri/
 * Description:       BEF calculator store custom code and functions
 * Version:           1.0.3
 * Author:            Hello World Devs
 * Author URI:        http://www.helloworlddevs.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       bef-store-calculator
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'BEF_STORE_CALCULATOR_VERSION', '1.0.3' );

/**
 * Calculator top-level form ID
 */
define( 'BEF_STORE_BUSINESS', 24);
define( 'BEF_STORE_HOUSEHOLD', 26);
define( 'BEF_FLIGHT_CALC', 22);

/**
 * wpdatatables ENV variables for easier reference
 */
define( 'BEF_AIRPORTS', 'wp_wpdatatable_24' );
define( 'BEF_TRANSPORTATION', 'wp_wpdatatable_35' );
define( 'BEF_EGRID_ZIP_SUBREGION', 'wp_wpdatatable_26' );
define( 'BEF_EGRID_FACTORS', 'wp_wpdatatable_33' );
define( 'BEF_DIET', 'wp_wpdatatable_28' );
define( 'BEF_BUILDING_TYPE', 'wp_wpdatatable_29' );
define( 'BEF_HOUSEHOLD_TYPE', 'wp_wpdatatable_30' );
define( 'BEF_EMISSIONS_FUEL_TYPE', 'wp_wpdatatable_31' );
define( 'BEF_CONVERSIONS', 'wp_wpdatatable_34' );


/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-bef-store-calculator-activator.php
 */
function activate_bef_store_calculator() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-bef-store-calculator-activator.php';
	bef_store_calculator_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-bef-store-calculator-deactivator.php
 */
function deactivate_bef_store_calculator() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-bef-store-calculator-deactivator.php';
	bef_store_calculator_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_bef_store_calculator' );
register_deactivation_hook( __FILE__, 'deactivate_bef_store_calculator' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-bef-store-calculator.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_bef_store_calculator() {

	$plugin = new bef_store_calculator();
	$plugin->run();

}
run_bef_store_calculator();
