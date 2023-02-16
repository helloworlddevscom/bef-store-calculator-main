<?php

namespace ForGravity\Fillable_PDFs\Generator\Field;

use ForGravity\Fillable_PDFs\Generator;
use GFAPI;
use GFCommon;

/**
 * Fillable PDFs PDF Generator field value class.
 *
 * @since     3.0
 * @package   FillablePDFs
 * @author    ForGravity
 * @copyright Copyright (c) 2021, ForGravity
 */
class Base {

	/**
	 * Instance of PDF generator.
	 *
	 * @since 3.0
	 *
	 * @var Generator
	 */
	protected $generator;

	/**
	 * Target field object or field ID.
	 *
	 * @since 3.0
	 *
	 * @var \GF_Field|string
	 */
	protected $field;

	/**
	 * Target field ID.
	 *
	 * @since 3.0
	 *
	 * @var string|int
	 */
	protected $field_id;

	/**
	 * Custom field value.
	 *
	 * @since 3.0
	 *
	 * @var string|null
	 */
	protected $value = null;

	/**
	 * Field modifiers.
	 *
	 * @since 3.0
	 *
	 * @var string[]
	 */
	protected $modifiers = [];

	/**
	 * Initialize Field Value.
	 *
	 * @since 3.0
	 *
	 * @param Generator $generator Instance of the PDF Generator.
	 * @param array     $mapping   Mapping properties.
	 */
	public function __construct( &$generator, $mapping ) {

		if ( $generator instanceof Generator ) {
			$this->generator = $generator;
		}

		$this->field_id  = rgar( $mapping, 'field', null );
		$this->value     = rgar( $mapping, 'value', null );
		$this->modifiers = rgar( $mapping, 'modifiers', [] );

		$field       = GFAPI::get_field( $this->generator->form, $this->field_id );
		$this->field = $field ? $field : $this->field_id;

	}

	/**
	 * Returns the field value.
	 *
	 * @since 3.0
	 *
	 * @return string
	 */
	public function get_value() {

		// For proper Gravity Forms Field objects, return the export value.
		if ( is_a( $this->field, '\GF_Field' ) ) {

			if ( in_array( 'image_choice_fill', $this->modifiers ) ) {
				return $this->get_image_choices_value();
			}

			return $this->field->get_value_export( $this->generator->entry, $this->field_id );

		}

		switch ( strtolower( $this->field ) ) {

			case 'date_created':
				return GFCommon::format_date( $this->generator->entry['date_created'], false, 'Y-m-d H:i:s', false );

			case 'form_title':
				return rgar( $this->generator->entry, 'title' );

			case 'gfchart':
				return $this->get_gfchart_image();

			case 'gf_custom':
				return $this->get_custom_value();

			case 'id':
			case 'ip':
			case 'source_url':
				return rgar( $this->generator->entry, strtolower( $this->field ) );

		}

		return rgar( $this->generator->entry, $this->field );

	}

	/**
	 * Returns the value for Merge Tag mappings.
	 *
	 * @since 3.0
	 *
	 * @return string
	 */
	private function get_custom_value() {

		$value = GFCommon::replace_variables( $this->value, $this->generator->form, $this->generator->entry, false, false, false, 'text' );

		// Process conditional shortcode.
		if ( has_shortcode( $value, 'gravityforms' ) || has_shortcode( $value, 'gravityform' ) ) {
			$value = do_shortcode( $value );
			$value = wp_strip_all_tags( $value );
		}

		return $value;

	}

	/**
	 * Returns the image URL for the embedded GFChart.
	 *
	 * @since 3.0
	 *
	 * @return string|null
	 */
	private function get_gfchart_image() {

		global $gfp_gfchart_image_charts;

		// If GFChart Image Charts plugin is not active, return.
		if ( ! is_object( $gfp_gfchart_image_charts ) ) {
			fg_fillablepdfs()->log_debug( __METHOD__ . '(): GFChart Image Charts plugin is unavailable; skipping field.' );
			return null;
		}

		// Enable image chart generation.
		$gfp_gfchart_image_charts->_doing_notification_message = true;

		// Get image URL from shortcode.
		$shortcode_response = do_shortcode( sprintf( '[gfchart id="%d"]', $this->value ) );
		if ( preg_match( '/<img src="(.*)" style="(.*)" \/>/', $shortcode_response, $matches ) !== 1 ) {
			return null;
		}

		$image_url = rgar( $matches, 1 );

		if ( ! empty( $image_url ) ) {
			$this->modifiers = [ 'image_fill' ];
		}

		return $image_url;

	}

	/**
	 * Returns the image URL for the selected Gravity Forms Image Choice.
	 *
	 * @since 3.0
	 *
	 * @return string|null
	 */
	private function get_image_choices_value() {

		// Image Choice fill modifier is not needed, remove.
		$this->modifiers = array_diff( $this->modifiers, [ 'image_choice_fill' ] );

		$export_value = $this->field->get_value_export( $this->generator->entry, $this->field_id );

		// If Image Choices are not enabled, return selected choice value.
		if ( ! $this->field->imageChoices_enableImages ) {
			return $export_value;
		}

		$selected_choice_value = rgar( $this->generator->entry, $this->field_id );
		$selected_choice       = [];

		foreach ( $this->field['choices'] as $choice ) {
			if ( $choice['value'] === $selected_choice_value ) {
				$selected_choice = $choice;
				break;
			}
		}

		if ( empty( $selected_choice ) ) {
			return $export_value;
		}

		// Get choice image from Media Library, fallback to provided URL.
		$choice_image = rgar( $selected_choice, 'imageChoices_image', false );
		if ( rgar( $selected_choice, 'imageChoices_imageID' ) ) {
			$choice_image_attachment = wp_get_attachment_image_src( $selected_choice['imageChoices_imageID'], 'full' );
			if ( $choice_image_attachment ) {
				$choice_image = rgar( $choice_image_attachment, 0, false );
			}
		}

		if ( ! $choice_image ) {
			return $export_value;
		}

		$this->modifiers = [ 'image_fill' ];

		return $choice_image;

	}





	// # HELPER METHODS ------------------------------------------------------------------------------------------------

	/**
	 * Returns the target field ID.
	 *
	 * @since 3.0
	 *
	 * @return string|int
	 */
	public function get_field_id() {

		return $this->field_id;

	}

	/**
	 * Returns input ID from defined field ID.
	 *
	 * @since 3.0
	 *
	 * @return int
	 */
	protected function get_input_id() {

		$exploded = explode( '.', $this->field_id );

		return (int) end( $exploded );

	}

	/**
	 * Returns the defined modifiers.
	 *
	 * @since 3.0
	 *
	 * @return string[]
	 */
	public function get_modifiers() {

		return $this->modifiers;

	}

}
