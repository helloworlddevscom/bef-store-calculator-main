/**
 * JS Controller for wpDataTables & Gravity Forms integration
 * @author Alexander Gilmanov
 * @since 06.04.2017
 */
(function ($) {

    $(function () {

        var applyButtonEvent = typeof $('.wdt-apply').data('events') !== 'undefined' ? $('.wdt-apply').data('events').click[1] : null;

        /**
         * Pick a form
         */
        $('#wdt-gravity-form-picker').on('changed.bs.select', function (e) {
            if ($(this).val() !== '') {
                wpdatatable_gf_config.setFormId($(this).val());
                $.ajax({
                    url: ajaxurl,
                    data: {
                        action: 'wdt_gf_get_form_fields',
                        nonce: $('#wdtNonce').val(),
                        formId: $('#wdt-gravity-form-picker').val()
                    },
                    dataType: 'json',
                    method: 'POST',
                    success: function (data) {
                        if ($('#wdt-gf-column-container').hasClass('hidden')) {
                            $('#wdt-gf-column-container').animateFadeIn();
                            if($('#wdt-server-side').is(':checked')) {
                                $('.alert-server-side').animateFadeIn();
                            }
                        }
                        if ($('.wdt-server-side-processing').hasClass('hidden')) {
                            $('.wdt-table-settings .wdt-server-side-processing').animateFadeIn();
                            $('.wdt-table-settings .wdt-auto-refresh').animateFadeIn();
                        }
                        if (typeof data.error !== 'undefined') {
                            wdtNotify(wdtGfTranslationStrings.error, data.error, 'danger');
                        } else {
                            fillFormFields(data);
                            if (typeof wpdatatable_init_config !== 'undefined' && wpdatatable_init_config.table_type === 'gravity') {
                                var content = JSON.parse(wpdatatable_init_config.content);
                                let fieldIds =  content.fieldIds;
                                if(wpdatatable_config.editable) {
                                    fieldIds = wpdatatable_gf_config.addEntryIdColumn(fieldIds);
                                }
                                wpdatatable_gf_config.setFields(fieldIds);
                            } else {
                                wpdatatable_gf_config.setFields(wpdatatable_gf_config.fields);
                            }
                        }
                    }
                });
            } else {
                $('#wdt-gf-column-container').animateFadeOut();
            }
        });

        /**
         * Save table config when columns are selected and preview the table
         */
        $('#wdt-gravity-form-column-picker').on('change', function () {
            if ($(this).val()) {
                $('.wdt-apply').prop('disabled', false);
                if (!$('.display-settings-tab').is(':visible')) {
                    $('.display-settings-tab').animateFadeIn();
                    $('.table-sorting-filtering-settings-tab').animateFadeIn();
                    $('.table-tools-settings-tab').animateFadeIn();
                    $('.gravity-settings-tab').animateFadeIn();
                }
            } else {
                $('.wdt-apply').prop('disabled', true);
                $('.display-settings-tab').animateFadeOut();
                $('.table-sorting-filtering-settings-tab').animateFadeOut();
                $('.table-tools-settings-tab').animateFadeOut();
                $('.gravity-settings-tab').animateFadeOut();
            }
            let fieldIds = $(this).val()
            if (wpdatatable_config.editable) {
                fieldIds = wpdatatable_gf_config.addEntryIdColumn(fieldIds);
            }
            wpdatatable_gf_config.setFields(fieldIds);
        });

        /**
         * Show Gravity Form block if "Gravity Form" table type is selected
         */
        $('#wdt-table-type').change(function () {
            if ($(this).val() === 'gravity') {
                // Hide "Placeholders" tab
                $('.placeholders-settings-tab').hide();
                // Hide Switch View (Excel-like link)
                $('div.wdt-edit-buttons').hide();
                // Show "Choose a Gravity Form" block on "Data Source" tabpanel
                $('#wdt-gf-form-container').animateFadeIn();
                // Off default save event and bind event for saving gravity form table
                $('.wdt-apply').off('click').click(function () {
                    saveTableConfig();
                });
            } else {
                // Hide "Choose a Gravity Form" and "Choose fields to show as columns" blocks
                $('#wdt-gf-form-container').addClass('hidden');
                $('#wdt-gf-column-container').addClass('hidden');
                // Reset "Choose a Gravity Form" and "Choose fields to show as columns" values
                $('#wdt-gravity-form-picker').selectpicker('val', '');
                $('#wdt-gravity-form-column-picker').selectpicker('val', '');
                // Off gravity form save event and revert to default one
                $('.wdt-apply').off().bind('click', applyButtonEvent);
            }
            // Reset content and disable "Apply" button
            wpdatatable_config.content = '';
            $('.wdt-apply').prop('disabled', true);
        });

        /**
         * Toggle "Show form deleted records"
         */
        $('#wdt-gf-toggle-deleted-records').change(function () {
            wpdatatable_gf_config.setShowDeletedRecords($(this).is(':checked') ? 1 : 0);
        });

        /**
         * Toggle "Show current user records"
         */
        $('#wdt-gf-toggle-current-user').change(function () {
            wpdatatable_gf_config.setShowCurrentUserRecords($(this).is(':checked') ? 1 : 0);
        });

        //Set users only rows
        $('#wdt-edit-only-own-rows').on('change', function (e) {
            // e.preventDefault();
            wpdatatable_gf_config.setShowCurrentUserRecords($(this).is(':checked') ? 1 : 0);
        });

        /**
         * Change Date Filter From
         */
        $('#wdt-gf-date-filter-from').on('dp.change', function () {
            wpdatatable_gf_config.setDateFilterFrom($(this).val());
        });

        /**
         * Change Date Filter To
         */
        $('#wdt-gf-date-filter-to').on('dp.change', function () {
            wpdatatable_gf_config.setDateFilterTo($(this).val());
        });

        /**
         * Change Date Filter Time Units
         */
        $('#wdt-gf-date-filter-time-units').on('input keyup change', function () {
            wpdatatable_gf_config.setDateFilterTimeUnits($(this).val());
        });

        /**
         * Change Date Filter Time Period
         */
        $('#wdt-gf-date-filter-time-period').on('change', function () {
            wpdatatable_gf_config.setDateFilterTimePeriod($(this).val());
        });

        /**
         * "Filter by date" logic
         */
        $('#wdt-gf-date-filter-logic').on('change', function (e) {
            wpdatatable_gf_config.setDateFilterLogic($(this).val());
            if ($(this).val() === 'range') {
                $('.wdt-gf-date-range-block').animateFadeIn();
                $('.wdt-gf-last-x-block').addClass('hidden');
            } else if ($(this).val() === 'last') {
                $('.wdt-gf-last-x-block').animateFadeIn();
                $('.wdt-gf-date-range-block').addClass('hidden');
                $('#wdt-gf-date-filter-time-period').change();
            } else {
                $('.wdt-gf-last-x-block').addClass('hidden');
                $('.wdt-gf-date-range-block').addClass('hidden');
            }
        });

        /**
         * Initialize datetime picker for Gravity Form "Filter by date range" feature
         */
        var wdtDateFormat = wdtGfSettings.wdtDateFormat.replace('d', 'DD').replace('M', 'MMM').replace('m', 'MM').replace('y', 'YY');
        var wdtTimeFormat = wdtGfSettings.wdtTimeFormat.replace('H', 'H').replace('i', 'mm');

        $('#wdt-gf-date-filter-from').datetimepicker({
            format: wdtDateFormat + ' ' + wdtTimeFormat,
            showClear: true
        });
        $('#wdt-gf-date-filter-to').datetimepicker({
            format: wdtDateFormat + ' ' + wdtTimeFormat,
            showClear: true,
            useCurrent: false
        });
        $("#wdt-gf-date-filter-from").on("dp.change", function (e) {
            $('#wdt-gf-date-filter-to').data("DateTimePicker").minDate(e.date);
        });
        $("#wdt-gf-date-filter-to").on("dp.change", function (e) {
            $('#wdt-gf-date-filter-from').data("DateTimePicker").maxDate(e.date);
        });

        /**
         * Load the table for editing
         */
        if (typeof wpdatatable_init_config !== 'undefined' && wpdatatable_init_config.table_type === 'gravity') {
            $('#wdt-gf-form-container').animateFadeIn();
            $('.gravity-settings-tab').animateFadeIn();

            wpdatatable_gf_config.initGFFromJSON(wpdatatable_init_config);

            $('.wdt-apply').off('click').click(function () {
                saveTableConfig();
            });

            $('a[href="#editing-settings"]').on('click', function () {
                wpdatatable_gf_config.disableTableEditingOptions();
            });

            $('a[href="#column-editing-settings"]').on('click', function () {
                wpdatatable_gf_config.disableColumnEditingOptions();
            });

        }

        /**
         * Disable options that are not relevant for Gravity editing and add entry id column to the table
         */
        $('#wdt-editable').on('change', function(e) {
            if ($(this).prop('checked')){

                if(wpdatatable_gf_config.fields){
                    wpdatatable_gf_config.disableTableEditingOptions();
                    if(!wpdatatable_gf_config.fields.includes('id')) {
                        wpdatatable_gf_config.fields = wpdatatable_gf_config.addEntryIdColumn(wpdatatable_gf_config.fields);

                        $('#wdt-gravity-form-column-picker').selectpicker('val', wpdatatable_gf_config.fields);
                    }
                }
                wpdatatable_gf_config.disableEntryId();
            } else {
                wpdatatable_gf_config.enableEntryId();
            }
        });


        $('#wdt-server-side').on('change', function(e) {
            if (!$(this).prop('checked')) {
                wpdatatable_gf_config.enableEntryId();
            }
        });

    });

    /**
     * Populates "Gravity form column picker" selectbox with form fields
     * @param fields
     */
    function fillFormFields(fields) {
        var options = '';
        for (var i in fields) {
            options += '<option value="' + fields[i].id + '">' + fields[i].label + ' (' + fields[i].type + ')</option>';
        }
        $('#wdt-gravity-form-column-picker #wdt-gf-form-fields').html(options);
        $('#wdt-gravity-form-column-picker').selectpicker('refresh');
    }

    /**
     * Save Gravity based wpDataTable config to DB and preview the wpDataTable
     */
    function saveTableConfig() {
        if ($('#wdt-gravity-form-picker').val() && $('#wdt-gravity-form-column-picker').val()) {
            $('.wdt-preload-layer').animateFadeIn();
            $.ajax({
                url: ajaxurl,
                data: {
                    action: 'wdt_gravity_save_table_config',
                    gravity: JSON.stringify(wpdatatable_gf_config.getGFConfig()),
                    nonce: $('#wdtNonce').val(),
                    table: JSON.stringify(wpdatatable_config.getJSON())
                },
                dataType: 'json',
                method: 'POST',
                success: function (data) {
                    $('.wdt-preload-layer').animateFadeOut();
                    if (data.error) {
                        // Show error message
                        wdtNotify(wdtGfTranslationStrings.error, data.error, 'danger');
                    } else {
                        if (wpdatatable_config.editable) {
                            location.reload();
                        } else {
                            // Reinitialize table with returned data
                            wpdatatable_config.initFromJSON(data.table);
                            wpdatatable_config.setTableHtml(data.wdtHtml);
                            wpdatatable_config.setDataTableConfig(data.wdtJsonConfig);
                            wpdatatable_config.renderTable();

                            //Show server-side toggle
                            $('.wdt-table-settings .wdt-server-side-processing').animateFadeIn();
                            //Server-side initialization
                            wpdatatable_config.setServerSide( data.table.server_side );

                            //Set editing
                            if (parseInt( data.table.editable )) {
                                wpdatatable_config.setEditable(parseInt(data.table.editable));
                            }

                            // Show success message
                            wdtNotify(
                                wpdatatables_edit_strings.success,
                                wpdatatables_edit_strings.tableSaved,
                                'success'
                            );
                            // Remove disable from "Apply" button
                            $('.wdt-apply').prop('disabled', false);

                            wpdatatable_gf_config.disableTableEditingOptions();

                            // Show editing tab
                            if (jQuery('.editing-settings-tab').is(':hidden')) {
                                $('.editing-settings-tab').animateFadeIn();
                            }
                        }
                        if (window.location.href.indexOf("table_id=") === -1) {
                            window.history.replaceState(null, null, window.location.pathname + "?page=wpdatatables-constructor&source&table_id=" + data.table.id);
                        }
                        if (!wpdatatable_config.server_side || wpdatatable_config.server_side) { location.reload();}
                    }
                },
                error: function () {
                    wdtNotify(
                        wpdatatables_edit_strings.error,
                        '',
                        'danger'
                    );
                }
            });
        }
    }

})(jQuery);