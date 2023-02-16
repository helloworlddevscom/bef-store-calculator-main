(function ($) {

    $(document).ready(function () {

        // On Reset btn click
        $('#edd-acp-reset').click(function(e) {
            // Confirm with message. If not accepted, prevent event default behavior (which is to submit the form)
            if (!confirm(EddAcpSettings.messages.confirmReset)) {
                e.preventDefault();
                e.stopPropagation();
            }
        });

        // Iterate all colorpicker containers
        $('.edd-acp-colorpicker').each(function() {
            var self = $(this);
            var field = self.find('input.edd-acp-colorpicker-value');
            field.spectrum({
                color: field.val(),
                allowEmpty: true,
                showInput: true,
                showAlpha: true,
                showPalette: true,
                palette: [],
                localStorageKey: 'spectrum.homepage',
                showInitial: true,
                preferredFormat: 'rgb',
            });
        });

        /**
         * Click handler for when the preview button is clicked.
         */
        $('.edd-acp-preview').click(function(e) {
            var settings = getSettings();
            var container = $('#edd-acp-preview-popup-container');
            getPreview(settings, function(response) {
                container.empty();
                // Destroy existing popup
                var popup = container.data('popup');
                if (popup) {
                    $('.edd-acp-popup').remove();
                    $('.b-modal').remove();
                    container.data('popup', null);
                }
                // Insert recevied HTML
                container.html(response);
                // Create popup instance
                popup = new EddAcp(container.parent(), true);
                // Show it
                popup.onPurchaseClick(e);
                // Save the instance for future destruction
                container.data('popup', popup);
            });
            e.preventDefault();
        });

        /**
         * Gets the current settings values from the form.
         * 
         * @returns {Object} The settings values as an object.
         */
        function getSettings() {
            var settings = {};
            for (var key in EddAcpSettings.options) {
                var option = EddAcpSettings.options[key];
                if (option.type === 'header') {
                    continue;
                }
                var optionElem = $('[name^="edd_settings[acp]['+option.id+']"]');
                // check for composite options
                if (optionElem.length > 1 && typeof option.default === 'object') {
                    settings[key] = {};
                    var properties = Object.getOwnPropertyNames(option.default);
                    for (var i in properties) {
                        var property = properties[i];
                        var propertyElem = $('[name="edd_settings[acp]['+option.id+']['+property+']"]');
                        settings[key][property] = getSettingValue(option.id + '-' + property, propertyElem);
                    }
                } else {
                    settings[key] = getSettingValue(option.id, optionElem);
                }
            }
            return settings;
        }

        /**
         * Gets a setting's value from its element.
         * 
         * @param {string} id The ID of the option element.
         * @param {Element} optionElem The element.
         * @returns The value
         */
        function getSettingValue(id, optionElem) {
            var value = optionElem.val();
            // Check if checbox
            if (optionElem.length > 1 && $(optionElem.get(1)).is(':checked')) {
                value = $(optionElem.get(1)).val();
            }
            // Check for textarea editors
            if (optionElem.is('textarea')) {
                value = tinymce.get(id).getContent();
            }
            // Check for select elements
            if (optionElem.is('select')) {
                value = optionElem.find('option:selected').val();
            }
            return value;
        }

        /**
         * Gets a preview from the server using a specific settings set.
         * 
         * @param {Object} settings The settings to use to generate the preview.
         * @param {Function} callback The callback to call after a response is received from the server.
         */
        function getPreview(settings, callback) {
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'edd_acp_preview',
                    settings: settings
                },
                success: function(response) {
                    callback(response);
                }
            });
        }

    });

})(jQuery);
