<?php

namespace WDTGravityIntegration;

/**
 * @package Gravity Forms integration for wpDataTables
 * @version 1.3
 */

/*
Plugin Name: Gravity Forms integration for wpDataTables
Plugin URI: https://wpdatatables.com/documentation/addons/gravity-forms-integration/
Description: Tool that adds "Gravity Form" as a new table type and allows you to create wpDataTables
             from Gravity Forms entries data.
Version: 1.3
Author: TMS-Plugins
Author URI: http://tms-plugins.com
Text Domain: wpdatatables
Domain Path: /languages
*/

use DateTime;
use WDTTools;
use WP_Error;

// Full path to the WDT GF root directory
define('WDT_GF_ROOT_PATH', plugin_dir_path(__FILE__));
// URL of WDT GF integration plugin
define('WDT_GF_ROOT_URL', plugin_dir_url(__FILE__));
// Current version of WDT GF integration plugin
define('WDT_GF_VERSION', '1.3');
// Required wpDataTables version
define('WDT_GF_VERSION_TO_CHECK', '3.0');

// Init Gravity Forms integration for wpDataTables add-on
add_action('plugins_loaded', array('WDTGravityIntegration\Plugin', 'init'), 10);

// Add Gravity Forms Integration activation setting
add_action('wdt_add_activation', array('WDTGravityIntegration\Plugin', 'addGravityActivation'));

// Enqueue Gravity Forms Integration add-on files on back-end settings page
add_action('wdt_enqueue_on_settings_page', array('WDTGravityIntegration\Plugin', 'wdtGravityEnqueueBackendSettings'));

// Check auto update
add_filter('pre_set_site_transient_update_plugins', array('WDTGravityIntegration\Plugin', 'wdtCheckUpdateGravity'));

// Check plugin info
add_filter('plugins_api', array('WDTGravityIntegration\Plugin', 'wdtCheckInfoGravity'), 10, 3);

// Add a message for unavailable auto update if plugin is not activated
add_action('in_plugin_update_message-' . plugin_basename(__FILE__), array('WDTGravityIntegration\Plugin', 'addMessageOnPluginsPageGravity'));

// Add error message on plugin update if plugin is not activated
add_filter('upgrader_pre_download', array('WDTGravityIntegration\Plugin', 'addMessageOnUpdateGravity'), 10, 4);

/**
 * Class Plugin
 * Main entry point of the wpDataTables Gravity Forms integration
 * @package WDTGravityIntegration
 */
class Plugin
{

    public static $initialized = false;
    public static $table;
    private static $wdtParameters = [];
    public static $fieldIDs = [];
    private static $gravityData;
    private static $searchCriteria;
    private static $dateFieldIds = [];
    private static $timeFieldIds = [];

    /**
     * Instantiates the class
     * @return bool
     */
    public static function init()
    {
        // Check if wpDataTables is installed
        if (!defined('WDT_ROOT_PATH') || !defined('GF_MIN_WP_VERSION')) {
            add_action('admin_notices', array('WDTGravityIntegration\Plugin', 'wdtNotInstalled'));
            return false;
        }
        // Enqueue Gravity files
        add_action('wdt_enqueue_on_edit_page', array('WDTGravityIntegration\Plugin', 'wdtGravityEnqueue'));

        // Add "Gravity Form" in "Input data source type" dropdown on "Data Source" tab
        add_action('wdt_add_table_type_option', array('WDTGravityIntegration\Plugin', 'addTableTypeOption'));

        // Add Gravity Form HTML elements on "Data Source" tab on table configuration page
        add_action('wdt_add_data_source_elements', array('WDTGravityIntegration\Plugin', 'addGravityOnDataSourceTab'));

        // Add alert template for sorting and filtering server-side tables
        add_action('wdt_above_table_alert', array('WDTGravityIntegration\Plugin', 'alertServerSide'));

        // Add "Gravity Form" tab on table configuration page
        add_action('wdt_add_table_configuration_tab', array('WDTGravityIntegration\Plugin', 'addGravityTab'));

        // Add tablpanel for "Gravity Form" tab on table configuration page
        add_action('wdt_add_table_configuration_tabpanel', array('WDTGravityIntegration\Plugin', 'addGravityTabPanel'));

        // Save table configuration
        add_action('wp_ajax_wdt_gravity_save_table_config', array('WDTGravityIntegration\Plugin', 'saveTableConfig'));

        // Get form fields AJAX action
        add_action('wp_ajax_wdt_gf_get_form_fields', array('WDTGravityIntegration\Plugin', 'getGravityFormFields'));

        // Extend the wpDataTables supported data sources
        add_action('wpdatatables_generate_gravity', array('WDTGravityIntegration\Plugin', 'gravityBasedConstruct'), 10, 3);

        // Extend table config before saving table to DB
        add_filter('wpdatatables_filter_insert_table_array', array('WDTGravityIntegration\Plugin', 'extendTableConfig'), 10, 1);

        // Extend Gravity pre-save lead
        add_filter('gform_entry_id_pre_save_lead', array('WDTGravityIntegration\Plugin', 'wdtPreSaveLead'), 10, 1);

        // Set multipage form to single one
        add_filter('gform_target_page', array('WDTGravityIntegration\Plugin', 'wdtTargetPage'), 10, 4);

        // Remove no Duplicates option validation when editing an existing entry
        add_filter('gform_pre_validation', array('WDTGravityIntegration\Plugin', 'wdtEditValidation'), 10, 2);

        //Changing the query for field type - Time
        add_filter('get_meta_sql', array('WDTGravityIntegration\Plugin', 'wdtModifyTimeQuery'), 10, 6);

        //Edit Gravity based table
        add_action('wp_ajax_wdt_save_gf_table_frontend', array('WDTGravityIntegration\Plugin', 'wdtSaveGFTableFrontend'));

        //Edit Gravity based table for non-logged in users
        add_action('wp_ajax_nopriv_wdt_save_gf_table_frontend', array('WDTGravityIntegration\Plugin', 'wdtSaveGFTableFrontend'));

        //Get possible values
        add_filter('wpdatatables_possible_values_gravity', array('WDTGravityIntegration\Plugin', 'getPossibleGFValuesRead'), 10, 3);

        //Delete Gravity entry
        add_action('wp_ajax_wdt_delete_gf_table_row', array('WDTGravityIntegration\Plugin', 'wdtDeleteGFEntry'));

        //Delete Gravity entry for non-logged in users
        add_action('wp_ajax_nopriv_wdt_delete_gf_table_row', array('WDTGravityIntegration\Plugin', 'wdtDeleteGFEntry'));

        // Add JS and CSS for editable tables on frontend
        add_action('wdt_enqueue_on_frontend', array('WDTGravityIntegration\Plugin', 'enqueueFrontendAssets'));

        // Check if wpDataTables required version is installed
        if (version_compare(WDT_CURRENT_VERSION, WDT_GF_VERSION_TO_CHECK) < 0) {
            // Show message if required wpDataTables version is not installed
            add_action('admin_notices', array('WDTGravityIntegration\Plugin', 'wdtRequiredVersionMissing'));
            return false;
        }

        \WPDataTable::$allowedTableTypes[] = 'gravity';
        return self::$initialized = true;
    }

    /**
     * Show message if wpDataTables is not installed
     */
    public static function wdtNotInstalled()
    {
        $message = __('Gravity Forms integration for wpDataTables is an add-on - please install and activate wpDataTables and Gravity Forms to be able to use it!', 'wpdatatables');
        echo "<div class=\"error\"><p>{$message}</p></div>";
    }

    /**
     * Show message if required wpDataTables version is not installed
     */
    public static function wdtRequiredVersionMissing()
    {
        $message = __('Gravity Forms integration for wpDataTables add-on requires wpDataTables version ' . WDT_GF_VERSION_TO_CHECK . '. Please update wpDataTables plugin to be able to use it!', 'wpdatatables');
        echo "<div class=\"error\"><p>{$message}</p></div>";
    }

    /**
     * Enqueue all necessary styles and scripts
     */
    public static function wdtGravityEnqueue()
    {
        // Gravity Forms integration CSS
        wp_enqueue_style('wdt-gf-wizard', WDT_GF_ROOT_URL . 'assets/css/table_creation_wizard.css', array(), WDT_GF_VERSION);
        // Gravity Forms integration JS
        wp_enqueue_script('wdt-gf-table-config', WDT_GF_ROOT_URL . 'assets/js/gf_table_config_object.js', array(), WDT_GF_VERSION, true);
        wp_enqueue_script('wdt-gf-wizard', WDT_GF_ROOT_URL . 'assets/js/table_creation_wizard.js', array(), WDT_GF_VERSION, true);

        //Include Gravity style for editing
        wp_enqueue_style('gform_admin', \GFCommon::get_base_url() . "/css/admin.min.css", array(), WDT_GF_VERSION);
        //Include Gravity script for editing
        wp_enqueue_script('gform_gravityforms');
        //Script for editing Gravity Forms from wpDataTables
        wp_enqueue_script('wdt-gf-editing', WDT_GF_ROOT_URL . 'assets/js/gf_editing.js', array(), WDT_GF_VERSION, true);

        //Script for editing Gravity Forms from wpDataTables
        wp_enqueue_style('wdt-gf-styling', WDT_GF_ROOT_URL . 'assets/css/gf_styling.css', array('gforms_datepicker_css'), WDT_GF_VERSION);

        wp_enqueue_script('wdt-gform_masked_input', \GFCommon::get_base_url() . "/js/jquery.maskedinput.js", array('jquery'), WDT_GF_VERSION, true);

        \WDTTools::exportJSVar('wdtGfSettings', \WDTTools::getDateTimeSettings());
        \WDTTools::exportJSVar('wdtGfTranslationStrings', \WDTTools::getTranslationStrings());
    }

    /**
     * Enqueue styles and scripts for frontend
     */
    public static function enqueueFrontendAssets($wpDataTable)
    {
        if ($wpDataTable->getTableType() === 'gravity') {
            //Include Gravity style for editing
            wp_enqueue_style('gform_admin', \GFCommon::get_base_url() . "/css/admin.min.css", array(), null);
            //Include Gravity script for editing
            wp_enqueue_script('gform_gravityforms');
            //Script for editing Gravity Forms from wpDataTables
            wp_enqueue_script('wdt-gf-editing', WDT_GF_ROOT_URL . 'assets/js/gf_editing.js', array(), WDT_GF_VERSION, true);

            //Script for editing Gravity Forms from wpDataTables
            wp_enqueue_style('wdt-gf-styling', WDT_GF_ROOT_URL . 'assets/css/gf_styling.css', array('gforms_datepicker_css'), WDT_GF_VERSION);

            wp_enqueue_script('wdt-gform_masked_input', \GFCommon::get_base_url() . "/js/jquery.maskedinput.js", array('jquery'), WDT_GF_VERSION, true);

            $content = $wpDataTable->getTableContent();
            $content = json_decode($content);
            require_once(\GFCommon::get_base_path() . '/form_display.php');
            $form = \GFAPI::get_form($content->formId);
            \GFFormDisplay::enqueue_form_scripts($form);

        }
    }

    /**
     * Method that adds "Gravity From" option in "Input data source type" dropdown
     */
    public static function addTableTypeOption()
    {
        echo '<option value="gravity">Gravity Form</option>';
    }

    /**
     * Adds Gravity Form HTML elements on table configuration page
     */
    public static function addGravityOnDataSourceTab()
    {
        ob_start();
        include WDT_GF_ROOT_PATH . 'templates/data_source_block.inc.php';
        $gravityDataSource = apply_filters('wdt_gravity_data_source_block', ob_get_contents());
        ob_end_clean();

        echo $gravityDataSource;
    }

    /**
     * Add "Gravity Form" tab on table configuration page
     */
    public static function addGravityTab()
    {
        ob_start();
        include WDT_GF_ROOT_PATH . 'templates/tab.inc.php';
        $gravityTab = apply_filters('wdt_gravity_tab', ob_get_contents());
        ob_end_clean();

        echo $gravityTab;
    }

    /**
     * Add tablpanel for "Gravity Form" tab on table configuration page
     */
    public static function addGravityTabPanel()
    {
        ob_start();
        include WDT_GF_ROOT_PATH . 'templates/tabpanel.inc.php';
        $gravityTabpanel = apply_filters('wdt_gravity_tabpanel', ob_get_contents());
        ob_end_clean();

        echo $gravityTabpanel;
    }

    /**
     * Helper method for retrieving table from DB
     *
     * @param $id
     *
     * @return array|bool|null|object|\stdClass
     * @throws \Exception
     */
    public static function loadDBTable($id)
    {
        self::$table = \WDTConfigController::loadTableFromDB($id);
    }

    /**
     * Setting wpDataTables parameters
     *
     * @param $table
     * @param $params
     */
    public static function setWdtParameters($params)
    {
        self::$wdtParameters['id'] = self::$table->id;
        self::$wdtParameters['sortable'] = self::$table->sorting;
        self::$wdtParameters = array_merge(self::$wdtParameters, $params);
    }

    /**
     * Setting gravity parameters
     * @param $table
     */
    public static function setGravityData()
    {
        self::$gravityData = json_decode(self::$table->advanced_settings)->gravity;
    }

    /**
     * List of chosen fields
     *
     * @param $fieldIds
     */
    public static function setFieldIDs($fieldIds)
    {
        self::$fieldIDs = $fieldIds;
    }

    /**
     * Add alert for sorting and filtering when server-side processing is enabled
     */
    public static function alertServerSide()
    {
        ob_start();
        include WDT_GF_ROOT_PATH . 'templates/alert_server_side.inc.php';
        $alertServerSide = apply_filters('wdt_alert_server_side', ob_get_contents());
        ob_end_clean();

        echo $alertServerSide;
    }

    /**
     * Helper method to get form names and IDs
     */
    public static function getGFNamesIds()
    {
        global $wpdb;

        $table = \GFFormsModel::get_form_table_name();
        $sql = $wpdb->prepare(
            "SELECT id, title
                 FROM $table
                 WHERE is_active = %d
                 AND is_trash = %d",
            true,
            false
        );

        return $wpdb->get_results($sql);

    }

    /**
     * Helper method to get form data for AJAX
     */
    public static function getGravityFormFields()
    {
        $nonce = sanitize_text_field($_POST['nonce']);
        if (wp_verify_nonce($nonce, 'wdtEditNonce')) {
            $formId = (int)$_POST['formId'];
            if ($formId) {
                $form = \GFAPI::get_form($formId);
                // Fetch fields as potential columns
                $formFields = array();
                foreach ($form['fields'] as $field) {
                    if (!in_array($field['type'], array('page', 'section', 'html', 'captcha', 'password'))) {
                        $formFields[] = array(
                            'id' => $field['id'],
                            'label' => $field['label'],
                            'type' => $field['type']
                        );
                    }
                }
                echo json_encode($formFields);
            } else {
                echo json_encode(array('error' => 'Form data could not be read!'));
            }
        } else {
            echo json_encode(array('error' => 'Nonce could not be verified!'));
        }
        exit();
    }

    /**
     * Validate and save Gravity based wpDataTable config to DB
     */
    public static function saveTableConfig()
    {
        // Sanitize NONCE
        $nonce = sanitize_text_field($_POST['nonce']);

        $gravityData = json_decode(
            stripslashes_deep($_POST['gravity'])
        );

        // Sanitize Gravity Config
        $gravityData = self::sanitizeGravityConfig($gravityData);

        if (wp_verify_nonce($nonce, 'wdtEditNonce')) {
            if ($gravityData->formId) {
                // Create a table object
                $table = json_decode(stripslashes_deep($_POST['table']));
                $table->content = json_encode(
                    array(
                        'formId' => $gravityData->formId,
                        'fieldIds' => $gravityData->fields
                    )
                );
                \WDTConfigController::saveTableConfig($table);
            } else {
                echo json_encode(array('error' => 'Form data could not be read!'));
            }
        } else {
            echo json_encode(array('error' => 'Nonce could not be verified!'));
        }
        exit();
    }

    /**
     * Helper method for sanitizing the user input in the gravity config
     *
     * @param $gravityData
     *
     * @return mixed
     */
    public static function sanitizeGravityConfig($gravityData)
    {
        foreach ($gravityData->fields as &$field) {
            if (!in_array($field, ['date_created', 'id', 'created_by', 'ip'])) {
                $field = (int)$field;
            } else {
                $field = sanitize_text_field($field);
            }
        }
        $gravityData->formId = (int)$gravityData->formId;
        $gravityData->showDeletedRecords = (int)$gravityData->showDeletedRecords;

        return $gravityData;
    }

    /**
     * Method that pass $array and $params to wpDataTable arrayBasedConstruct Method
     * that will fill wpDataTable object
     *
     * @param $wpDataTable - WPDataTable object
     * @param $content - stdClass with with form ID and form fields IDs
     * @param $params - parameters that are prepared in WPDataTable fillFromData method
     */
    public static function gravityBasedConstruct($wpDataTable, $content, $params)
    {
        $content = json_decode($content);
        /** @var \WPDataTable $wpDataTable */
        if ($wpDataTable->getWpId()) {
            self::loadDBTable($wpDataTable->getWpId());
            self::setWdtParameters($params);
            self::setGravityData();
            self::setFieldIDs($content->fieldIds);
        }

        if (empty($params['columnTitles'])) {
            $params['columnTitles'] = self::getColumnHeaders($content->formId, $content->fieldIds);
        }

        if ($wpDataTable->isAjaxReturn()) {
            self::ajaxReturnConstruct(self::generateFormArray($content->formId, $content->fieldIds), $content->formId, $wpDataTable);
        } else {
            $wpDataTable->arrayBasedConstruct(self::generateFormArray($content->formId, $content->fieldIds, null), $params);
        }
    }

    /**
     * Helper method to get fields data
     *
     * @param $form
     * @param $fieldsIds
     *
     * @return array
     */
    public static function getFieldsData($form, $fieldsIds)
    {
        $fieldsData = array();
        // Get selected fields data with the child field (multi-fields
        // like checkboxes, products, etc.
        foreach ($form['fields'] as $formField) {
            if (in_array((int)$formField->id, $fieldsIds)) {
                $fieldsData[$formField->id]['type'] = $formField['type'];

                $existingOriginalHeaders = array_map(function ($fieldsData) {
                    if (isset($fieldsData['label'])) {
                        return $fieldsData['label'];
                    }
                }, $fieldsData);
                $fieldsData[$formField->id]['label'] = \WDTTools::generateMySQLColumnName($formField['label'], $existingOriginalHeaders);

                if (is_array($formField->inputs)) {
                    $selectedMultiField = array();
                    foreach ($formField->inputs as $formInput) {
                        $selectedMultiField[] = $formInput['id'];
                    }
                    $fieldsData[$formField->id]['fieldIds'] = $selectedMultiField;
                } else {
                    $fieldsData[$formField->id]['fieldIds'] = $formField->id;
                }
            }
        }
        foreach (array_intersect($fieldsIds, ['date_created', 'id', 'created_by', 'ip']) as $commonField) {
            $fieldsData[$commonField] = array(
                'type' => $commonField,
                'label' => str_replace('_', '', $commonField),
                'fieldIds' => $commonField,
            );
        }
        return $fieldsData;
    }

    /**
     * Generate array for table
     *
     * @param $formId
     * @param $fieldsIds
     * @param $gravityData
     *
     * @return array
     */
    public static function generateFormArray($formId, $fieldsIds, $displayLength = null)
    {
        $tableArray = array();

        if (class_exists('GFAPI')) {
            $form = \GFAPI::get_form($formId);
        } else {
            throw new WDTException(__('You are trying to load a table of an unknown type. Probably you did not activate the addon which is required to use this table type.', 'wpdatatables'));
        }

        $fieldsData = self::getFieldsData($form, $fieldsIds);

        //Searching
        self::$searchCriteria = self::prepareSearchCriteria($fieldsIds, $form);

        //Sorting
        $sorting = self::getSorting($fieldsIds, $fieldsData);

        //Paging
        $paging = self::getPagination($displayLength);

        //Get all the entries
        $entries = \GFAPI::get_entries($formId, self::$searchCriteria, $sorting, $paging);

        //Preparing the field data
        foreach ($entries as $entry) {
            $tableArrayEntry = array();

            foreach ($fieldsData as $fieldData) {
                $tableArrayEntry[$fieldData['label']] = self::prepareFieldsData($entry, $fieldData);
            }
            $tableArray[] = $tableArrayEntry;
        }

        return $tableArray;
    }

    /**
     * Generates the data for tables with server side enabled
     *
     * @param $entriesArray
     * @param $formId
     * @param $wpDataTable
     */
    public static function ajaxReturnConstruct($entriesArray, $formId, $wpDataTable)
    {

        $countEntriesTotal = \GFAPI::count_entries($formId, array('status' => self::$searchCriteria['status']));
        $countEntriesFiltered = \GFAPI::count_entries($formId, self::$searchCriteria);

        $output = array(
            'draw' => (int)$_POST['draw'],
            'recordsTotal' => $countEntriesTotal,
            'recordsFiltered' => $countEntriesFiltered,
            'data' => array()
        );

        $colObjs = $wpDataTable->prepareColumns(self::$wdtParameters);
        $output['data'] = $wpDataTable->prepareOutputData($entriesArray, self::$wdtParameters, $colObjs);
        $output['data'] = apply_filters('wpdatatables_custom_prepare_output_data', $output['data'], $wpDataTable, $entriesArray, self::$wdtParameters, $colObjs);
        $json = json_encode($output);
        $json = apply_filters('wpdatatables_filter_server_side_data', $json, $wpDataTable->getWpId(), $_GET);

        echo $json;
        exit();
    }

    /**
     * Changes the query to remove "[]" from data in the database
     *
     * @param $request
     * @param $object
     *
     * @return mixed
     */
    public static function changeRequestQuery($request)
    {
        $request = str_replace('wp_gf_entry_meta.meta_value', 'REPLACE(REPLACE(wp_gf_entry_meta.meta_value, \'[\', \'\'), \']\', \'\')', $request);
        return $request;
    }

    /**
     * Handle sorting and change the query if necessary
     *
     * @param $fieldsIds
     *
     * @return array|null
     */
    public static function getSorting($fieldsIds, $fieldsData)
    {
        if (self::$wdtParameters != [] && self::$wdtParameters['sortable']) {
            $originalHeadersArray = array_keys(self::$wdtParameters['columnTitles']);
            if (isset($_POST['order'])) {
                $sortColumnType = self::$wdtParameters['data_types'][$originalHeadersArray[$_POST['order'][0]['column']]];
                $key = $fieldsIds[$_POST['order'][0]['column']] ?: null;
                $direction = $_POST['order'][0]['dir'];
                in_array($sortColumnType, ['float', 'int'], true) ? $numeric = true : $numeric = null;
            } else {
                $key = null;
                $direction = 'asc';
                $numeric = null;
            }
            $sorting = array('key' => $key, 'direction' => $direction, 'is_numeric' => $numeric);

            //Adding filters for changing the query necessary for sorting Gravity multiselect column
            if ($key && $fieldsData[$key]['type'] == "multiselect") {
                add_filter('gform_entries_orderby', array('WDTGravityIntegration\Plugin', 'changeRequestQuery'), 10, 1);
            }

        } else {
            $sorting = null;
        }
        return $sorting;
    }

    /**
     * Calculate number of item for pagination. For non server side tables it is set to 10000
     *
     * @param $displayLength
     *
     * @return array
     */
    public static function getPagination($displayLength)
    {
        $offset = isset($_POST['start']) ? $_POST['start'] : 0;
        $page_size = isset($_POST['length']) && $_POST['length'] != -1 ? $_POST['length'] :
            (is_null($displayLength) ? 10000 : $displayLength);
        return array('offset' => $offset, 'page_size' => $page_size);
    }

    /**
     * Return array that will passed in Gravity Form API get_entries method
     * to filter the entries
     *
     * @param $gravityData
     *
     * @return array
     */
    public static function prepareSearchCriteria($fieldsIds, $form)
    {
        $searchCriteria = [];

        if (isset($_POST['search']['value'])) {
            $searchCriteria['field_filters'][] = array('operator' => 'contains', 'value' => $_POST['search']['value']);
        }

        if ((!isset($_POST['showAllRows']) && isset(self::$gravityData->showCurrentUserRecords) && self::$gravityData->showCurrentUserRecords !== 0) ||
            (isset($_POST['showAllRows']) && isset(self::$gravityData->showCurrentUserRecords) && self::$gravityData->showCurrentUserRecords !== 0) && !self::$table->showAllRows) {
            $searchCriteria['field_filters'][] = array('key' => 'created_by', 'value' => get_current_user_id());
        }

        if (self::$gravityData === null || self::$gravityData->showDeletedRecords === 0) {
            $searchCriteria['status'] = 'active';
        }

        $aColumns = (self::$wdtParameters != [] && self::$wdtParameters['columnTitles'] != null )?  array_keys(self::$wdtParameters['columnTitles']) : [];
        $columnNumber = count($aColumns);
        for ($i = 0; $i < $columnNumber; $i++) {

            //
            $columnSearchFromTable = false;
            //
            $columnSearchFromDefaultValue = false;

            if (isset($_POST['columns'][$i]['search']) &&
                $_POST['columns'][$i]['search']['value'] !== '' &&
                $_POST['columns'][$i]['search']['value'] !== '|') {
                $columnSearchFromTable = true;
            }
            if ((isset($_POST['draw']) && ($_POST['draw'] == 1) || $columnSearchFromTable == true) &&
                isset(self::$wdtParameters['filterDefaultValue'][$i]) &&
                self::$wdtParameters['filterDefaultValue'][$i] !== '' &&
                self::$wdtParameters['filterDefaultValue'][$i] !== '|') {
                $columnSearchFromDefaultValue = true;
            }
            if ((isset($_POST['columns'][$i]['searchable']) && $_POST['columns'][$i]['searchable'] == true) &&
                ($columnSearchFromTable || $columnSearchFromDefaultValue)) {
                $columnSearch = $columnSearchFromTable ? $_POST['columns'][$i]['search']['value'] : self::$wdtParameters['filterDefaultValue'][$i];

                if (isset(self::$wdtParameters['filterTypes'][$aColumns[$i]])) {
                    switch (self::$wdtParameters['filterTypes'][$aColumns[$i]]) {

                        case 'number-range':
                            list($left, $right) = explode('|', $columnSearch);
                            if ($left !== '') {
                                $left = (float)$left;
                                $searchCriteria['field_filters'][] = ['key' => $fieldsIds[$i], 'value' => $left, 'operator' => '>=', 'is_numeric' => TRUE];
                            }
                            if ($right !== '') {
                                $right = (float)$right;
                                $searchCriteria['field_filters'][] = ['key' => $fieldsIds[$i], 'value' => $right, 'operator' => '<=', 'is_numeric' => TRUE];
                            }
                            break;

                        case 'date-range':
                            self::$dateFieldIds[] = $fieldsIds[$i];
                            list($left, $right) = explode('|', $columnSearch);

                            if ($left && $right) {
                                $searchCriteria['field_filters'][] = ['key' => $fieldsIds[$i],
                                    'value' => DateTime::createFromFormat(get_option('wdtDateFormat'), $left)->format('Y-m-d H:i:s'),
                                    'operator' => '>='];
                                $searchCriteria['field_filters'][] = ['key' => $fieldsIds[$i], 'value' => DateTime::createFromFormat(get_option('wdtDateFormat'), $right)->format('Y-m-d H:i:s'),
                                    'operator' => '<='];
                            } elseif ($left) {
                                $searchCriteria['field_filters'][] = ['key' => $fieldsIds[$i],
                                    'value' => DateTime::createFromFormat(get_option('wdtDateFormat'), $left)->format('Y-m-d H:i:s'),
                                    'operator' => '>='];
                            } elseif ($right) {
                                $searchCriteria['field_filters'][] = ['key' => $fieldsIds[$i],
                                    'value' => DateTime::createFromFormat(get_option('wdtDateFormat'), $right)->format('Y-m-d H:i:s'),
                                    'operator' => '<='];
                            }
                            break;

                        case 'datetime-range':
                            self::$timeFieldIds[] = $fieldsIds[$i];
                            list($left, $right) = explode('|', $columnSearch);
                            $timeFormat = get_option('wdtTimeFormat');
                            $dateFormat = get_option('wdtDateFormat');
                            if ($form['fields'][$i]['timeFormat'] === '12') {
                                self::$timeFieldIds['format'] = '12';
                            } else {
                                self::$timeFieldIds['format'] = '24';
                            }

                            if ($left && $right) {
                                $searchCriteria['field_filters'][] = ['key' => $fieldsIds[$i],
                                    'value' => DateTime::createFromFormat($dateFormat . ' ' . $timeFormat, $left)->format('Y-m-d H:i:s'),
                                    'operator' => '>='];
                                $searchCriteria['field_filters'][] = ['key' => $fieldsIds[$i],
                                    'value' => DateTime::createFromFormat($dateFormat . ' ' . $timeFormat, $right)->format('Y-m-d H:i:s'),
                                    'operator' => '<='];
                            } elseif ($left) {
                                $searchCriteria['field_filters'][] = ['key' => $fieldsIds[$i],
                                    'value' => DateTime::createFromFormat($dateFormat . ' ' . $timeFormat, $left)->format('Y-m-d H:i:s'),
                                    'operator' => '>='];
                            } elseif ($right) {
                                $searchCriteria['field_filters'][] = ['key' => $fieldsIds[$i],
                                    'value' => DateTime::createFromFormat($dateFormat . ' ' . $timeFormat, $right)->format('Y-m-d H:i:s'),
                                    'operator' => '<='];
                            }
                            break;

                        case 'time-range':
                            self::$timeFieldIds[] = $fieldsIds[$i];
                            list($left, $right) = explode('|', $columnSearch);
                            $dateTimeFormat = get_option('wdtTimeFormat');
                            if ($form['fields'][$i]['timeFormat'] === '12') {
                                self::$timeFieldIds['format'] = '12';
                            } else {
                                self::$timeFieldIds['format'] = '24';
                            }

                            if ($left && $right) {
                                $searchCriteria['field_filters'][] = ['key' => $fieldsIds[$i],
                                    'value' => DateTime::createFromFormat($dateTimeFormat, $left)->format('H:i:s'),
                                    'operator' => '>='];
                                $searchCriteria['field_filters'][] = ['key' => $fieldsIds[$i],
                                    'value' => DateTime::createFromFormat($dateTimeFormat, $right)->format('H:i:s'),
                                    'operator' => '<='];
                            } elseif ($left) {
                                $searchCriteria['field_filters'][] = ['key' => $fieldsIds[$i],
                                    'value' => DateTime::createFromFormat($dateTimeFormat, $left)->format('H:i:s'),
                                    'operator' => '>='];
                            } elseif ($right) {
                                $searchCriteria['field_filters'][] = ['key' => $fieldsIds[$i],
                                    'value' => DateTime::createFromFormat($dateTimeFormat, $right)->format('H:i:s'),
                                    'operator' => '<='];
                            }
                            break;

                        case 'checkbox':
                        case 'multiselect':
                            if (self::$wdtParameters['exactFiltering'][$aColumns[$i]] == 1) {
                                // Trim regex parts for first and last one
                                if (strpos($columnSearch, '$') !== false) {
                                    $checkboxSearches = explode('$|^', $columnSearch);
                                    $checkboxSearches[0] = substr($checkboxSearches[0], 1);
                                    if (count($checkboxSearches) > 1) {
                                        $checkboxSearches[count($checkboxSearches) - 1] = substr($checkboxSearches[count($checkboxSearches) - 1], 0, -1);
                                    } else {
                                        $checkboxSearches[0] = substr($checkboxSearches[0], 0, -1);
                                    }
                                } else {
                                    $checkboxSearches = explode('|', $columnSearch);
                                }
                            } else {
                                $checkboxSearches = explode('|', $columnSearch);
                            }
                            $searchCriteria['field_filters'][] = ['key' => $fieldsIds[$i], 'operator' => 'in', 'value' => $checkboxSearches];
                            break;
                        case 'select':
                        case 'text':
                        case 'number':
                            $values = explode(' ', $columnSearch);
                            if (null === self::$wdtParameters['foreignKeyRule'][$_POST['columns'][$i]['name']]) {
                                foreach ($values as $value) {
                                    if (self::$wdtParameters['exactFiltering'][$aColumns[$i]] === 1) {
                                        $searchCriteria['field_filters'][] = ['key' => $fieldsIds[$i], 'value' => $value];
                                    } else {
                                        if (self::$wdtParameters['filterTypes'][$aColumns[$i]] === 'number') {
                                            $searchCriteria['field_filters'][] = ['key' => $fieldsIds[$i], 'value' => $value];
                                        } else {
                                            $searchCriteria['field_filters'][] = ['key' => $fieldsIds[$i], 'value' => $value, 'operator' => 'contains'];
                                        }
                                    }
                                }
                            }
                            break;
                        default:
                            $searchCriteria['field_filters'][] = ['key' => $fieldsIds[$i], 'value' => $columnSearch];
                    }
                }
            }
        }

        if (self::$gravityData !== null && self::$gravityData->dateFilterLogic) {

            if (self::$gravityData->dateFilterLogic === 'range') {

                $dateFormat = get_option('wdtDateFormat');
                $timeFormat = get_option('wdtTimeFormat');

                if (self::$gravityData->dateFilterFrom) {
                    $searchCriteria['start_date'] = DateTime::createFromFormat(
                        $dateFormat . ' ' . $timeFormat,
                        self::$gravityData->dateFilterFrom
                    )->format('Y-m-d H:i:s');
                }

                if (self::$gravityData->dateFilterTo) {
                    $searchCriteria['end_date'] = DateTime::createFromFormat(
                        $dateFormat . ' ' . $timeFormat,
                        self::$gravityData->dateFilterTo
                    )->format('Y-m-d H:i:s');
                }

            } else {
                $searchCriteria['start_date'] = date('Y-m-d', strtotime("-" . self::$gravityData->dateFilterTimeUnits . self::$gravityData->dateFilterTimePeriod));
                $searchCriteria['end_date'] = date('Y-m-d');
            }
        }

        return $searchCriteria;
    }

    /**
     * Formats the entry how it should be displayed in wpDataTable
     * based on the field type
     *
     * @param $entry
     * @param $fieldData
     *
     * @return mixed|string
     */
    public static function prepareFieldsData($entry, $fieldData)
    {
        switch ($fieldData['type']) {
            case 'textarea':
                $fieldData = nl2br($entry[$fieldData['fieldIds']]);

                return $fieldData;
                break;
            case 'multiselect':
                $fieldData = str_replace(',', ', ', str_replace(array('[', ']', '"'), '', $entry[$fieldData['fieldIds']]));

                return $fieldData;
                break;
            case 'checkbox':
                foreach ($fieldData['fieldIds'] as $fieldId) {
                    $childEntry[] = $entry[$fieldId];
                }

                return implode(', ', array_filter($childEntry));
                break;
            case 'name':
                foreach ($fieldData['fieldIds'] as $fieldId) {
                    $childEntry[] = $entry[$fieldId];
                }

                return implode(' ', array_filter($childEntry));
                break;
            case 'time':
                $fieldData = $entry[(int)$fieldData['fieldIds'][0]];

                return $fieldData;
                break;
            case 'date':
                if (is_array($fieldData['fieldIds'])) {
                    $fieldData = $entry[(int)$fieldData['fieldIds'][0]];
                } else {
                    $fieldData = $entry[$fieldData['fieldIds']];
                }

                return $fieldData;
                break;
            case 'address':
                foreach ($fieldData['fieldIds'] as $fieldId) {
                    $childEntry[] = $entry[$fieldId];
                }

                return implode('<br>', array_filter($childEntry));
                break;
            case 'email':
                if (is_array($fieldData['fieldIds'])) {
                    $fieldData = $entry[$fieldData['fieldIds'][0]];
                } else {
                    $fieldData = $entry[$fieldData['fieldIds']];
                }

                return $fieldData;
                break;
            case 'fileupload':
                $fileURLOutput = '';
                if (!empty($entry[$fieldData['fieldIds']])) {
                    if (json_decode($entry[$fieldData['fieldIds']])) {
                        $fieldData = json_decode($entry[$fieldData['fieldIds']]);

                        foreach ($fieldData as $fileURL) {
                            $fileURLOutput .= '<a href=' . $fileURL . '>' . basename($fileURL) . '</a></br>';
                        }
                    } else if (in_array(wp_check_filetype($entry[$fieldData['fieldIds']])['ext'], array('jpg', 'jpeg', 'png', 'gif'), true)) {
                        $fileURLOutput = $entry[$fieldData['fieldIds']];
                    } else {
                        $fileURLOutput .= '<a href=' . $entry[$fieldData['fieldIds']] . '>' . basename($entry[$fieldData['fieldIds']]) . '</a></br>';
                    }

                    return $fileURLOutput;
                }
                break;
            case 'list':
                if (!empty($entry[$fieldData['fieldIds']])) {
                    $listArray = unserialize($entry[$fieldData['fieldIds']]);
                    if (is_array($listArray[0])) {
                        $keys = array_keys($listArray[0]);
                        $listOutput = self::listTemplate($listArray, $keys);
                    } else {
                        $listOutput = is_array($listArray) ? implode('<br>', array_filter($listArray)) : $listArray;
                    }

                    return $listOutput;
                }
                break;
            case 'post_tags':
                if (is_array($fieldData['fieldIds'])) {
                    $childEntry = array();
                    foreach ($fieldData['fieldIds'] as $fieldId) {
                        $childEntry[] = $entry[$fieldId];
                    }

                    $fieldData = implode(', ', array_filter($childEntry));
                } else {
                    $fieldData = $entry[$fieldData['fieldIds']];
                }

                return $fieldData;
                break;
            case 'post_category':
                $allPostCategories = array();
                $fieldData = $entry[$fieldData['fieldIds']];
                $fieldData = explode(',', $fieldData);
                foreach ($fieldData as $postCategory) {
                    $postCategory = substr($postCategory, 0, strpos($postCategory, ':'));
                    $allPostCategories[] = $postCategory;
                }
                $allPostCategories = implode(', ', $allPostCategories);

                return $allPostCategories;
                break;
            case 'post_image':
                $fieldData = $entry[$fieldData['fieldIds']];
                $fieldData = substr($fieldData, 0, strpos($fieldData, '|:|'));
                $fieldData = '<img src=' . $fieldData . '>';

                return $fieldData;
                break;
            case 'post_custom_field':
                if (is_array($fieldData['fieldIds'])) {
                    $childEntry = array();
                    foreach ($fieldData['fieldIds'] as $fieldId) {
                        $childEntry[] = $entry[$fieldId];
                    }

                    $fieldData = implode(', ', array_filter($childEntry));
                } else {
                    $fieldData = $entry[$fieldData['fieldIds']];
                }

                return $fieldData;
                break;
            case 'created_by':
                if (!empty($entry[$fieldData['fieldIds']])) {
                    $fieldData = get_user_by('id', $entry[$fieldData['fieldIds']])->user_login;
                } else {
                    $fieldData = '';
                }

                return $fieldData;
                break;
            case 'product':
                if (is_array($fieldData['fieldIds'])) {
                    $childEntry = array();
                    foreach ($fieldData['fieldIds'] as $fieldId) {
                        $childEntry[] = $entry[$fieldId];
                    }
                    $fieldData = implode(' - ', array_filter($childEntry));
                } elseif (strpos($entry[$fieldData['fieldIds']], '|')) {
                    $fieldData = str_replace('|', ' - ', $entry[$fieldData['fieldIds']]);
                } else {
                    $fieldData = $entry[$fieldData['fieldIds']];
                }

                return $fieldData;
                break;
            case 'option':
                if (is_array($fieldData['fieldIds'])) {
                    $childEntry = array();
                    foreach ($fieldData['fieldIds'] as $fieldId) {
                        $childEntry[] = str_replace('|', ' - ', $entry[$fieldId]);
                    }
                    $fieldData = implode('<br>', array_filter($childEntry));
                } elseif (strpos($entry[$fieldData['fieldIds']], '|')) {
                    $fieldData = str_replace('|', ' - ', $entry[$fieldData['fieldIds']]);
                } else {
                    $fieldData = $entry[$fieldData['fieldIds']];
                }

                return $fieldData;
                break;
            case 'shipping':
                if (strpos($entry[$fieldData['fieldIds']], '|')) {
                    $fieldData = str_replace('|', ' - ', $entry[$fieldData['fieldIds']]);
                } else {
                    $fieldData = $entry[$fieldData['fieldIds']];
                }

                return $fieldData;
                break;
            case 'number':
            case 'total':
                $numberFormat = get_option('wdtNumberFormat') ? get_option('wdtNumberFormat') : 1;

                if ($numberFormat == 1) {
                    $fieldData = str_replace('.', ',', $entry[$fieldData['fieldIds']]);
                } else {
                    $fieldData = $entry[$fieldData['fieldIds']];
                }

                return $fieldData;
                break;
            case 'signature':
                $fieldData = pathinfo($entry[$fieldData['fieldIds']], PATHINFO_FILENAME);
                return site_url() . "?page=gf_signature&signature={$fieldData}";
                break;
            case 'date_created':
                $fieldData = gmdate('Y-m-d H:i:s', \GFCommon::get_local_timestamp(strtotime($entry[$fieldData['fieldIds']])));
                return $fieldData;
                break;
            case 'chainedselect':
                foreach ($fieldData['fieldIds'] as $fieldId) {
                    $childEntry[] = $entry[$fieldId];
                }

                return implode(', ', array_filter($childEntry));
                break;
            // for field types: text, html, date, select, radio, phone, website, post_title, post_content, post_excerpt, quantity
            default:
                $fieldData = $entry[$fieldData['fieldIds']];

                return $fieldData;
                break;
        }

    }

    /**
     * Include template for list field table
     *
     * @param $listArray
     * @param $keys
     *
     * @return mixed
     */
    public static function listTemplate($listArray, $keys)
    {
        ob_start();
        include WDT_GF_ROOT_PATH . 'templates/list_field.inc.php';
        $gravityListField = apply_filters('wdt_gravity_list_field', ob_get_contents());
        ob_end_clean();

        return $gravityListField;
    }

    /**
     * Function that extend table config before saving table to the database
     *
     * @param $tableConfig - array that contains table configuration
     *
     * @return mixed
     */
    public static function extendTableConfig($tableConfig)
    {

        if ($tableConfig['table_type'] !== 'gravity') {
            return $tableConfig;
        }

        $gravityData = json_decode(
            stripslashes_deep($_POST['gravity'])
        );

        //Sanitize Gravity Config
        $gravityData = self::sanitizeGravityConfig($gravityData);

        $advancedSettings = json_decode($tableConfig['advanced_settings']);
        $advancedSettings->gravity = array(
            'dateFilterLogic' => $gravityData->dateFilterLogic,
            'dateFilterFrom' => $gravityData->dateFilterFrom,
            'dateFilterTo' => $gravityData->dateFilterTo,
            'dateFilterTimeUnits' => $gravityData->dateFilterTimeUnits,
            'dateFilterTimePeriod' => $gravityData->dateFilterTimePeriod,
            'showDeletedRecords' => $gravityData->showDeletedRecords,
            'showCurrentUserRecords' => $gravityData->showCurrentUserRecords,
            'hasServerSideIntegration' => $gravityData->hasServerSideIntegration
        );

        $tableConfig['advanced_settings'] = json_encode($advancedSettings);

        return $tableConfig;
    }

    /**
     * Get field display headers
     *
     * @param $formId - Gravity Form ID
     * @param $fieldsIds - IDs of the fields to fetch labels
     *
     * @return array of columns headers (field labels)
     */
    public static function getColumnHeaders($formId, $fieldsIds)
    {
        $form = \GFAPI::get_form($formId);
        $columnHeaders = array();

        foreach ($form['fields'] as $formField) {
            if (in_array((int)$formField->id, $fieldsIds)) {

                $originalHeader = \WDTTools::generateMySQLColumnName($formField['label'], $columnHeaders);

                if (empty($columnHeaders[$originalHeader])) {
                    $columnHeaders[$originalHeader] = $formField->label;
                }
            }
        }

        foreach (array_intersect($fieldsIds, ['date_created', 'id', 'created_by', 'ip']) as $commonField) {
            if ($commonField === 'date_created') {
                $columnHeaders['datecreated'] = 'Entry Date';
            } else if ($commonField === 'id') {
                $columnHeaders['id'] = 'Entry ID';
            } else if ($commonField === 'created_by') {
                $columnHeaders['createdby'] = 'User';
            } else {
                $columnHeaders['ip'] = 'User IP';
            }
        }

        return $columnHeaders;
    }

    /**
     * Get List of all possible values for column
     *
     * @param $column
     * @param null $tableData
     * @param $filterByUserId
     *
     * @return array
     */
    public static function getPossibleGFValuesRead($column, $tableData = null, $filterByUserId)
    {
        $searchCriteria = [];
        $parentTable = $column->getParentTable();
        $content = $parentTable->getTableContent();
        $content = json_decode($content);
        $fieldsIds = $content->fieldIds;
        $form = \GFAPI::get_form($content->formId);
        $fieldsData = self::getFieldsData($form, $fieldsIds);

        $columnFieldIds = null;
        foreach ($fieldsData as $fieldData) {
            if ($fieldData['label'] == $column->getOriginalHeader()) {
                $columnFieldIds = $fieldData['fieldIds'];
                break;
            }
        }

        if (in_array($column->getFilterType(), ['select', 'multiselect'], true) && $column->getDataType() === "string") {
            $paging = array('offset' => 0, 'page_size' => $column->getPossibleValuesAjax());
        } elseif ($column->getFilterType() === 'checkbox') {
            $paging = array('offset' => 0, 'page_size' => 200);
        }
        if (!empty($_POST['q'])) {
            $paging = ['offset' => 0, 'page_size' => 1000];
            $searchCriteria['field_filters'][] = ['key' => $columnFieldIds, 'value' => $_POST['q'], 'operator' => 'contains'];
        }

        $entries = \GFAPI::get_entries($content->formId, $searchCriteria, null, $paging);
        $possibleValues = [];
        foreach ($entries as $entry) {
            if (is_array($columnFieldIds)) {
                $distinctValueArr = [];
                foreach ($columnFieldIds as $columnFieldId) {
                    $distinctValueArr[] = $entry[$columnFieldId];
                }
                $distinctValue = implode(' ', $distinctValueArr);
            } else {
                $distinctValue = $entry[$columnFieldIds];
            }
            if (!in_array($distinctValue, $possibleValues, true)) {
                $possibleValues[] = $distinctValue;
            }
        }
        return $possibleValues;
    }

    /**
     * Loads the template for modal and fill it with data for
     * both New and Edit
     *
     * @param $form
     * @param $hasCaptchaField
     */
    public static function editModalConstruct($form, $hasCaptchaField)
    {
        ob_start();
        include \GFCommon::get_base_path() . '/entry_detail.php';
        include \GFCommon::get_base_path() . '/form_display.php';
        include WDT_GF_ROOT_PATH . 'templates/edit_modal.php';
        echo ob_get_clean();
        wp_die();

    }

    /**
     * Prepare the data for the data for both New and Edit modal
     *
     * @throws \Exception
     */
    public static function wdtSaveGFTableFrontend()
    {
        if (!wp_verify_nonce($_POST['wdtNonce'], 'wdtFrontendEditTableNonce' . (int)$_POST['table_id'])) {
            exit();
        }

        $tableData = \WDTConfigController::loadTableFromDB((int)$_POST['table_id']);

        //If current user cannot edit - do nothing
        if (!wdtCurrentUserCanEdit($tableData->editor_roles, $_POST['table_id'])) {
            exit();
        }

        $content = json_decode($tableData->content);
        $formId = $content->formId;
        $form = \GFAPI::get_form($formId);
        $hasCaptchaField = self::hasCaptchaField($form);

        self::editModalConstruct($form, $hasCaptchaField);

    }

    /**
     * Check if form has captcha field
     *
     * @param $form
     *
     * @return bool
     */
    private static function hasCaptchaField($form)
    {
        if (is_array($form['fields'])) {
            foreach ($form['fields'] as $field) {
                if (($field->type == 'captcha' || $field->inputType == 'captcha') && !in_array($field->captchaType, array('simple_captcha', 'math'))) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Deletes an entry from frontend
     */
    public static function wdtDeleteGFEntry()
    {

        if (!wp_verify_nonce($_POST['wdtNonce'], 'wdtFrontendEditTableNonce' . (int)$_POST['table_id'])) {
            exit();
        }

        $tableData = \WDTConfigController::loadTableFromDB($_POST['table_id']);

        //If current user cannot edit - do nothing
        if (!wdtCurrentUserCanEdit($tableData->editor_roles, $_POST['table_id'])) {
            exit();
        }

        //Calls API method for entry deletation
        \GFAPI::delete_entry($_POST['entry_id']);

        exit();
    }

    /**
     * In case of editing set Entry ID or set 0 for new entries
     *
     * @return int
     */
    public static function wdtPreSaveLead()
    {
        return isset($_POST['entry_id']) ? (int)$_POST['entry_id'] : 0;
    }

    /**
     * If we edit mulipage form entry, set it as single page
     *
     * @param $page_number
     * @param $form
     * @param $current_page
     * @param $field_values
     *
     * @return int
     */
    public static function wdtTargetPage($page_number, $form, $current_page, $field_values)
    {
        if (isset($_POST['entry_id'])) {
            $page_number = 0;
        }
        return $page_number;
    }


    /**
     * Remove no Duplicates option validation when editing an existing entry
     *
     * @param $form
     *
     * @return mixed
     */
    public static function wdtEditValidation($form)
    {
        if (isset($_POST['entry_id'])) {
            foreach ($form['fields'] as &$field) {
                if ($field['noDuplicates']) {
                    $field['noDuplicates'] = false;
                }
            }
        }
        return $form;
    }

    /**
     * Modify the where part of the query so it cast the time correctly
     *
     * @param $sql
     * @param $queries
     * @param $type
     * @param $primary_table
     * @param $primary_id_column
     * @param $context
     *
     * @return mixed
     */
    public static function wdtModifyTimeQuery($sql, $queries, $type, $primary_table, $primary_id_column, $context)
    {
        global $wpdb;
        if (isset($_POST['wdtNonce']) && isset($context)) {
            foreach ($context->meta_query->get_clauses() as &$clause) {
                if (in_array($clause['key'], self::$timeFieldIds) && self::$timeFieldIds['format'] === '12') {
                    $wpdb->set_sql_mode();
                    $sql['where'] = str_replace($clause['alias'] . ".meta_value", "STR_TO_DATE(" . $clause['alias'] . ".meta_value, '%h:%i %p')", $sql['where']);
                }
            }
        }
        return $sql;
    }

    /**
     * Add Gravity Forms Integration activation on wpDataTables settings page
     */
    public static function addGravityActivation()
    {
        ob_start();
        include WDT_GF_ROOT_PATH . 'templates/activation.inc.php';
        $activation = ob_get_contents();
        ob_end_clean();

        echo $activation;
    }

    /**
     * Enqueue Gravity Forms Integration add-on files on back-end settings page
     */
    public static function wdtGravityEnqueueBackendSettings()
    {
        if (self::$initialized) {
            wp_enqueue_script(
                'wdt-gf-settings',
                WDT_GF_ROOT_URL . 'assets/js/wdt.gf.settings.js',
                array(),
                WDT_GF_VERSION,
                true
            );
        }
    }

    /**
     * @param $transient
     *
     * @return mixed
     */
    public static function wdtCheckUpdateGravity($transient)
    {
        if (class_exists('WDTTools')) {

            $pluginSlug = plugin_basename(__FILE__);

            if (empty($transient->checked)) {
                return $transient;
            }

            $purchaseCode = get_option('wdtPurchaseCodeStoreGravity');

            $envatoTokenEmail = get_option('wdtEnvatoTokenEmailGravity');

            // Get the remote info
            $remoteInformation = WDTTools::getRemoteInformation('wdt-gravity-integration', $purchaseCode, $envatoTokenEmail);

            // If a newer version is available, add the update
            if ($remoteInformation && version_compare(WDT_GF_VERSION, $remoteInformation->new_version, '<')) {
                $remoteInformation->package = $remoteInformation->download_link;
                $transient->response[$pluginSlug] = $remoteInformation;
            }
        }

        return $transient;
    }

    /**
     * @param $response
     * @param $action
     * @param $args
     *
     * @return bool|mixed
     */
    public static function wdtCheckInfoGravity($response, $action, $args)
    {

        if (class_exists('WDTTools')) {

            $pluginSlug = plugin_basename(__FILE__);

            if ('plugin_information' !== $action) {
                return $response;
            }

            if (empty($args->slug)) {
                return $response;
            }

            $purchaseCode = get_option('wdtPurchaseCodeStoreGravity');

            $envatoTokenEmail = get_option('wdtEnvatoTokenEmailGravity');

            if ($args->slug === $pluginSlug) {
                return WDTTools::getRemoteInformation('wdt-gravity-integration', $purchaseCode, $envatoTokenEmail);
            }
        }

        return $response;
    }

    public static function addMessageOnPluginsPageGravity()
    {
        /** @var bool $activated */
        $activated = get_option('wdtActivatedGravity');

        /** @var string $url */
        $url = get_site_url() . '/wp-admin/admin.php?page=wpdatatables-settings&activeTab=activation';

        /** @var string $redirect */
        $redirect = '<a href="' . $url . '" target="_blank">' . __('settings', 'wpdatatables') . '</a>';

        if (!$activated) {
            echo sprintf(' ' . __('To receive automatic updates license activation is required. Please visit %s to activate Gravity Forms integration for wpDataTables.', 'wpdatatables'), $redirect);
        }
    }

    public static function addMessageOnUpdateGravity($reply, $package, $updater)
    {
        if (isset($updater->skin->plugin_info['Name']) && $updater->skin->plugin_info['Name'] === get_plugin_data( __FILE__ )['Name']) {
            /** @var string $url */
            $url = get_site_url() . '/wp-admin/admin.php?page=wpdatatables-settings&activeTab=activation';

            /** @var string $redirect */
            $redirect = '<a href="' . $url . '" target="_blank">' . __('settings', 'wpdatatables') . '</a>';

            if (!$package) {
                return new WP_Error(
                    'wpdatatables_gravity_not_activated',
                    sprintf(' ' . __('To receive automatic updates license activation is required. Please visit %s to Gravity Forms integration for wpDataTables.', 'wpdatatables'), $redirect)
                );
            }

            return $reply;
        }

        return $reply;
    }
}
