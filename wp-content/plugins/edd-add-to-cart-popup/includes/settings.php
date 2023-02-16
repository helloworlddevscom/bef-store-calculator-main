<?php

use \Aventura\Edd\AddToCartPopup\Core\Settings;

/**
 * Settings HTML rendering static class.
 */
abstract class EddAcpSettingsHtml
{

    /**
     * Renders a generic HTML field.
     * 
     * @param  string $type The type of the field to render. This should translate to a static method for this class.
     * @param  Settings $settings The settings class instance.
     * @param  string $id The ID of the option. Used to get the value to use when rendering the field.
     * @param  boolean $labelBefore If true, the label will be outputted before the field instead of after. Default: false
     * @return string The HTML output.
     */
    public static function renderField($type, $settings, $id, $labelBefore = false)
    {
        // Checks if method for this type exists, and the settings instance has the option with the given id.
        if (!method_exists(__CLASS__, $type) || !$settings->hasOption($id)) {
            return;
        }
        // Begin buffering
        ob_start();
        // Call the static method for the field's type, pasing the ID, option name and option value.
        $fieldRender = self::$type($id, $settings->getSubValueOptionName($id), $settings->getSubValue($id));
        // Get the option description and output a label for the option field.
        $desc = $settings->getOption($id)->desc;
        $fieldLabel = sprintf('<label for="%1$s">%2$s</label>', esc_attr($id), nl2br($desc));
        // Output in the correct order, based on the $labelBefore parameter
        if ($labelBefore) {
            echo $fieldLabel;
            echo $fieldRender;
        } else {
            echo $fieldRender;
            echo $fieldLabel;
        }
        // Return the buffered output
        return ob_get_clean();
    }

    /**
     * Renders a composite field.
     *
     * @param  string $id The field ID.
     * @param  string $name The name attribute of the field.
     * @param  string $value The value of the field.
     * @param  array  $composition The field's properties as an assoc array of property IDs and field types.
     * @return string The HTML output.
     */
    public static function renderCompositeField($id, $name, $value, array $composition = array(), $labels = false)
    {
        ob_start();
        foreach ($composition as $propertyKey => $fieldType) {
            $propertyId = sprintf('%s-%s', $id, $propertyKey);
            $propertName = sprintf('%s[%s]', $name, $propertyKey);
            $propertyValue = (is_array($value) && isset($value[$propertyKey]))
                ? $value[$propertyKey]
                : '';
            echo static::$fieldType($propertyId, $propertName, $propertyValue);
            if (is_array($labels) && isset($labels[$propertyKey])) {
                printf('<label for="%s">%s</label>', $propertyId, $labels[$propertyKey]);
                echo '<br/>';
            }
        }
        return ob_get_clean();
    }

    /**
     * Renders a regular text field.
     * 
     * @param  string $id The field ID.
     * @param  string $name The name attribute of the field.
     * @param  string $value The value of the field.
     * @return string The HTML output.
     */
    public static function text($id, $name, $value)
    {
        ob_start();
        ?>
        <input type="text"
               class="regular-text"
               id="<?php echo esc_attr($id); ?>"
               name="<?php echo esc_attr($name); ?>"
               value="<?php echo esc_attr($value); ?>"
               />
        <br/>
        <?php
        return ob_get_clean();
    }

    /**
     * Renders a small text field.
     * 
     * @param  string $id The field ID.
     * @param  string $name The name attribute of the field.
     * @param  string $value The value of the field.
     * @return string The HTML output.
     */
    public static function smalltext($id, $name, $value)
    {
        ob_start();
        ?>
        <input type="text"
               class="small-text"
               id="<?php echo esc_attr($id); ?>"
               name="<?php echo esc_attr($name); ?>"
               value="<?php echo esc_attr($value); ?>"
               />
        <?php
        return ob_get_clean();
    }

    /**
     * Renders a dropdown select element.
     *
     * @param  string $id The field ID.
     * @param  string $name The name attribute of the field.
     * @param  string $value The value of the field.
     * @param  array  $options The options as an assoc. array of value and label pairs.
     * @return string The HTML output.
     */
    public static function select($id, $name, $value, array $options = array())
    {
        ob_start();
        ?>
        <select id="<?php echo esc_attr($id); ?>"
                name="<?php echo esc_attr($name); ?>"
                >
            <?php foreach ($options as $option => $label): ?>
            <option
                value="<?php echo esc_attr($option); ?>"
                <?php selected($option, $value); ?>
                >
                <?php echo $label; ?>
            </option>
            <?php endforeach; ?>
        </select>
        <?php
        return ob_get_clean();
    }

    /**
     * Renders a number field.
     * 
     * @param  string $id The field ID.
     * @param  string $name The name attribute of the field.
     * @param  string $value The value of the field.
     * @return string The HTML output.
     */
    public static function number($id, $name, $value)
    {
        ob_start();
        ?>
        <input type="number"
               class="small-text"
               id="<?php echo esc_attr($id); ?>"
               name="<?php echo esc_attr($name); ?>"
               value="<?php echo esc_attr($value); ?>"
               min="0"
               />
        <?php
        return ob_get_clean();
    }

    /**
     * Renders a number field suitable for pixel values.
     * 
     * @param  string $id The field ID.
     * @param  string $name The name attribute of the field.
     * @param  string $value The value of the field.
     * @return string The HTML output.
     */
    public static function numberPx($id, $name, $value)
    {
        ob_start();
        echo static::number($id, $name, $value);
        ?>
        <label>px &nbsp;</label>
        <?php
        return ob_get_clean();
    }

    /**
     * Renders a WP Editor field.
     * 
     * @param string $id The field ID.
     * @param string $name The name attribute of the field.
     * @param string $value The value of the field.
     * @param array $args [optional] Array of arguments to pass to the wp_editor() function.
     * @return string The HTML output.
     */
    public static function editor($id, $name, $value, $args = array())
    {
        ob_start();
        $defaults = array(
            'textarea_rows' => 5
        );
        $settings = wp_parse_args($args, $defaults);
        $settings['textarea_name'] = $name;
        echo '<hr/>';
        wp_editor($value, $id, $settings);
        return ob_get_clean();
    }

    /**
     * Renders a colorpicker field.
     * 
     * @param  string $id The field ID.
     * @param  string $name The name attribute of the field.
     * @param  string $value The value of the field.
     * @return string The HTML output.
     */
    public static function colorpicker($id, $name, $value)
    {
        ob_start();
        ?>
        <div class="edd-acp-colorpicker">
            <input type="hidden"
                   class="edd-acp-colorpicker-value"
                   id="<?php echo esc_attr($id); ?>"
                   name="<?php echo esc_attr($name); ?>"
                   value="<?php echo esc_attr($value); ?>" 
                   />
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Renders a checkbox field.
     * 
     * @param  string $id The field ID.
     * @param  string $name The name attribute of the field.
     * @param  string $value The value of the field.
     * @return string The HTML output.
     */
    public static function checkbox($id, $name, $value)
    {
        ob_start();
        ?>
        <input type="hidden" name="<?php echo esc_attr($name); ?>" value="0" />
        <input type="checkbox"
               id="<?php echo esc_attr($id); ?>"
               name="<?php echo esc_attr($name); ?>"
               <?php checked($value, '1'); ?>
               value="1"
               />
        <?php
        return ob_get_clean();
    }

    /**
     * Renders a number field that is more suitable for opacity values.
     *
     * @param  string $id The field ID.
     * @param  string $name The name attribute of the field.
     * @param  string $value The value of the field.
     * @return string The HTML output.
     */
    public static function opacity($id, $name, $value)
    {
        ob_start();
        ?>
        <input type="number"
               class="small-text"
               id="<?php echo esc_attr($id); ?>"
               name="<?php echo esc_attr($name); ?>"
               value="<?php echo esc_attr($value); ?>"
               min="0"
               max="1"
               step="0.05"
               />
        <?php
        return ob_get_clean();
    }

    /**
     * Renders a composite field for border properties.
     *
     * @param  string $id The field ID.
     * @param  string $name The name attribute of the field.
     * @param  string $value The value of the field.
     * @return string The HTML output.
     */
    public static function border($id, $name, $value)
    {
        ob_start();
        echo static::renderCompositeField($id, $name, $value, array(
            'width' => 'numberPx',
            'style' => 'borderStyle',
            'color' => 'colorpicker'
        ));
        return ob_get_clean();
    }

    /**
     * Renders a border style select element.
     * 
     * @staticvar type $borderStyles
     * @param  string $id The field ID.
     * @param  string $name The name attribute of the field.
     * @param  string $value The value of the field.
     * @return string The HTML output.
     */
    public static function borderStyle($id, $name, $value)
    {
        $options = array('none', 'solid', 'dashed', 'dotted', 'double', 'groove', 'ridge', 'inset', 'outset');
        return static::select($id, $name, $value, array_combine($options, $options));
    }

    /**
     * Renders a composite field for box shadow properties.
     * 
     * @param  string $id The field ID.
     * @param  string $name The name attribute of the field.
     * @param  string $value The value of the field.
     * @return string The HTML output.
     */
    public static function shadow($id, $name, $value)
    {
        ob_start();
        echo static::renderCompositeField($id, $name, $value, array(
            'amount' => 'numberPx',
            'color'  => 'colorpicker'
        ));
        return ob_get_clean();
    }

    /**
     * Renders a dropdown select element with direction options.
     * 
     * @param  string $id The field ID.
     * @param  string $name The name attribute of the field.
     * @param  string $value The value of the field.
     * @return string The HTML output.
     */
    public static function direction($id, $name, $value)
    {
        $directions = array('horizontal', 'vertical');
        return static::select($id, $name, $value, array_combine($directions, $directions));
    }

    /**
     * Renders a dropdown select element with alignment options.
     *
     * @param  string $id The field ID.
     * @param  string $name The name attribute of the field.
     * @param  string $value The value of the field.
     * @return string The HTML output.
     */
    public static function alignment($id, $name, $value)
    {
        $alignments = array('left', 'center', 'right');
        return static::select($id, $name, $value, array_combine($alignments, $alignments));
    }

    /**
     * Renders a dropdown select element with button order options.
     *
     * @param  string $id The field ID.
     * @param  string $name The name attribute of the field.
     * @param  string $value The value of the field.
     * @return string The HTML output.
     */
    public static function btnOrder($id, $name, $value)
    {
        $orders = array(
            'checkout' => __('Checkout button first', 'edd_acp'),
            'continue' => __('Continue buttton first', 'edd_acp'),
        );
        return static::select($id, $name, $value, $orders);
    }

    /**
     * Renders an HTML button.
     *
     * @param string $id The button ID.
     * @param string $text The button text.
     * @param string $class The HTML class attribute.
     * @return string The rendered HTML.
     */
    public static function button($id, $text, $class)
    {
        ob_start();
        ?>
        <button
            id="<?php echo esc_attr($id); ?>"
            class="<?php echo esc_attr($class); ?>"
            type="button">
            <?php echo $text; ?>
        </button>
        <?php
        return ob_get_clean();
    }

    /**
     * Renders a set of input fields for width and height.
     *
     * @param string $id The button ID.
     * @param  string $name The name attribute of the field.
     * @param  string $value The value of the field.
     * @return string The rendered HTML.
     */
    public static function size($id, $name, $value)
    {
        ob_start();
        echo static::renderCompositeField($id, $name, $value, array(
            'width'  => 'smalltext',
            'height' => 'smalltext',
        ), array(
            'width'  => __('Width', 'edd_acp'),
            'height' => __('Height', 'edd_acp'),
        ));
        return ob_get_clean();
    }

    /**
     * Renders a set of input fields for padding.
     *
     * @param string $id The button ID.
     * @param  string $name The name attribute of the field.
     * @param  string $value The value of the field.
     * @return string The rendered HTML.
     */
    public static function padding($id, $name, $value)
    {
        ob_start();
        echo static::renderCompositeField($id, $name, $value, array(
            'top'    => 'numberPx',
            'bottom' => 'numberPx',
            'left'   => 'numberPx',
            'right'  => 'numberPx',
        ), array(
            'top'    => __('Top', 'edd_acp'),
            'bottom' => __('Bottom', 'edd_acp'),
            'left'   => __('Left', 'edd_acp'),
            'right'  => __('Right', 'edd_acp'),
        ));
        return ob_get_clean();
    }

    /**
     * Renders a Preview button inside a fake EDD purchase form.
     *
     * The preview button will trigger a popup for viewing purposes.
     *
     * @return string The HTML render.
     */
    public static function renderPreview()
    {
        ob_start();
        ?>
        <div class="edd-acp-fake-purchase-form">
            <input type="hidden" class="edd_action_input" value="add_to_cart" />
            <input type="hidden" class="edd-item-quantity" value="1" />
            <div class="edd_purchase_submit_wrapper">
                <p>
                    <label>
                        <?php
                            echo EddAcpSettingsHtml::button(
                                'edd-acp-preview',
                                __('Preview', 'edd_acp'),
                                'button button-secondary edd-add-to-cart edd-acp-preview'
                            );
                        ?>
                    </label>
                </p>
                <p>
                    <?php _e('Click the button to preview your settings before saving.', 'edd_acp'); ?>
                    <br/>
                    <?php _e('You can also use the "Preview Popup" button on the admin bar.', 'edd_acp'); ?>
                </p>
            </div>
            <div id="edd-acp-preview-popup-container"></div>
        </div>
        <?php
        return ob_get_clean();
    }

}

// Get text domain
$textDomain = edd_acp()->getTextDomain()->getName();

/**
 * Registers the plugin options to a settings instance.
 *
 * @param Settings $settings The settings instance to which to register the options.
 * @return Settings $settings The settings instance.
 */
function eddAcpRegisterOptions(Settings $settings)
{
    $settings
        ->addOption(
            'enabled', __('Enable Popup', 'edd_acp'),
            __('Tick this box to enable the popup. Untick it to disable it.', 'edd_acp'), '0',
            function($settings, $id, $args)
            {
                echo EddAcpSettingsHtml::renderField('checkbox', $settings, $id);
            }
        )
        ->addOption(
            'preview', __('Preview Popup', 'edd_acp'),
            __('', 'edd_acp'),
            __('Click the button to preview your settings before saving.', 'edd_acp'),
            function ($settings, $id, $args)
            {
                echo EddAcpSettingsHtml::renderPreview();
            }
        )
        ->addOption(
            'maintext', __('Popup Text', 'edd_acp'),
            sprintf(__("The text shown on the popup.\nThe \"%%s\" will be replaced by the name of the item added to the cart.\n%s", 'edd_acp'), eddAcpExplainShortcodes()),
            __('%s has been added to your cart!', 'edd_acp'),
            function($settings, $id, $args)
            {
                echo EddAcpSettingsHtml::renderField('editor', $settings, $id, true);
            }
        )
        ->addOption(
            'pluraltext', __('Popup Plural Text', 'edd_acp'),
            sprintf(__("The text shown on the popup when multiple items have been added to the cart.\n The \"%%s\" will be replaced with a comma separated list of the names of the added items.\n%s", 'edd_acp'), eddAcpExplainShortcodes()),
            __('%s have been added to your cart!', 'edd_acp'),
            function($settings, $id, $args)
            {
                echo EddAcpSettingsHtml::renderField('editor', $settings, $id, true);
            }
        )
        ->addOption(
            'fontsize', __('Font Size', 'edd_acp'),
            __('The font size for the popup text, in one of these formats: 10px, 2em, 50%. Leave empty to use the theme default font size.'),
            '',
            function($settings, $id, $args)
            {
                echo EddAcpSettingsHtml::renderField('smalltext', $settings, $id);
            }
        )
        ->addOption(
            'textcolor', __('Text Color', 'edd_acp'),
            __('Change the color of the text inside the popup box.', 'edd_acp'),
            'rgb(0, 0, 0)',
            function($settings, $id, $args)
            {
                echo EddAcpSettingsHtml::renderField('colorpicker', $settings, $id);
            }
        )
        ->addOption(
            'bgcolor', __('Background Color', 'edd_acp'),
            __('Change the background color of the popup box.', 'edd_acp'),
            'rgb(255, 255, 255)',
            function($settings, $id, $args)
            {
                echo EddAcpSettingsHtml::renderField('colorpicker', $settings, $id);
            }
        )
        ->addOption(
            'border', __('Border', 'edd_acp'),
            __('The border thickness, style and color respectively.', 'edd_acp'),
            array(
                'width' => '0',
                'style' => 'solid',
                'color' => 'rgb(0, 0, 0)'
            ),
            function ($settings, $id, $args)
            {
                echo EddAcpSettingsHtml::renderField('border', $settings, $id);
            }
        )
        ->addOption(
            'borderRadius', __('Border Radius', 'edd_acp'),
            __('The radius of the popup border, in pixels. The radius will still apply even if no border is visible.', 'edd_acp'),
            '0',
            function ($settings, $id, $args)
            {
                echo EddAcpSettingsHtml::renderField('number', $settings, $id);
            }
        )
        ->addOption(
            'shadow', __('Shadow', 'edd_acp'),
            __('The shadow amount and color, respectively.', 'edd_acp'),
            array(
                'amount'  => '0',
                'color'   => 'rgb(0, 0, 0)'
            ),
            function ($settings, $id, $args)
            {
                echo EddAcpSettingsHtml::renderField('shadow', $settings, $id);
            }
        )
        ->addOption('overlay', __('Overlay Color', 'edd_acp'),
            __('The color of the overlay that covers the page when the popup is shown.', 'edd_acp'),
            'rgba(0, 0, 0, 0.7)',
            function($settings, $id, $args)
            {
                echo EddAcpSettingsHtml::renderField('colorpicker', $settings, $id);
            }
        )

        /**
         * Options for popup size.
         */
        ->addOption('popupSizeHeader', __('Popup Dimensions', 'edd_acp'))
        ->addOption('size', __('Size', 'edd_acp'),
            __('The width and height for the popup, in pixels (px), percentages (%) or other accepted CSS formats. Leave blank for auto-sizing.', 'edd_acp'),
            array(
                'width'  => '',
                'height' => '',
            ),
            function($settings, $id, $args)
            {
                echo EddAcpSettingsHtml::renderField('size', $settings, $id);
            }
        )
        ->addOption('padding', __('Padding', 'edd_acp'),
            __('The spacing between the popup border and its content, in pixels.', 'edd_acp'),
            array(
                'top'    => '20',
                'bottom' => '20',
                'left'   => '25',
                'right'  => '25',
            ),
            function($settings, $id, $args)
            {
                echo EddAcpSettingsHtml::renderField('padding', $settings, $id);
            }
        )

        /**
         * Options for generic button styles
         */
        ->addOption('btnStylesHeader', __('Button Styles', 'edd_acp'))
        ->addOption(
            'btnBorder', __('Button Border', 'edd_acp'),
            __('The button border CSS rule. Example: "1px solid #000". Leave empty to use the theme defaults.'),
            array(
                'width' => '1',
                'style' => 'solid',
                'color' => '#000'
            ),
            function($settings, $id, $args)
            {
                echo EddAcpSettingsHtml::renderField('border', $settings, $id);
            }
        )
        ->addOption(
            'btnBorderRadius', __('Corner Radius', 'edd_acp'),
            __('The radius of the corners, in pixels. For example: "2", "5", etc. Leave empty to use the theme default.', 'edd_acp'),
            '2',
            function($settings, $id, $args)
            {
                echo EddAcpSettingsHtml::renderField('number', $settings, $id);
            }
        )
        ->addOption(
            'btnPadding', __('Button Padding', 'edd_acp'),
            __('The inner padding for the popup buttons, in pixels. Leave empty to use the theme defaults.', 'edd_acp'),
            array(
                'top'    => '8',
                'bottom' => '8',
                'left'   => '15',
                'right'  => '15',
            ),
            function($settings, $id, $args)
            {
                echo EddAcpSettingsHtml::renderField('padding', $settings, $id);
            }
        )
        ->addOption(
            'btnDirection', __('Button Direction', 'edd_acp'),
            __('Horizontal will place the buttons side by side while vertical will put them on top of each other.', 'edd_acp'),
            'horizontal',
            function ($settings, $id, $args)
            {
                echo EddAcpSettingsHtml::renderField('direction', $settings, $id);
            }
        )
        ->addOption('btnAlignment', __('Button Alignment', 'edd_acp'),
            __('The alignment for the buttons. Does not apply if the buttons are placed vertically.', 'edd_acp'),
            'left',
            function ($settings, $id, $args)
            {
                echo EddAcpSettingsHtml::renderField('alignment', $settings, $id);
            }
        )
        ->addOption('btnOrder', __('Button Order', 'edd_acp'),
            __('Choose which button to display first.', 'edd_acp'),
            'checkout',
            function($settings, $id, $args)
            {
                echo EddAcpSettingsHtml::renderField('btnOrder', $settings , $id);
            }
        )

        /**
         * Options for the Checkout Button
         */
        ->addOption('checkoutBtnHeader', __('Checkout Button', 'edd_acp'))
        ->addOption(
            'showCheckoutBtn',
            __('Enabled', 'edd_acp'),
            __('Tick to show the Checkout button.', 'edd_acp'),
            '1',
            function($settings, $id, $args)
            {
                echo EddAcpSettingsHtml::renderField('checkbox', $settings, $id);
            }
        )
        ->addOption(
            'checkoutBtnText',
            __('Text', 'edd_acp'),
            __('The text of the Checkout button.', 'edd_acp'),
            __('Proceed to Checkout', 'edd_acp'),
            function($settings, $id, $args)
            {
                echo EddAcpSettingsHtml::renderField('text', $settings, $id);
            }
        )
        ->addOption(
            'checkoutBtnTextColor',
            __('Text Color', 'edd_acp'),
            __('The text color for the Checkout button.', 'edd_acp'),
            'rgb(255, 255, 255)',
            function($settings, $id, $args)
            {
                echo EddAcpSettingsHtml::renderField('colorpicker', $settings, $id);
            }
        )
        ->addOption(
            'checkoutBtnHoverTextColor',
            __('Text Color on Hover', 'edd_acp'),
            __('The text color for the Checkout button when hovered over.', 'edd_acp'),
            'rgb(255, 255, 255)',
            function($settings, $id, $args)
            {
                echo EddAcpSettingsHtml::renderField('colorpicker', $settings, $id);
            }
        )
        ->addOption(
            'checkoutBtnBgColor',
            __('Background Color', 'edd_acp'),
            __('The background color for the Checkout button.', 'edd_acp'),
            'rgb(68, 68, 68)',
            function($settings, $id, $args)
            {
                echo EddAcpSettingsHtml::renderField('colorpicker', $settings, $id);
            }
        )
        ->addOption(
            'checkoutBtnHoverBgColor',
            __('Background Color on Hover', 'edd_acp'),
            __('The background color for the Checkout button when hovered over.', 'edd_acp'),
            'rgb(68, 68, 68)',
            function($settings, $id, $args)
            {
                echo EddAcpSettingsHtml::renderField('colorpicker', $settings, $id);
            }
        )

        /**
         * Options for the Continue Shopping Button
         */
        ->addOption('continueBtnHeader', __('Continue Button', 'edd_acp'))
        ->addOption(
            'showContinueBtn',
            __('Enabled', 'edd_acp'),
            __('Tick to show the Continue Shopping button.', 'edd_acp'),
            '1',
            function($settings, $id, $args)
            {
                echo EddAcpSettingsHtml::renderField('checkbox', $settings, $id);
            }
        )
        ->addOption(
            'continueBtnText',
            __('Text', 'edd_acp'),
            __('The text of the continue shopping button.', 'edd_acp'),
            __('Continue shopping', 'edd_acp'),
            function($settings, $id, $args)
            {
                echo EddAcpSettingsHtml::renderField('text', $settings, $id);
            }
        )
        ->addOption(
            'continueBtnTextColor',
            __('Text Color', 'edd_acp'),
            __('The text color for the Continue Shopping button.', 'edd_acp'),
            'rgb(255, 255, 255)',
            function($settings, $id, $args)
            {
                echo EddAcpSettingsHtml::renderField('colorpicker', $settings, $id);
            }
        )
        ->addOption(
            'continueBtnHoverTextColor',
            __('Text Color on Hover', 'edd_acp'),
            __('The text color for the Continue Shopping button when hovered over.', 'edd_acp'),
            'rgb(255, 255, 255)',
            function($settings, $id, $args)
            {
                echo EddAcpSettingsHtml::renderField('colorpicker', $settings, $id);
            }
        )
        ->addOption(
            'continueBtnBgColor',
            __('Background Color', 'edd_acp'),
            __('The background color for the Continue Shopping button.', 'edd_acp'),
            'rgb(68, 68, 68)',
            function($settings, $id, $args)
            {
                echo EddAcpSettingsHtml::renderField('colorpicker', $settings, $id);
            }
        )
        ->addOption(
            'continueBtnHoverBgColor',
            __('Background Color on Hover', 'edd_acp'),
            __('The background color for the Continue Shopping button when hovered over.', 'edd_acp'),
            'rgb(68, 68, 68)',
            function($settings, $id, $args)
            {
                echo EddAcpSettingsHtml::renderField('colorpicker', $settings, $id);
            }
        )

        /**
         * Misc.
         */
        ->addOption('miscHeader', __('Misc Options', 'edd_acp'))
        ->addOption(
            'resetOptions',
            __('Reset Options', 'edd_acp'),
            __('Click this button to reset all your popup settings back to default. Note: this cannot be undone.', 'edd_acp'),
            '0',
            function($settings, $id, $args)
            {
                $name = esc_attr($settings->getSubValueOptionName('reset'));
                ?>
                <p>
                    <label>
                        <input type="submit"
                               id="edd-acp-reset"
                               name="<?php echo $name; ?>"
                               value="<?php _e('Reset Options'); ?>"
                               class="button button-secondary"
                               />
                        <?php echo $args['desc']; ?>
                    </label>
                </p>
                <?php
            }
        )
    ;
    return $settings;
}

// Register settings to the singleton's settings
eddAcpRegisterOptions(edd_acp()->getSettings());

/**
 * Explains the shortcode-preview problem.
 *
 * @return string
 */
function eddAcpExplainShortcodes()
{
    return sprintf(
        __('Shortcodes are also supported! Note that although they might not be displayed correctly in the Preview, they will be displayed correctly on the site, as explained <a %s>here</a>.', 'edd_acp'),
        sprintf('href="%s" target="edd-acp-docs"', EDD_ACP_DOCS_URL)
    );
}
