<?php

/**
 * Main admin page building module for bef store calculator module.
 *
 * @link       http://www.helloworlddevs.com
 * @since      1.0.2
 *
 * @package    bef_store_calculator
 * @subpackage bef_store_calculator/admin
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
 * @since      1.0.2
 * @package    bef_store_calculator
 * @subpackage bef_store_calculator/admin
 * @author     Jeff Browning <jeff@helloworlddevs.com>
 */
class bef_store_calculator_mainPage {

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      BEF_Store_Calculator_Loader $loader Maintains and registers all hooks for the plugin.
     */
    public static function bef_store_display_page() {

        echo '<div class= "row">';
        echo '<h1 class="center" >BEF Store Admin Dashboard</h1>';
        echo '</div>';
    }
}
