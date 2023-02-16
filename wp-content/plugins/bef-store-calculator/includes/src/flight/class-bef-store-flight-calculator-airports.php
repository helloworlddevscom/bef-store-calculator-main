<?php

/**
 * Code for generating custom gravity form code for business calculator
 *
 * This class is used to generate the eGRID lookup from a user entered zipcode
 *
 * @link       http://www.helloworlddevs.com
 * @since      1.0.2
 *
 * @package    bef_store_calculator
 * @subpackage bef_store_calculator/public
 */

if (!defined('WPINC')) { // MUST have WordPress.
    exit('Do not access this file directly.');
}

/**
 * The core plugin class to build UI.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.3
 * @package    bef_store_calculator
 * @subpackage bef_store_calculator/public
 * @author     Jeff Browning <jeff@helloworlddevs.com>
 */

if (!defined('WPINC')) { // MUST have WordPress.
    exit('Do not access this file directly.');
}

class bef_store_flight_calculator_airports {

    /**
     * Initialize the class and set its properties.
     *
     * @param string $bef_store_calculator The name of the plugin.
     * @param string $version The version of this plugin.
     * @since    1.0.0
     */
    public function __construct()
    {
    }

    // PHP Code to query the DB and get the airports
    public function get_airports($airport_a, $airport_b)
    {
        global $wpdb;

        $query_a = sprintf( 'SELECT latitude, longitude FROM %s WHERE iatacode="%s"', BEF_AIRPORTS, $airport_a );
        $query_b = sprintf( 'SELECT latitude, longitude FROM %s WHERE iatacode="%s"', BEF_AIRPORTS, $airport_b );

        $airport_query_a = $wpdb->get_results($query_a);
        $airport_query_b = $wpdb->get_results($query_b);

        $return_query = [$airport_query_a[0], $airport_query_b[0]];

        // results from BEF_AIRPORTS table
        return $return_query;
    }
}
