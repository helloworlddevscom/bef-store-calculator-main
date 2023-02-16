<?php

namespace Aventura\Edd\AddToCartPopup\Core;

/**
 * Renders styles from arrays.
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
abstract class StyleRenderer
{

    /**
     * Renders a set of styles.
     * 
     * @param array $styles The styles to render. Expected to be an array of "style" arrays with selector keys.
     * @param string $scope The CSS scope; acts as a selector prefix
     * @param string $styleTag If true, a style tag will surround the rendered styles.
     * @return string The rendered result.
     */
    public static function renderStyles(array $styles, $scope = '', $styleTag = false)
    {
        $render = '';
        foreach ($styles as $selector => $rules) {
            $render .= static::renderStyle(sprintf('%s %s', $scope, $selector), $rules);
        }
        return $styleTag
            ? sprintf("<style type='text/css'>\n%s\n</style>", $render)
            : $render;
    }

    /**
     * Renders a style and its rules.
     * 
     * @param string $selector The selector.
     * @param array $rules An array of style rules.
     * @return string The rendered result.
     */
    public static function renderStyle($selector, array $rules)
    {
        $attributes = '';
        foreach ($rules as $attribute => $value) {
            $attributes .= static::renderRule($attribute, $value);
        }
        return sprintf("%s {\n%s}\n", $selector, $attributes);
    }

    /**
     * Renders a single rule.
     * 
     * @param string $attribute The rule attribute.
     * @param string $value The rule value.
     * @return string The rendered result.
     */
    public static function renderRule($attribute, $value)
    {
        return sprintf("\t%s: %s;\n", $attribute, $value);
    }

    /**
     * Converts a hex color string into rgba format.
     *
     * @param string $hex The hex color string.
     * @param float $opacity [optional] The opacity to add to the color. Default: 1.0
     * @return string|boolean The converted color in rgba format or false if the given $hex color string was invalid.
     */
    public static function colorHexToRgba($hex, $opacity = 1.0)
    {
        $rgb = static::hex2RGB($hex, true, ',');
        if (!$rgb) {
            return false;
        }
        return sprintf('rgba(%s,%.2f)', $rgb, $opacity);
    }

    /**
     * Converts a hex string into its RGB equivalent.
     *
     * @param string $hexStr The hex string.
     * @param boolean $returnAsString [optional] Whether to return as a string or not. Default: false
     * @param string $seperator [optional] The separator to use for concatenation if $returnAsString is true. Default: ','
     * @return string|array|boolean An array containing the red, green and blue components if $returnAsString is false,
     * a string containing the red, green and blue components concatenated by the $separator if $returnAsString is true
     * or false if the given $hexStr was not valid.
     */
    public static function hex2RGB($hexStr, $returnAsString = false, $seperator = ',')
    {
        $hexStr = preg_replace("/[^0-9A-Fa-f]/", '', $hexStr);
        $rgbArray = array();
        // If a proper hex code, convert using bitwise operation. No overhead... faster
        if (strlen($hexStr) == 6) {
            $colorVal = hexdec($hexStr);
            $rgbArray['red'] = 0xFF & ($colorVal >> 0x10);
            $rgbArray['green'] = 0xFF & ($colorVal >> 0x8);
            $rgbArray['blue'] = 0xFF & $colorVal;
        // if shorthand notation, need some string manipulations
        } elseif (strlen($hexStr) == 3) {
            $rgbArray['red'] = hexdec(str_repeat(substr($hexStr, 0, 1), 2));
            $rgbArray['green'] = hexdec(str_repeat(substr($hexStr, 1, 1), 2));
            $rgbArray['blue'] = hexdec(str_repeat(substr($hexStr, 2, 1), 2));
        //Invalid hex color code
        } else {
            return false;
        }
        return $returnAsString
            ? implode($seperator, $rgbArray)
            : $rgbArray;
    }

}
