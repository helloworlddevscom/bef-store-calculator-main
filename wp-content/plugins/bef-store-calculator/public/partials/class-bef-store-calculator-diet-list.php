<?php
/**
 * Code for generating custom gravity form code for household calculator
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
 * The core plugin class to build custom drop-down list for household diet entry.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.3
 * @package    bef_store_calculator
 * @author     Jeff Browning <jeff@helloworlddevs.com>
 */
class bef_store_calculator_diet_list
{

    /**
     * Contains custom code for generation of gravity forms columns
     *
     */
    private int $household_form_id;

    /**
     * Initialize the class and set its properties.
     *
     * @param string $bef_store_calculator The name of the plugin.
     * @param string $version The version of this plugin.
     * @param int $household_form_id Gravity Form ID.
     * @since    1.0.0
     */
    public function __construct($household_form_id)
    {
        $this->household_form_id = $household_form_id;
    }

    /**
     * Custom Updates to BEF Calc diet types from database query
     *
     * This function is run during pre-population of the form.
     * Using the gform_pre_render filter allows us to modify the form right before it is displayed
     * It looks for the expected $type entry and cssClass and will reassign the choices
     * This type and class selector are specific to the custom drop down partial component.
     *
     **/
    public function set_diet_type_column($form)
    {

        foreach ($form['fields'] as $field) {
            // Looping through all fields in the form to find the correct/unique location to assign the values
            // NOTE:  strpos returns an integer if found in the string.   Check if integer returned
            if ($field->type === 'list' &&
                (is_int(strpos($field->cssClass, 'household-type__select-diet'))
                ))
            {
                // Consistency Check that forms at filter level are the same
                if ($this->household_form_id === $field->formId) {
                    // Add Filter to append database query of diet types
                    add_filter('gform_column_input_' . $field->formId . '_' . $field->id . '_1', array($this, 'set_diet_type_values'));
                    add_filter('gform_column_input_content_' . $field->formId . '_' . $field->id, array($this, 'set_diet_type_unique'), 10, 6);

                } else {
                    error_log(print_r('--- BEF_LOG: Consistency: Business Form Filter not matching global ENV -----:  ', true));
                }
            } else {
                continue; // if isn't type=list OR special selector class, skip the rest.
            }
        }
        return $form;
    }

    function set_diet_type_values()
    {
        global $wpdb;

        $result = $wpdb->get_results('SELECT description, diettype FROM ' . BEF_DIET);

        $choices = array();

        foreach ($result as $data) {
            $choices[] = array('text' => $data->description,
                'value' => $data->diettype
            );
        }
        array_unshift($choices, array('text' => 'Please choose a diet', 'value' => 'none'));
        return array('type' => 'select', 'choices' => $choices);
    }

    function set_diet_type_unique( $input, $input_info, $field, $text, $value, $form_id )
    {
        $fieldLower = strtolower($text);
        $class = "";
        if (strpos($fieldLower, 'meals') !== false) {
            $customClass = 'diet__meal';
            $class = 'class=' . $customClass . ' ';
        }
        if (strpos($fieldLower, 'people') !== false) {
            $customClass = 'diet__people';
            $class = 'class=' . $customClass . ' ';
        }
        return substr_replace( $input, $class, 7, 0 );
    }
}
