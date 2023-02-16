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
 * The core plugin class to build custom drop-down list for business listings.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.3
 * @package    bef_store_calculator
 * @author     Jeff Browning <jeff@helloworlddevs.com>
 */
class bef_store_calculator_public_list
{

    /**
     * Contains custom code for generation of gravity forms columns
     *
     */
    private int $business_form_id;

    /**
     * Initialize the class and set its properties.
     *
     * @param string $bef_store_calculator The name of the plugin.
     * @param string $version The version of this plugin.
     * @param int $business_form_id Gravity Form ID.
     * @since    1.0.0
     */
    public function __construct($business_form_id)
    {
        $this->business_form_id = $business_form_id;
    }

    /**
     * Custom Updates to BEF Calc building types from database query
     *
     * This function is run during pre-population of the form.
     * Using the gform_pre_render filter allows us to modify the form right before it is displayed
     * It looks for the expected $type entry and cssClass and will reassign the choices
     * This type and class selector are specific to the custom drop down partial component.
     *
     **/
    public function set_building_type_column($form)
    {

        foreach ($form['fields'] as $field) {
            // Looping through all fields in the form to find the correct/unique location to assign the values
            if (($field->type != 'list' || strpos($field->cssClass, 'building-type__selector') === false)) {
                continue;
            }

            // Consistency Check that forms at filter level are the same
            if ($this->business_form_id === $field->formId) {
                // Add Filter to append database query of building types
                add_filter('gform_column_input_' . $field->formId . '_' . $field->id . '_1', array($this, 'set_building_type_values'));
            } else {
                error_log( print_r( '--- BEF_LOG: Consistency: Business Form Filter not matching global ENV -----:  ', true ) );
            }
        }
        return $form;
    }

    function set_building_type_values()
    {
        global $wpdb;

        $result = $wpdb->get_results('SELECT description FROM ' . BEF_BUILDING_TYPE);
        $choices = array();

        foreach ($result as $data) {
            $choices[] = array('text' => $data->description,
                'value' => $data->description);
        }

        return array('type' => 'select', 'choices' => $choices);
    }

}
