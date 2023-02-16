/**
 * wpDataTable Gravity Form config object
 *
 * Contains all the settings for the gravity form based wpDataTable.
 * setter methods adjust the binded jQuery elements
 *
 * @author Milos Timotic
 * @since 14.07.2017
 */
var wpdatatable_gf_config = {
    dateFilterLogic: null,
    dateFilterFrom: null,
    dateFilterTo: null,
    dateFilterTimeUnits: null,
    dateFilterTimePeriod: null,
    fields: null,
    formId: null,
    hasServerSideIntegration: 1,
    showDeletedRecords: 0,
    showCurrentUserRecords: 0,

    setDateFilterLogic: function (logic) {
        wpdatatable_gf_config.dateFilterLogic = logic;
        jQuery('#wdt-gf-date-filter-logic').selectpicker('val', logic);
    },
    setDateFilterFrom: function (dateFrom) {
        wpdatatable_gf_config.dateFilterFrom = dateFrom;
        jQuery('#wdt-gf-date-filter-from').val(dateFrom);
    },
    setDateFilterTo: function (dateTo) {
        wpdatatable_gf_config.dateFilterTo = dateTo;
        jQuery('#wdt-gf-date-filter-to').val(dateTo);
    },
    setDateFilterTimeUnits: function (timeUnits) {
        wpdatatable_gf_config.dateFilterTimeUnits = timeUnits;
        jQuery('#wdt-gf-date-filter-time-units').val(timeUnits);
    },
    setDateFilterTimePeriod: function (timePeriod) {
        wpdatatable_gf_config.dateFilterTimePeriod = timePeriod;
        jQuery('#wdt-gf-date-filter-time-period').selectpicker('val', timePeriod);
    },
    setFields: function (fields) {
        wpdatatable_gf_config.fields = fields;
        jQuery('#wdt-gravity-form-column-picker').selectpicker('val', fields);
    },
    setFormId: function (formId) {
        wpdatatable_gf_config.formId = formId;
        jQuery('#wdt-gravity-form-picker').selectpicker('val', formId);
    },
    addEntryIdColumn: function (fieldIds) {
        if (!fieldIds.includes('id')) {
            fieldIds.push('id');
            if (!_.filter(wpdatatable_config.columns, {orig_header: 'id'}).length) {
                wpdatatable_config.columns.push(new WDTColumn({
                    orig_header: 'id',
                    visible: 0,
                    id_column: 1
                }));
            }
        }
        return fieldIds;
    },
    setShowDeletedRecords: function (showDeletedRecords) {
        wpdatatable_gf_config.showDeletedRecords = showDeletedRecords;
        jQuery('#wdt-gf-toggle-deleted-records').prop('checked', showDeletedRecords);
    },
    setShowCurrentUserRecords: function (showCurrentUserRecords) {
        wpdatatable_gf_config.showCurrentUserRecords = showCurrentUserRecords;
        jQuery('#wdt-gf-toggle-current-user').prop('checked', showCurrentUserRecords);
        jQuery('#wdt-edit-only-own-rows').prop('checked', showCurrentUserRecords);
        if (showCurrentUserRecords){
            jQuery('.own-rows-editing-settings-block').animateFadeIn();
            jQuery('.show-all-rows-editing-settings-block').animateFadeIn();
            wpdatatable_config.userid_column_id = null;
            wpdatatable_config.showAllRows = false;
        } else {
            jQuery('.own-rows-editing-settings-block').animateFadeOut();
            jQuery('.show-all-rows-editing-settings-block').animateFadeOut();
            wpdatatable_config.userid_column_id = null;
            wpdatatable_config.showAllRows = false;
        }
    },
    disableForeignKey: function () {
        jQuery("select#wdt-column-values option[value='foreignkey']").attr('disabled', true );
    },
    enableForeignKey: function () {
        jQuery("select#wdt-column-values option[value='foreignkey']").attr('disabled', false );
    },
    disableEntryId: function () {
        jQuery("select#wdt-gravity-form-column-picker option[value='id']").attr('disabled', true );
        jQuery('#wdt-gravity-form-column-picker').selectpicker('refresh');
    },
    enableEntryId: function () {
        jQuery("select#wdt-gravity-form-column-picker option[value='id']").attr('disabled', false);
        jQuery('#wdt-gravity-form-column-picker').selectpicker('refresh');
    },
    disableRangeSlider: function () {
        if (jQuery("#wdt-column-filter-type").selectpicker('val', "number-range"))  {
            jQuery("#wdt-column-range-slider").prop('disabled', true)
                .parent()
                .parent()
                .addClass('c-gray')
                .siblings('h4')
                .addClass('c-gray');
        }
    },
    /**
     *  Disable editing option not implemented in Add-on
     */
    disableTableEditingOptions: function () {
        jQuery('#wdt-popover-tools, #wdt-inline-editable, #wdt-mysql-table-name, #wdt-id-editing-column')
            .prop('disabled', true)
            .parent()
            .addClass('c-gray')
            .siblings('h4')
            .addClass('c-gray');

        jQuery('#wdt-id-editing-column, #wdt-user-id-column')
            .prop('disabled', true)
            .parents('div.select')
            .siblings('h4')
            .addClass('c-gray');
        jQuery('#wdt-mysql-table-name').val('');
    },

    /**
     * Disable editing option per column
     */
    disableColumnEditingOptions: function () {
        jQuery('#wdt-column-not-null, #wdt-editing-default-value')
            .prop('disabled', true)
            .parent()
            .parent()
            .addClass('c-gray')
            .siblings('h4')
            .addClass('c-gray');

        jQuery('#wdt-column-editor-input-type')
            .prop('disabled', true)
            .parents('div.form-group')
            .siblings('h4')
            .addClass('c-gray');
        jQuery('#wdt-editing-default-value').val('');
    },
    getGFConfig: function () {
        return {
            dateFilterLogic: wpdatatable_gf_config.dateFilterLogic,
            dateFilterFrom: wpdatatable_gf_config.dateFilterFrom,
            dateFilterTo: wpdatatable_gf_config.dateFilterTo,
            dateFilterTimeUnits: wpdatatable_gf_config.dateFilterTimeUnits,
            dateFilterTimePeriod: wpdatatable_gf_config.dateFilterTimePeriod,
            fields: wpdatatable_gf_config.fields,
            formId: wpdatatable_gf_config.formId,
            hasServerSideIntegration: wpdatatable_gf_config.hasServerSideIntegration,
            showDeletedRecords: wpdatatable_gf_config.showDeletedRecords,
            showCurrentUserRecords: wpdatatable_gf_config.showCurrentUserRecords
        };
    },

    /**
     * Initializes gravity config from JSON for edit table
     * @param tableJSON
     */
    initGFFromJSON: function(tableJSON) {
        // Fill "Choose a Gravity Form" dropdown and trigger change so that
        // "Choose fields to show as columns" dropdown will be populated with form fields
        var content = JSON.parse(tableJSON.content);
        jQuery('#wdt-gravity-form-picker').selectpicker('val', content.formId).change();

        //Show server-side toggle
        jQuery('.wdt-table-settings .wdt-server-side-processing').animateFadeIn();

        //Server-side initialization
        wpdatatable_config.setServerSide( tableJSON.server_side );

        let fieldIds = content.fieldIds;

        //Set editing
        if (parseInt( tableJSON.editable )) {
            wpdatatable_config.setEditable(parseInt(tableJSON.editable));
            fieldIds = wpdatatable_gf_config.addEntryIdColumn(content.fieldIds);
            wpdatatable_gf_config.disableEntryId();
        }

        wpdatatable_gf_config.setFields(fieldIds);

        var gravityData = JSON.parse(tableJSON.advanced_settings).gravity;
        wpdatatable_gf_config.setShowDeletedRecords(gravityData.showDeletedRecords);
        wpdatatable_gf_config.setDateFilterLogic(gravityData.dateFilterLogic);
        wpdatatable_gf_config.setDateFilterFrom(gravityData.dateFilterFrom);
        wpdatatable_gf_config.setDateFilterTo(gravityData.dateFilterTo);
        wpdatatable_gf_config.setDateFilterTimeUnits(gravityData.dateFilterTimeUnits);
        wpdatatable_gf_config.setDateFilterTimePeriod(gravityData.dateFilterTimePeriod);
        wpdatatable_gf_config.setShowCurrentUserRecords(gravityData.showCurrentUserRecords);
        if (tableJSON.server_side){
            wpdatatable_gf_config.disableForeignKey();
            wpdatatable_gf_config.disableRangeSlider();
        } else {
            wpdatatable_gf_config.enableForeignKey();
        }


        // Trigger change event to show selected logic block
        jQuery('#wdt-gf-date-filter-logic').change();
    }

};