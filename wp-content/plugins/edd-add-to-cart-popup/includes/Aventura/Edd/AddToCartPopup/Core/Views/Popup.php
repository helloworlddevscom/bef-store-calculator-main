<?php

use \Aventura\Edd\AddToCartPopup\Core\StyleRenderer;

// If the popup is already rendering, stop.
// Fixes nested popups when using EDD shortcodes that render purchase buttons
if ($this->getPlugin()->getPopup()->isRendering()) {
    return;
}

// Set rendering flag
$this->getPlugin()->getPopup()->setRendering(true);

// Get settings instance
$settings = !is_null($viewbag->settings)
    ? $viewbag->settings
    : $this->getPlugin()->getSettings();

// Get item name
if ($viewbag->downloadId === 0) {
    $itemName = __('Test', 'edd_acp');
} else {
    $itemName = the_title_attribute(array(
        'before' => '',
        'after'  => '',
        'echo'   => false,
        'post'   => $viewbag->downloadId
    ));
}
// Filter it
$filteredItemName = apply_filters('edd_acp_item_name', $itemName, $viewbag->downloadId, $settings);

do_action('edd_acp_before_popup_view', $viewbag->downloadId, $settings);

// Prepare some style vars
$border = $settings->getValue('border');
$shadow = $settings->getValue('shadow');
$btnBorder = $settings->getValue('btnBorder');
$padding = $settings->getValue('padding');
$btnPadding = $settings->getValue('btnPadding');
$size = $settings->getValue('size');

/**
 * Print styles.
 */
$popupStyles = array(
    '' => array(
        'width'            => $size['width'],
        'height'           => $size['height'],
        'background-color' => $settings->getValue('bgcolor'),
        'box-shadow'       => sprintf('0 0 %spx %s', $shadow['amount'], $shadow['color']),
        'border-width'     => sprintf('%spx', $border['width']),
        'border-style'     => sprintf('%s', $border['style']),
        'border-color'     => sprintf('%s', $border['color']),
        'border-radius'    => sprintf('%spx', $settings->getValue('borderRadius')),
        'padding-top'      => sprintf('%spx', $padding['top']),
        'padding-bottom'   => sprintf('%spx', $padding['bottom']),
        'padding-left'     => sprintf('%spx', $padding['left']),
        'padding-right'    => sprintf('%spx', $padding['right']),
    ),
    'p' => array(
        'color'            => $settings->getValue('textcolor'),
        'font-size'        => $settings->getValue('fontsize'),
    ),
);
$popupStylesFiltered = apply_filters('edd_acp_popup_styles', $popupStyles, $settings);
echo StyleRenderer::renderStyles($popupStylesFiltered, 'body div.edd-acp-popup', true);

$btnStyles = array(
    '' => array(
        'text-align'       => $settings->getValue('btnAlignment')
    ),
    'button.button' => array(
        'border-width'     => sprintf('%spx', $btnBorder['width']),
        'border-style'     => sprintf('%s', $btnBorder['style']),
        'border-color'     => sprintf('%s', $btnBorder['color']),
        'border-radius'    => sprintf('%spx', $settings->getValue('btnBorderRadius')),
        'font-size'        => $settings->getValue('fontsize'),
        'padding-top'      => sprintf('%spx', $btnPadding['top']),
        'padding-bottom'   => sprintf('%spx', $btnPadding['bottom']),
        'padding-left'     => sprintf('%spx', $btnPadding['left']),
        'padding-right'    => sprintf('%spx', $btnPadding['right']),
    ),
    'button.edd-acp-checkout-btn' => array(
        'color'            => $settings->getValue('checkoutBtnTextColor'),
        'background'       => $settings->getValue('checkoutBtnBgColor')
    ),
    'button.edd-acp-checkout-btn:hover, button.edd-acp-checkout-btn:focus, button.edd-acp-checkout-btn:active' => array(
        'color'            => $settings->getValue('checkoutBtnHoverTextColor'),
        'background'       => $settings->getValue('checkoutBtnHoverBgColor'),
        'box-shadow'       => '0 0 0 transparent',
    ),
    'button.edd-acp-continue-btn' => array(
        'color'            => $settings->getValue('continueBtnTextColor'),
        'background'       => $settings->getValue('continueBtnBgColor')
    ),
    'button.edd-acp-continue-btn:hover, button.edd-acp-continue-btn:focus, button.edd-acp-continue-btn:active' => array(
        'color'            => $settings->getValue('continueBtnHoverTextColor'),
        'background'       => $settings->getValue('continueBtnHoverBgColor'),
        'box-shadow'       => '0 0 0 transparent',
    ),
);
$btnStylesFiltered = apply_filters('edd_acp_popup_btn_styles', $btnStyles, $settings);
echo StyleRenderer::renderStyles($btnStylesFiltered, 'body div.edd-acp-popup div.edd-acp-button-container', true);

$overlayStyles = array(
    '.b-modal' => array(
        'background-color' => sprintf('%s !important', $settings->getValue('overlay')),
    )
);
$overlayStylesFiltered = apply_filters('edd_acp_overlay_styles', $overlayStyles, $settings);
echo StyleRenderer::renderStyles($overlayStylesFiltered, 'body', true);
?>

<div class="edd-acp-popup">

    <?php do_action('edd_acp_inside_popup_view_top', $viewbag->downloadId, $settings); ?>

    <input type="hidden" class="edd-acp-item-name" value="<?php echo esc_attr($filteredItemName); ?>" />
    <div class="edd-acp-popup-singular">
        <?php
        $singularText = apply_filters('edd_acp_popup_singular_text', $settings->getValue('maintext'),
            $viewbag->downloadId, $settings);
        $singularTextFormatted = sprintf($singularText, '<strong class="item-name"></strong>');
        $filteredSingular = apply_filters('edd_acp_popup_singular_text_formatted', $singularTextFormatted, $viewbag->downloadId,
            $settings);
        echo wpautop(do_shortcode($filteredSingular));
        ?>
    </div>
    <div class="edd-acp-popup-plural">
        <?php
        $pluralText = apply_filters('edd_acp_popup_plural_text', $settings->getValue('pluraltext'),
            $viewbag->downloadId, $settings);
        $pluralTextFormatted = sprintf($pluralText, '<strong class="item-name"></strong>');
        $filteredPlural = apply_filters('edd_acp_popup_plural_text_formatted', $pluralTextFormatted, $viewbag->downloadId, $settings);
        echo wpautop(do_shortcode($filteredPlural));
        ?>
    </div>
    <div class="edd-acp-button-container edd-acp-buttons-<?php echo esc_attr($settings->getValue('btnDirection')) ?>">
        <?php
        // If Checkout button is enabled
        $checkoutBtnRender = '';
        if ((bool)($settings->getValue('showCheckoutBtn'))) {
            // Filter the text
            $checkoutBtnText = apply_filters('edd_acp_popup_checkout_button_text',
                $settings->getValue('checkoutBtnText'),
                $viewbag->downloadId,
                $settings
            );
            $checkoutBtnEscapedText = esc_html($checkoutBtnText);
            $checkoutBtnRender = sprintf('<a href="#" class="edd-acp-goto-checkout"><button class="button edd-acp-checkout-btn">%s</button></a>', $checkoutBtnEscapedText);
        }
        // If Continue Shopping button is enabled
        $continueBtnRender = '';
        if ((bool)($settings->getValue('showContinueBtn'))) {
            // Filter the text
            $continueBtnText = apply_filters('edd_acp_popup_continue_button_text',
                $settings->getValue('continueBtnText'),
                $viewbag->downloadId,
                $settings
            );
            $continueBtnEscapedText = esc_html($continueBtnText);
            $continueBtnRender = sprintf('<button class="button edd-acp-close-popup edd-acp-continue-btn">%s</button>', $continueBtnEscapedText);
        }
        // Check order
        if ($settings->getValue('btnOrder') === 'continue') {
            echo $continueBtnRender;
            echo $checkoutBtnRender;
        } else {
            echo $checkoutBtnRender;
            echo $continueBtnRender;
        }
        ?>
    </div>

<?php do_action('edd_acp_inside_popup_view_bottom', $viewbag->downloadId, $settings); ?>

</div>

<?php

do_action('edd_acp_after_popup_view');

// Unset rendering flag
$this->getPlugin()->getPopup()->setRendering(false);
