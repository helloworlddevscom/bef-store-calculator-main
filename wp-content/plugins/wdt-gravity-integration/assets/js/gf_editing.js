var wpDataTablesEditors = wpDataTablesEditors || {};
wpDataTablesEditors.gravity = {};

(function($) {
    var modal = $('#wdt-frontend-modal');
    var modalBody = modal.find('.modal-body');
    var modalFooter = modal.find('.modal-footer');


    //Styling for previous and next buttons in multipage modal
    $("body").bind("DOMNodeInserted", function() {

        if (modal.find('input:submit')) {
            modal.find('input:submit').hide();
        }
    });

    //Call Gravity methods for specific input types
    wpDataTablesEditors.gravity.callGravityMethods = function() {
        if(window['gformInitDatepicker']) {gformInitDatepicker();}
        if(window['gformInitPriceFields']) {gformInitPriceFields();}
    };

    wpDataTablesEditors.gravity.findEntryId = function(tableDescription) {
        var rowIndex = wpDataTables[tableDescription.tableId].api().row( $(tableDescription.selector + ' tbody tr.selected') ).index();
        //Get column index
        var columnIndex = wpDataTables[tableDescription.tableId].api().column('id:name').index();
        //Get EntryId from table column
        var entry_id = wpDataTables[tableDescription.tableId].api().cell({row: rowIndex, column: columnIndex}).data();

        return entry_id;
    };

    /**
     * Prepare modal content for both New and Edit
     * @param data
     * @param tableId
     * @param type
     */
    wpDataTablesEditors.gravity.prepareEditForm = function( data, tableId, type ) {
        //Remove overlay
        wdtRemoveOverlay('#'+tableId);
        //Hide delete modal if it's present
        $('#wdt-delete-modal').modal('hide');
        //Check if there is Captcha error and disable submit
        if ($(data).filter('div.captcha-error').length) {
            modalBody.append(data).find('.wdt-gf-submit').prop("disabled",true);
        } else {
            //Add data to the modal and change action from admin-ajax
            if (type == 'new') {
                //Append empty, new form
                modalBody.append(data).find('form').attr('action', $('#wdt_home_page').val());
            } else {
                //Append filled inputs and wrap in form tag
                modalBody.append('<div class="gform_wrapper"><form id="wdt-gf-form">' + data + '</form></div>');
            }

            if (modalBody.find('.wp-editor-area')) {
                modalBody.find('.wp-editor-area').each(function() {
                    tinymce.execCommand('mceRemoveEditor', true, $(this).attr('id'));
                    tinymce.init({
                        selector: '#' + $(this).attr('id'),
                        menubar: false
                    });
                });
            }


            wpDataTablesEditors.gravity.callGravityMethods();

        }
        //Add input with table selector and buttons (OK and Cancel)
        modalFooter.append('<input id="wdt-gf-table-selector" type="hidden" value="'+tableId+'"/>')
            .append($('.wdt-gf-edit-dialog-button-block').show());
        //Disable file upload input
        if ($('div.ginput_container_fileupload').length) {
            $('div.ginput_container_fileupload').replaceWith($('.wdt-gf-file-editing-alert').show());
        }
        //Hide Gravity submit buttons
        modal.find('.gform_footer')
            .hide()
            .find('input:submit')
            .remove();
        modal.find('.gform_page_footer')
            .find('input:submit')
            .remove();

        //Show the modal
        modal.modal('show');
        //reset muliti-click preventing parameter
        singleClick = false;
    };

    /**
     * Ajax call which returns empty form for New modal
     * @param tableDescription
     */
    wpDataTablesEditors.gravity.new = function(tableDescription) {
        //Add overlay
        wdtAddOverlay('#'+tableDescription.tableId);
        $.ajax({
            url: tableDescription.adminAjaxBaseUrl,
            method: 'POST',
            data: {
                action: 'wdt_save_gf_table_frontend',
                table_id: tableDescription.tableWpId,
                wdtNonce: $('#wdtNonceFrontendEdit_' + tableDescription.tableWpId).val()
            },
            success: function (data) {
                wpDataTablesEditors.gravity.prepareEditForm(data, tableDescription.tableId, 'new');
            }
        });
    };

    /**
     * Ajax call which returns filed inputs for Edit modal
     * @param tableDescription
     */
    wpDataTablesEditors.gravity.edit = function(tableDescription) {
        //Add overlay
        wdtAddOverlay('#'+tableDescription.tableId);

        entry_id = wpDataTablesEditors.gravity.findEntryId(tableDescription);

        $.ajax({
            url: tableDescription.adminAjaxBaseUrl,
            method: 'POST',
            data: {
                action: 'wdt_save_gf_table_frontend',
                table_id: tableDescription.tableWpId,
                entry_id: entry_id,
                wdtNonce: $('#wdtNonceFrontendEdit_' + tableDescription.tableWpId).val()
            },
            success: function (data) {
                wpDataTablesEditors.gravity.prepareEditForm(data, tableDescription.tableId, 'edit');
            }
        });
    };

    /**
     * Ajax call which deletes chosen entry
     * @param tableDescription
     */
    wpDataTablesEditors.gravity.delete = function(tableDescription) {

        entry_id = wpDataTablesEditors.gravity.findEntryId(tableDescription);

        $.ajax({
            url: tableDescription.adminAjaxBaseUrl,
            method: 'POST',
            data: {
                action: 'wdt_delete_gf_table_row',
                table_id: tableDescription.tableWpId,
                entry_id: entry_id,
                wdtNonce: $('#wdtNonceFrontendEdit_' + tableDescription.tableWpId).val()
            },
            success: function () {
                wpDataTables[tableDescription.tableId].fnDraw(false);
                $('#wdt-delete-modal').modal('hide');
                wdtNotify(wpdatatables_edit_strings.success, wpdatatables_edit_strings.rowDeleted, 'success');
            }
        });
    };

    /**
     * Submit both Edit and New
     */
    $(document).on('click','button.wdt-gf-submit',function(e) {
        e.preventDefault();
        //Find Rich text
        var richText = $('#wdt-frontend-modal .modal-body textarea.wp-editor-area');
        if (richText.length){
            richText.each( function ( index, element) {
                let richTextId = element.id
                let editData = $('#' + richTextId + '_ifr').contents().find('[data-id="' + richTextId + '"]').html();
                $('#' + richTextId).html(editData)
            })
        }
        //Find form
        var form = modalBody.find('form');
        //Get the data
        var formData = form.serializeArray();
        //Add overlay
        $('#wdt-frontend-modal .modal-dialog .wdt-preload-layer').animateFadeIn();
        //Find table
        var wpDataTable = wpDataTables[$('#wdt-gf-table-selector').val()];
        wdtAddOverlay('#'+$('#wdt-gf-table-selector').val());
        $.ajax({
            type: "POST",
            url: window.location,
            data: formData,
            success: function(data)
            {
                //Remove overlay
                wdtRemoveOverlay('#'+$('#wdt-gf-table-selector').val());
                //Remove preloader
                $('#wdt-frontend-modal .modal-dialog .wdt-preload-layer').animateFadeOut();
                //Refresh the table
                wpDataTable.fnDraw(false);
                // If there is confirmation message or redirect URL
                if ($(data).find('.gform_confirmation_message').length || data.indexOf('gformRedirect()') > 0 ) {
                    modalFooter.empty();
                    setTimeout(function () {
                        modal.find('.modal-body').html('');
                        $('.modal').modal('hide');
                    }, 3000);
                }
                //Add data to the modal
                modalBody.html(data);
                wpDataTablesEditors.gravity.callGravityMethods();
                //Disable file upload input
                if ($( 'div.ginput_container_fileupload' ).length) {
                    $('div.ginput_container_fileupload').replaceWith($('.wdt-gf-file-editing-alert').show());
                }
                //Hide Gravity submit buttons
                modal.find('.gform_footer').hide();
            }
        });

    });

})(jQuery);