<?php

/**
 * Code for generating custom gravity form code for business calculator
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
 * @since      1.0.0
 * @package    bef_store_calculator
 * @subpackage bef_store_calculator/admin
 * @author     Jeff Browning <jeff@helloworlddevs.com>
 */
class bef_store_calculator_business {

    /**
     * Contains custom code for generation for top-level business form
     *
     */
    private string $bef_store_calculator;
    private string $version;
    private $business_form_id;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $bef_store_calculator       The name of the plugin.
     * @param      string    $version    The version of this plugin.
     * @param      int    $business_form_id    Gravity Form ID.
     */
    public function __construct( $bef_store_calculator, $version, $business_form_id ) {

        $this->bef_store_calculator = $bef_store_calculator;
        $this->version = $version;
        $this->business_form_id = $business_form_id;
    }
}
