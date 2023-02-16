<?php

/**
 * Code for generating custom gravity form code for custom city to city tables
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
if (class_exists('GF_Field')) {
    class bef_store_flight_calculator_annual_num_of_flights extends GF_Field {
        public $type = 'custom_annual_flights_table';

        private $column_headers = ['staff_flights' => '# of Staff Flights', 'refractive_index' => 'Account for Refractive Index? (Y/N)'];

        // These values MUST match the values in the function calculation.   They are what determine the
        // class hook for the event listener.   Defined in defaultMPG object in
        // bef-store-calculator/includes/src/calculators/complexTravel/flightTable.js
        private $classVals = ['shortHaul', 'mediumHaul', 'longHaul'];

        public function get_form_editor_field_title() {
            return esc_attr__('[C] Ann. Flights', 'txtdomain');
        }

        public function get_form_editor_button() {
            return [
                'group' => 'advanced_fields',
                'text'  => $this->get_form_editor_field_title(),
            ];
        }

        public function get_form_editor_field_settings() {
            return [
                'label_setting',
                'choices_setting',
                'description_setting',
                'rules_setting',
                'error_message_setting',
                'css_class_setting',
                'conditional_logic_field_setting',
                'prepopulate_field_setting',
            ];
        }

        public function is_value_submission_array() {
            return true;
        }

        // Called when the form is rendered/setup
        public function get_field_input($form, $value = '', $entry = null) {

            $id = (int) $this->id;

            if ($this->is_entry_detail()) {
                $table_value = maybe_unserialize($value);
            } else {
                $table_value = $this->translateValueArray($value);
            }

            if ($form['id'] == BEF_STORE_HOUSEHOLD) {
                $this->column_headers['staff_flights'] = '# of Flights';
            }

            $table = '<table class="table__annual-num-of-flights"><tbody><tr>';
            $table .= '<th>' . __('Trip Type', 'txtdomain') . '</th>';
            foreach ($this->column_headers as $col) {
                $table .= '<th>' . $col . '</th>';
            }
            $table .= '</tr>';

            $counter = 0;
            foreach ($this->choices as $trip_types) {
                $table .= '<tr>';
                $table .= '<td>' . $trip_types['text'] . '</td>';
                foreach (array_keys($this->column_headers) as $col) {
                    if ($col == 'refractive_index') {
                        $table .= '<td><select class="RFIvalues ' . $this->classVals[$counter] . '" name="input_' . $id . '[]" value="' . $table_value[$trip_types['text']][$col] . '"><option value="NO" selected>No</option><option value="YES">Yes</option></select></td>';
                    } else {
                        $table .= '<td><input class="flightNum ' . $this->classVals[$counter] . '" type="number" size="1" name="input_' . $id . '[]" value="' . $table_value[$trip_types['text']][$col] . '" /></td>';
                    }

                }
                $counter++;
                $table .= '</tr>';
            }

            $table .= '</tbody></table>';

            return $table;
        }

        // transform this one-dimensional array into a multidimensional associative array
        // NOTE: As this is a one-dimensional array, we can loop through each entry in the array
        // using $counter++ as a simplification.   We are just going through each entry in the array and
        // reassigning
        private function translateValueArray($value) {
            if (empty($value)) {
                return [];
            }
            $table_value = [];
            $counter = 0;
            foreach ($this->choices as $row) {
                foreach (array_keys($this->column_headers) as $col) {
                    $table_value[$row['text']][$col] = $value[$counter++];
                }
            }
            return $table_value;
        }

        // array transformation we also need to serialize the array so that Gravity Forms can store the
        // value properly in the database.
        // $table_value is our associative array, but $value is still the serialized which is stored in the DB
        public function get_value_save_entry($value, $form, $input_name, $lead_id, $lead) {
            if (empty($value)) {
                $value = '';
            } else {
                $table_value = $this->translateValueArray($value);
                $value = serialize($table_value);
            }
            return $value;
        }

        // Display the submitted values
        // custom function that accepts the value; the unserialized multidimensional array, as parameter.
        // The function then builds up some HTML that displays the array in a pretty way and returns the string
        private function listOutput($value) {
            $value_number = [];

            foreach ($value as $vehicle => $data) {
                $val =  $data;
                array_push($value_number, array_shift($val));
            }

            $value_sum = array_sum($value_number);

            if ($value_sum > 0) {

                $summary = '';
                $str = '<table class="gfield_list review_table">';

                // Table Header Generation
                $header = '';
                $header .= '<th class="review_table__header">Trip Type</th>';
                foreach (array_values($this->column_headers) as $col) {
                    $header .= '<th class="review_table__header">' . $col . '</th>';
                }

                // Table Body Generation
                $body = '';
                foreach ($value as $trip_type => $entry) {
                    // Only add entries if there were any requests at all
                    $body .= '<tr><td class="review_table__entry">' . $trip_type . '</td>';
                    foreach ($entry as $col => $value) {
                        $body .= '<td class="review_table__entry">' . $value . '</td>';
                    }
                    $body .= '</tr>';
                }

                if (!empty($body)) {
                    // Combine all entries
                    $str .= '<thead>' . $header . '</thead>';
                    $str .= '<tbody>' . $body . '</tbody>';
                    $summary .= $str . '</table>';
                }

                return $summary;

            } else {
                return '';
            }


        }

        /**
         * Format the entry value for display on the entries list page.
         * Return a value that's safe to display on the page.
         */
        public function get_value_entry_list($value, $entry, $field_id, $columns, $form) {
            return __('Enter details to see annual flight details', 'txtdomain');
        }

        public function get_value_entry_detail($value, $currency = '', $use_text = false, $format = 'html', $media = 'screen') {
            $value = maybe_unserialize($value);

            if (empty($value)) {
                return $value;
            }
            $str = $this->listOutput($value);
            return $str;
        }

        public function get_value_merge_tag($value, $input_id, $entry, $form, $modifier, $raw_value, $url_encode, $esc_html, $format, $nl2br) {
            return $this->listOutput($value);
        }

        public function is_value_submission_empty($form_id) {
            $value = (array) rgpost('input_' . $this->id);
            foreach ($value as $input) {
                if (strlen(trim($input)) > 0) {
                    return false;
                }
            }
            return true;
        }
    }
    GF_Fields::register(new bef_store_flight_calculator_annual_num_of_flights());
}
