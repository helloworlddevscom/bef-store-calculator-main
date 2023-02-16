var EddAcp = (function EddAcpClass() {

    EddAcp.prototype = Object.create(Object.prototype);

    var $ = jQuery;

    function EddAcp(element, nobind) {
        this.element = element;
        this.testing = false;
        this.initElems();
        nobind = (typeof nobind === 'undefined') ? false : true;
        if (!nobind) {
            this.initEvents();
        }
    }

    EddAcp.prototype.initElems = function () {
        // Get purchase elements
        this.eddPurchaseWrapper = this.element.find('> .edd_purchase_submit_wrapper');
        this.eddPurchaseButton = this.eddPurchaseWrapper.find('> .edd-add-to-cart');
        // Get the popup element
        this.popup = this.element.parent().find('.edd-acp-popup');
        // Get the item name
        this.itemName = this.popup.find('.edd-acp-item-name').val();
        // Get the variable price option element, if available
        this.priceOptions = this.element.find('div.edd_price_options');
        // Set the url of the "continue to checkout button" to the checkout page
        this.popup.find('a.edd-acp-goto-checkout').attr('href', window.edd_scripts ? edd_scripts.checkout_page : '#');

        return this;
    };

    EddAcp.prototype.initEvents = function () {
        this.eddPurchaseButton.click(this.onPurchaseClick.bind(this));
        return this;
    };

    EddAcp.prototype.onPurchaseClick = function (event) {
        if (this.eddPurchaseButton.is('.edd-free-download')) {
            return;
        }
        if (this.element.find('.edd_action_input').val() !== 'add_to_cart') {
            return;
        }
        // Item name to show on popup
        var name = this.itemName;
        // Get the default quantity field
        var eddQtyField = this.element.find('.edd-item-quantity');
        var singleQty = eddQtyField.length > 0 ? parseInt(eddQtyField.val()) : 0;
        // Get selected price options
        var priceOptions = this.getSelectedPriceOption();
        var numItemsSelected = priceOptions.length > 0 ? priceOptions.length : 1;
        // If no selection, or variable pricing disabled, skip the below block
        if (priceOptions.length > 0) {
            // Use hyphen between item name and selected options
            name += ' - ';
            // Put selections in array, to join by comma later
            var optionStrings = [];
            for (var i in priceOptions) {
                // If the quantity is 1 and only one option is selected, do not show quantity
                var qtyStr = '';
                if (priceOptions[i].qty > 1 || priceOptions.length > 1) {
                    qtyStr = ' x' + priceOptions[i].qty;
                } else if (eddQtyField.length > 0 && singleQty > 1) {
                    qtyStr = ' x' + singleQty;
                }
                // Generate string for selected price option and add to array
                var optionString = ' ' + priceOptions[i].name + qtyStr;
                optionStrings.push(optionString);
            }
            // Join options by comma and add to item name
            name += optionStrings.join(', ');
        } else if (singleQty > 1) {
            name += ' - x' + singleQty;
        }
        // Set the item name on popup element
        this.popup.find('strong.item-name').text(name);
        // Hide both singular and plural texts
        this.popup.find('div.edd-acp-popup-singular, div.edd-acp-popup-plural').hide();

        if (numItemsSelected > 1 || singleQty > 1) {
            this.popup.find('div.edd-acp-popup-plural').show();
        } else {
            this.popup.find('div.edd-acp-popup-singular').show();
        }

        // Show the popup
        this.showPopup();

        if (this.testing) {
            event.stopPropagation();
            event.preventDefault();
        }
    };

    EddAcp.prototype.showPopup = function() {
        // Hide popup
        this.popup.css({ visibility: 'hidden' });
        // Initialize the popup
        this.popup.bPopup({
            positionStyle: 'fixed',
            speed: 100,
            followSpeed: 1,
            closeClass: 'edd-acp-close-popup',
            position: ['auto', 'auto']
        });
        // Show popup
        this.popup.css({ visibility: 'visible' });
    };

    EddAcp.prototype.getSelectedPriceOption = function () {
        if (this.priceOptions.length === 0) {
            return [];
        }
        var selected = [];
        // For each price option
        this.priceOptions.find('> ul > li').each(function (i, l) {
            // Get the label and quantity wrapper
            var label = $(l).find('> label');
            var qtyWrapper = $(l).find('> div.edd_download_quantity_wrapper');
            // If the option is selected
            if (label.find('input').is('[type="checkbox"]:checked, [type="radio"]:checked')) {
                // Get the option name and entered quantity
                var name = label.find('.edd_price_option_name').text();
                var qty = qtyWrapper.length === 0
                    ? 1
                    : parseInt(qtyWrapper.find('input.edd-item-quantity').val());
                // Add selection
                selected.push({
                    name: name,
                    qty: qty
                });
            }
        });
        console.log(selected);
        return selected;
    };

    return EddAcp;
}());

jQuery(document).ready(function () {
    window.edd_acp = {};
    // Instances array
    window.edd_acp.instances = [];
    // Go through each download and init instance
    jQuery('form.edd_download_purchase_form').each(function () {
        window.edd_acp.instances.push(new EddAcp(jQuery(this)));
    });
});
