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

class bef_store_calculator_electrcity_grid
{

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

    // PHP Code to query the DB and get the egrid value
    public function get_egrid($zipcode)
    {
        global $wpdb;
        $queryString = 'SELECT egrid FROM ' . BEF_EGRID_ZIP_SUBREGION . ' WHERE zipcode=' . $zipcode;
        $result = $wpdb->get_results($queryString);
        // results from $wpdb are an array of objects.   Only want the object part.
        return $result[0];
    }
}
